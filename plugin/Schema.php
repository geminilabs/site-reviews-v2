<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     2.3.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use DateTime;
use GeminiLabs\SchemaOrg\Review as ReviewSchema;
use GeminiLabs\SchemaOrg\Schema as SchemaOrg;
use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Rating;
use WP_Post;

class Schema
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var Database
	 */
	protected $db;

	/**
	 * @var Rating
	 */
	protected $rating;

	/**
	 * @var array
	 */
	protected $reviews;

	public function __construct( App $app, Database $db, Rating $rating )
	{
		$this->app = $app;
		$this->db = $db;
		$this->rating = $rating;
	}

	/**
	 * @return array
	 */
	public function build( array $args = [] )
	{
		$this->args = $args;
		$schema = $this->buildSummary( $args );
		$reviews = [];
		foreach( $this->db->getReviews( $this->args )->reviews as $review ) {
			$reviews[] = $this->buildReview( $review );
		}
		if( !empty( $reviews )) {
			array_walk( $reviews, function( &$review ) {
				unset( $review['@context'] );
				unset( $review['itemReviewed'] );
			});
			$schema['review'] = $reviews;
		}
		return $schema;
	}

	/**
	 * @param object $review
	 * @return array
	 */
	public function buildReview( $review )
	{
		$schema = SchemaOrg::Review()
			->doIf( !in_array( 'title', $this->args['hide'] ), function( ReviewSchema $schema ) use( $review ) {
				$schema->name( $review->title );
			})
			->doIf( !in_array( 'excerpt', $this->args['hide'] ), function( ReviewSchema $schema ) use( $review ) {
				$schema->reviewBody( $review->content );
			})
			->datePublished(( new DateTime( $review->date ))->format( DateTime::ISO8601 ))
			->itemReviewed( $this->getSchemaType()
				->name( $this->getThingName() )
			)
			->author( SchemaOrg::Person()
				->name( $review->author )
			);
		if( !empty( $review->rating )) {
			$schema->reviewRating( SchemaOrg::Rating()
				->ratingValue( $review->rating )
				->bestRating( Rating::MAX_RATING )
				->worstRating( Rating::MIN_RATING )
			);
		}
		return apply_filters( 'site-reviews/schema/Review', $schema->toArray(), $review, $this->args );
	}

	/**
	 * @param null|array $args
	 * @return array
	 */
	public function buildSummary( $args = null )
	{
		if( is_array( $args )) {
			$this->args = $args;
		}
		$schema = $this->getSchemaType()
			->doIf( $this->getSchemaOption( 'type' ) == 'Product', function( $schema ) {
				$schema->setProperty( '@id', $this->getThingUrl() );
			})
			->name( $this->getThingName() )
			->description( $this->getThingDescription() )
			->image( $this->getThingImage() )
			->url( $this->getThingUrl() );
		$count = $this->getReviewCount();
		if( !empty( $count )) {
			$schema->aggregateRating( SchemaOrg::AggregateRating()
				->ratingValue( $this->getRatingValue() )
				->reviewCount( $count )
				->bestRating( Rating::MAX_RATING )
				->worstRating( Rating::MIN_RATING )
			);
		}
		$schema = $schema->toArray();
		$args = wp_parse_args( ['count' => -1], $this->args );
		return apply_filters( sprintf( 'site-reviews/schema/%s', $schema['@type'] ), $schema, $args );
	}

	/**
	 * @return null|string
	 */
	public function render()
	{
		if( is_null( $this->app->schemas ))return;
		return sprintf( '<script type="application/ld+json">%s</script>', json_encode(
			apply_filters( 'site-reviews/schema/all', $this->app->schemas ),
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		));
	}

	/**
	 * @return void
	 */
	public function store( array $schema )
	{
		$schemas = (array) $this->app->schemas;
		$schemas[] = $schema;
		$this->app->schemas = array_map( 'unserialize', array_unique( array_map( 'serialize', $schemas )));
	}

	/**
	 * @return int|float
	 */
	protected function getRatingValue()
	{
		return $this->rating->getAverage( $this->getReviews() );
	}

	/**
	 * @return int
	 */
	protected function getReviewCount()
	{
		return count( $this->getReviews() );
	}

	/**
	 * Get all reviews possible for given args
	 * @return array
	 */
	protected function getReviews( $force = false )
	{
		if( !is_array( $this->reviews ) || $force ) {
			$this->reviews = $this->db->getReviews( wp_parse_args( ['count' => -1], $this->args ))->reviews;
		}
		return $this->reviews;
	}

	/**
	 * @param string $option
	 * @param string $fallback
	 * @return string
	 */
	protected function getSchemaOption( $option, $fallback = '' )
	{
		if( $schemaOption = trim( (string) get_post_meta( (int) get_the_ID(), sprintf( 'schema_%s', $option ), true ))) {
			return $schemaOption;
		}
		$path = 'settings.reviews.schema.%s.%s';
		$default = $this->db->getOption( sprintf( $path, $option, 'default' ), $fallback );
		return $default == 'custom'
			? $this->db->getOption( sprintf( $path, $option, 'custom' ), $fallback )
			: $default;
	}

	/**
	 * @param string $option
	 * @param string $fallback
	 * @return null|string
	 */
	protected function getSchemaOptionValue( $option, $fallback = 'post' )
	{
		$value = $this->getSchemaOption( $option, $fallback );
		if( $value != $fallback ) {
			return $value;
		}
		if( !is_single() && !is_page() )return;
		switch( $option ) {
			case 'description':
				$post = get_post();
				if( !( $post instanceof WP_Post )) {
					return '';
				}
				$text = strip_shortcodes( wp_strip_all_tags( $post->post_excerpt ));
				return wp_trim_words( $text, apply_filters( 'excerpt_length', 55 ));
			case 'image':
				return (string)get_the_post_thumbnail_url( null, 'large' );
			case 'name':
				return get_the_title();
			case 'url':
				return (string)get_the_permalink();
		}
	}

	/**
	 * @return \GeminiLabs\SchemaOrg\Type
	 */
	protected function getSchemaType()
	{
		$type = $this->getSchemaOption( 'type', 'LocalBusiness' );
		return SchemaOrg::$type( $type );
	}

	/**
	 * @return null|string
	 */
	protected function getThingDescription()
	{
		return $this->getSchemaOptionValue( 'description' );
	}

	/**
	 * @return null|string
	 */
	protected function getThingImage()
	{
		return $this->getSchemaOptionValue( 'image' );
	}

	/**
	 * @return null|string
	 */
	protected function getThingName()
	{
		return $this->getSchemaOptionValue( 'name' );
	}

	/**
	 * @return null|string
	 */
	protected function getThingUrl()
	{
		return $this->getSchemaOptionValue( 'url' );
	}
}
