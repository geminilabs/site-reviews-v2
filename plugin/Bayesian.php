<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2017, Paul Ryley
 * @license   GPLv3
 * @since     2.3.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

/**
 * The quality of a 5 star rating depends not only on the average number of stars but also on the number of reviews.
 */
class Bayesian
{
	/**
	 * Z scores for confidence percentage intervals (credible interval)
	 * @var array
	 */
	const Z_SCORE_CONFIDENCE_PERCENTAGE_INTERVALS = [
		50     => 0.67449,
		70     => 1.04,
		75     => 1.15035,
		80     => 1.282,
		85     => 1.44,
		90     => 1.64485,
		92     => 1.75,
		95     => 1.95996,
		96     => 2.05,
		97     => 2.17009,
		98     => 2.326,
		99     => 2.57583,
		'99.5' => 2.81,
		'99.8' => 3.08,
		'99.9' => 3.29053,
	];

	/**
	 * @return array
	 */
	public function getRatingCounts( array $reviews )
	{
		$counts = array_fill_keys( [5,4,3,2,1], [] );
		array_walk( $counts, function( &$count, $key ) use( $reviews ) {
			$count = count( array_filter( $reviews, function( $review ) use( $key ) {
				if( !isset( $review->rating ))return;
				return $review->rating == $key;
			}));
		});
		return $counts;
	}

	/**
	 * Get the lower bound for up/down ratings
	 * Method receives an up/down ratings array: [1, -1, -1, 1, 1, -1]
	 * @see http://www.evanmiller.org/how-not-to-sort-by-average-rating.html
	 * @return int|float
	 */
	public function getLowerBound( array $upDownRatings, $confidencePercentage = 95 )
	{
		$numRatings = count( $upDownRatings );
		if( !$numRatings )return 0;
		$positiveRatings = count( array_filter( $upDownRatings, function( $value ) {
			return $value > 0;
		}));
		$z = static::Z_SCORE_CONFIDENCE_PERCENTAGE_INTERVALS[$confidencePercentage];
		$phat = 1 * $positiveRatings / $numRatings;
		return ($phat + $z * $z / (2 * $numRatings) - $z * sqrt(($phat * (1 - $phat) + $z * $z / (4 * $numRatings)) / $numRatings))/(1 + $z * $z / $numRatings);
	}

	/**
	 * Calculate the ranking of a page by its number of reviews and their rating
	 * Method receives an array of rating counts: [5=>?, 4=>?, 3=>?, 2=>?, 1=>?]
	 * @see http://www.evanmiller.org/ranking-items-with-star-ratings.html
	 * @return float
	 */
	public function getRanking( array $ratingCounts )
	{
		$ratingCountsSum = array_sum( $ratingCounts ) + count( $ratingCounts );
		$weight = $this->getWeight( $ratingCounts, $ratingCountsSum );
		$weightPow2 = $this->getWeight( $ratingCounts, $ratingCountsSum, true );
		$zScore = static::Z_SCORE_CONFIDENCE_PERCENTAGE_INTERVALS[90];
		return $weight - $zScore * sqrt(( $weightPow2 - $weight**2 ) / ( $ratingCountsSum + 1 ));
	}

	/**
	 * Get the average rating for an array of reviews
	 * @return int|float
	 */
	public function getRatingAverage( array $reviews )
	{
		$ratingSum = array_reduce( $reviews, function( $sum, $review ) {
			return $sum + intval( $review->rating );
		});
		return ( $ratingCount = count( $reviews ))
			? round( $ratingSum / $ratingCount, 4 )
			: 0;
	}

	/**
	 * Get the bayesian average rating for an array of reviews
	 * @see https://www.xkcd.com/937/
	 * @see https://districtdatalabs.silvrback.com/computing-a-bayesian-estimate-of-star-rating-means
	 * @see http://fulmicoton.com/posts/bayesian_rating/
	 * @return float
	 */
	public function getRatingAverageBayesian( array $reviews ) {
		// represents the number of ratings that we expect are needed to begin observing a pattern that would put confidence in the prior
		// could also be the total number of reviews of all items.
		$CONFIDENCE = 7;
		// represents a prior for the average of stars
		// could also be the average score of all items instead of a fixed value
		$PRIOR = 5;
		$numberOfReviews = count( $reviews );
		$avgRating = $this->getRatingAverage( $reviews );
		$result = $avgRating > 0
			? (( $CONFIDENCE * $PRIOR ) + ( $avgRating * $numberOfReviews )) / ( $CONFIDENCE + $numberOfReviews )
			: 0;
		return round( $result, 4 );
	}

	/**
	 * Get the percentage rating for an array of reviews
	 * @param int $highestRating
	 * @return float
	 */
	public function getRatingPercentage( array $reviews, $highestRating = 5 )
	{
		return round( $this->getRatingAverage( $reviews ) * 100 / $highestRating, 2 );
	}

	/**
	 * @param int $ratingCountsSum
	 * @param bool $powerOf2
	 * @return float
	 */
	protected function getWeight( array $ratingCounts, $ratingCountsSum, $powerOf2 = false )
	{
		return array_reduce( array_keys( $ratingCounts ),
			function( $count, $rating ) use( $ratingCounts, $ratingCountsSum, $powerOf2 ) {
				$ratingLevel = $powerOf2 ? $rating**2 : $rating;
				return $count + ( $ratingLevel * ( $ratingCounts[$rating] + 1 )) / $ratingCountsSum;
			}
		);
	}
}
