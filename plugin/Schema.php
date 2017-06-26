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

	public function build( array $args = [] )
	{
		$this->args = $args;
		return $this->graph();
	}

	/**
	 * @return array
	 */
	public function buildReviewSchema()
	{
		return SchemaOrg::Review()
			->name( $this->getReviewName() )
			->reviewBody( $this->getReviewBody() )
			->datePublished( $this->getReviewDatePublished() )
			->author( SchemaOrg::Person()
				->name( $this->getReviewAuthor() )
			)
			->itemReviewed( $this->getSchemaType()
				->name( $this->getThingName() )
			)
			->reviewRating( SchemaOrg::Rating()
				->ratingValue( $this->getReviewRatingValue() )
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
			->photo( $this->getThingPhoto() )
			->url( $this->getThingUrl() )
			->aggregateRating( SchemaOrg::AggregateRating()
				->ratingValue( $this->getRatingValue() )
				->reviewCount( $this->getReviewCount() )
			)
			->toArray();
	}

	public function graph()
	{
	}

	/**
	 * @return int|float
	 */
	protected function getRatingValue()
	{
		return $this->rating->getAverage( $this->reviews );
	}

	/**
	 * @return string
	 */
	protected function getReviewAuthor()
	{}

	/**
	 * @return string
	 */
	protected function getReviewBody()
	{}

	/**
	 * @return int
	 */
	protected function getReviewCount()
	{
		return count( $this->reviews );
	}

	/**
	 * @return string
	 */
	protected function getReviewDatePublished()
	{
		return ( new DateTime( 'now' ))->format( DateTime::ISO8601 );
	}

	/**
	 * @return string
	 */
	protected function getReviewName()
	{}

	/**
	 * @return int
	 */
	protected function getReviewRatingValue()
	{}

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
	{}

	/**
	 * @return string
	 */
	protected function getThingName()
	{}

	/**
	 * @return string
	 */
	protected function getThingPhoto()
	{}

	/**
	 * @return string
	 */
	protected function getThingUrl()
	{}
}
