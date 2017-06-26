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
use GeminiLabs\SchemaOrg\Schema as SchemaOrg;
use GeminiLabs\SchemaOrg\Review as ReviewSchema;
use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Rating;

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
	 * @var array
	 */
	protected $currentReview;

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
	 * @return void
	 */
	public function build( array $args = [] )
	{
		$this->args = $args;
		$schemas = (array) $this->app->schemas;
		foreach( $this->getReviews() as $review ) {
			$schemas[] = $this->buildReviewSchema( $review );
		}
		$schemas[] = $this->buildSummarySchema();
		$this->app->schemas = array_map( 'unserialize', array_unique( array_map( 'serialize', $schemas )));
	}

	/**
	 * @param object $review
	 * @return array
	 */
	public function buildReviewSchema( $review )
	{
		return SchemaOrg::Review()
			->doIf( !in_array( 'title', $this->args['hide'] ), function( ReviewSchema $schema ) use( $review ) {
				$schema->name( $review->title );
			})
			->doIf( !in_array( 'excerpt', $this->args['hide'] ), function( ReviewSchema $schema ) use( $review ) {
				$schema->reviewBody( $review->content );
			})
			->datePublished(( new DateTime( $review->date ))->format( DateTime::ISO8601 ))
			->author( SchemaOrg::Person()
				->name( $review->author )
			)
			->itemReviewed( $this->getSchemaType()
				->name( $this->getThingName() )
			)
			->reviewRating( SchemaOrg::Rating()
				->ratingValue( $review->rating )
				->bestRating( Rating::MAX_RATING )
				->worstRating( Rating::MIN_RATING )
			)
			->toArray();
	}

	/**
	 * @return array
	 */
	public function buildSummarySchema()
	{
		return $this->getSchemaType()
			->name( $this->getThingName() )
			->description( $this->getThingDescription() )
			->image( $this->getThingPhoto() )
			->url( $this->getThingUrl() )
			->aggregateRating( SchemaOrg::AggregateRating()
				->ratingValue( $this->getRatingValue() )
				->reviewCount( $this->getReviewCount() )
			)
			->toArray();
	}

	/**
	 * @return string
	 */
	public function render()
	{
		return sprintf( '<script type="application/ld+json">%s</script>', json_encode( $this->app->schemas ));
	}

	/**
	 * @return int|float
	 */
	protected function getRatingValue()
	{
		return $this->rating->getAverage( $this->reviews );
	}

	/**
	 * @return int
	 */
	protected function getReviewCount()
	{
		return count( $this->reviews );
	}

	/**
	 * @return array
	 */
	protected function getReviews()
	{
		if( !is_array( $this->reviews )) {
			$this->reviews = $this->db->getReviews( $this->args )->reviews;
		}
		return $this->reviews;
	}

	/**
	 * @return string
	 */
	protected function getSchemaType()
	{
		$type = $this->db->getOption( 'settings.review.schema.type', 'LocalBusiness' );
		if( $type == 'custom' ) {
			$type = $this->db->getOption( 'settings.review.schema.custom', 'LocalBusiness' );
		}
		return SchemaOrg::$type( $type );
	}

	/**
	 * @return string
	 */
	protected function getThingDescription()
	{
		return 'THING_DESCRIPTION';
	}

	/**
	 * @return string
	 */
	protected function getThingName()
	{
		return 'THING_NAME';
	}

	/**
	 * @return string
	 */
	protected function getThingPhoto()
	{
		return 'THING_PHOTO';
	}

	/**
	 * @return string
	 */
	protected function getThingUrl()
	{
		return 'THING_URL';
	}
}
