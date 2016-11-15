<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv2 or later
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Partials;

use GeminiLabs\SiteReviews\Html\Partials\Base;

class Reviews extends Base
{
	/**
	 * Generate a review
	 *
	 * @return string
	 */
	public function render()
	{
		$defaults = [
			'class'       => '',
			'display'     => 'title',
			'max_reviews' => '',
			'min_rating'  => '',
			'order_by'    => '',
			'show_author' => false,
			'show_date'   => false,
			'show_link'   => false,
			'show_rating' => false,
			'site_name'   => '',
			'word_limit'  => 55,
		];

		$args = shortcode_atts( $defaults, $this->args );

		$reviews = $this->app->make( 'Database' )->getReviews( $args );

		if( $reviews ) {

			$html = '';

			foreach( $reviews as $post ) : setup_postdata( $post );

				$meta = get_post_meta( $post->ID );
				$meta = (object) array_map( 'array_shift', $meta );

				$review_author  = $this->reviewAuthor( $args['show_author'], $meta->author );
				$review_excerpt = $this->reviewExcerpt( $args, $meta, $post->ID );
				$review_meta    = $this->reviewMeta( $args, $meta, $post->ID );
				$review_title   = $this->reviewTitle( $args, $meta, $post->ID );

				$print_both    = $review_title . $review_meta . $review_excerpt;
				$print_excerpt = $review_meta . $review_excerpt;
				$print_title   = $review_title . $review_meta;

				$html .= sprintf( '<div class="glsr-review">%s%s</div>', ${"print_{$args['display']}"}, $review_author );

			endforeach;

			wp_reset_postdata();
		}
		else {
			$html = sprintf( '<p class="glsr-review glsr-no-reviews">%s</p>',
				__( 'No reviews were found.', 'site-reviews' )
			);
		}

		return sprintf( '<div class="glsr-reviews-wrap"><div class="glsr-reviews glsr-reviews-%s %s">%s</div></div>',
			$args['site_name'],
			$args['class'],
			$html
		);
	}

	/**
	 * Build an excerpt from a string
	 *
	 * @param string $text
	 * @param int    $wordCount
	 * @param string $more
	 *
	 * @return string
	 */
	protected function buildExcerpt( $text, $wordCount = 55, $more = null )
	{
		$text = strip_shortcodes( $text );
		$text = wptexturize( $text );
		$text = convert_smilies( $text );
		$text = wpautop( $text );
		$text = str_replace( ']]>', ']]&gt;', $text );
		$text = wp_trim_words( $text, $wordCount, $more );

		return $text;
	}

	/**
	 * Build the review author string
	 *
	 * @param string $author
	 * @param string $metaAuthor
	 *
	 * @return string
	 */
	protected function reviewAuthor( $author, $metaAuthor )
	{
		return wp_validate_boolean( $author )
			? sprintf( '<p class="glsr-review-author">&mdash;%s</p> ', $metaAuthor )
			: '';
	}

	/**
	 * Build the review excerpt string
	 *
	 * @param object $meta
	 * @param string $postId
	 *
	 * @return string
	 */
	protected function reviewExcerpt( array $args, $meta, $postId )
	{
		$excerpt     = $this->buildExcerpt( get_the_content( $postId ), $args['word_limit'] );
		$review_link = $this->reviewLink( $args['show_link'], $meta->url );

		$use_excerpt_as_link = apply_filters( "site-reviews/{$meta->site_name}/widget/use_excerpt_as_link", false )
			&& in_array( $args['display'], ['both', 'excerpt'] )
			&& !empty( $review_link );

		$show_excerpt_read_more = !apply_filters( "site-reviews/{$meta->site_name}/widget/hide_excerpt_read_more", false )
			? $review_link
			: '';

		$review_excerpt = $use_excerpt_as_link
			? sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $meta->url ), $excerpt )
			: "{$excerpt} {$show_excerpt_read_more}";

		return !empty( $review_excerpt )
			? sprintf( '<p class="glsr-review-excerpt">%s</p>', $review_excerpt )
			: '';
	}

	/**
	 * Build the review url
	 *
	 * @param string $link
	 * @param string $metaLink
	 *
	 * @return string
	 */
	protected function reviewLink( $link, $metaLink )
	{
		return wp_validate_boolean( $link )
			? sprintf( '<span class="glsr-review-link">[<a href="%s" target="_blank">%s</a>]</span>', esc_url( $metaLink ), __( 'read more', 'site-reviews' ) )
			: '';
	}

	/**
	 * Build the review meta string
	 *
	 * @param object $meta
	 * @param string $postId
	 *
	 * @return string
	 */
	protected function reviewMeta( array $args, $meta, $postId )
	{
		$rating = wp_validate_boolean( $args['show_rating'] )
			? $this->app->make( 'Html' )->renderPartial( 'rating', ['stars' => $meta->rating ], 'return' )
			: '';

		$date = wp_validate_boolean( $args['show_date'] )
			? sprintf( '<span class="glsr-review-date">%s</span> ', get_the_date( 'M j, Y', $postId ) )
			: '';

		return ( $rating || $date )
			? sprintf( '<p class="glsr-review-meta">%s%s</p>', $rating, $date )
			: '';
	}

	/**
	 * Build the review title string
	 *
	 * @param object $meta
	 * @param string $postId
	 *
	 * @return string
	 */
	protected function reviewTitle( array $args, $meta, $postId )
	{
		$review_link = $this->reviewLink( $args['show_link'], $meta->url );

		$use_title_as_link = apply_filters( "site-reviews/{$meta->site_name}/widget/use_title_as_link", false )
			&& in_array( $args['display'], ['both', 'excerpt'] )
			&& !empty( $review_link );

		$review_title = $use_title_as_link
			? sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $meta->url ), get_the_title( $postId ) )
			: get_the_title( $postId );

		return !empty( $review_title )
			? sprintf( '<h3 class="glsr-review-title">%s</h3>', $review_title )
			: '';
	}
}
