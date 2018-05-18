<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database\OptionManager;

class Blacklist
{
	/**
	 * @return bool
	 */
	public function isBlacklisted( CreateReview $review )
	{
		$target = implode( "\n", array_filter([
			$review->author,
			$review->content,
			$review->email,
			$review->ipAddress,
			$review->title,
		]));
		return (bool) apply_filters( 'site-reviews/blacklist/is-blacklisted',
			$this->check( $target ),
			$review
		);
	}

	/**
	 * @param string $target
	 * @return bool
	 */
	protected function check( $target )
	{
		$blacklist = trim( glsr( OptionManager::class )->get( 'settings.reviews-form.blacklist.entries' ));
		if( empty( $blacklist )) {
			return false;
		}
		$lines = explode( "\n", $blacklist );
		foreach( (array) $lines as $line ) {
			$line = trim( $line );
			if( empty( $line ) || 256 < strlen( $line ))continue;
			$pattern = sprintf( '#%s#i', preg_quote( $line, '#' ));
			if( preg_match( $pattern, $target )) {
				return true;
			}
		}
		return false;
	}
}