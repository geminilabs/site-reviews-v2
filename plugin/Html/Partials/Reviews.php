<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
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
	 * @return null|string
	 */
	public function render()
	{
		global $post;

		$defaults = [
			'category'     => '',
			'class'        => '',
			'count'        => '',
			'hide_author'  => false,
			'hide_date'    => false,
			'hide_excerpt' => false,
			'hide_link'    => true,
			'hide_rating'  => false,
			'hide_title'   => false,
			'orderby'      => 'date',
			'pagination'   => false,
			'rating'       => '',
			'type'         => '',
			'word_limit'   => 55,
		];

		$args = shortcode_atts( $defaults, $this->args );

		if( $this->hideReviews( $args ) )return;

		$reviews = $this->db->getReviews( $args );

		$html = '';

		foreach( $reviews->reviews as $review ) {
			$html .= sprintf( '<div class="glsr-review">%s%s%s%s%s</div>',
				$this->reviewTitle( $review, $args ),
				$this->reviewMeta( $review, $args ),
				$this->reviewExcerpt( $review, $args ),
				$this->reviewAvatar( $review->avatar ),
				$this->reviewAuthor( $review->author, $args['hide_author'] )
			);
		}

		if( empty( $reviews->reviews )) {
			$html = sprintf( '<p class="glsr-review glsr-no-reviews">%s</p>',
				__( 'No reviews were found.', 'site-reviews' )
			);
		}
		else if( $args['pagination'] ) {
			$html .= $this->buildPagination( $reviews->max_num_pages );
		}

		return sprintf(
			'<div class="glsr-reviews-wrap">' .
				'<div class="glsr-reviews %s">%s</div>' .
			'</div>',
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
		$text = str_replace( ']]>', ']]&gt;', $text );

		if( $this->db->getOption( 'settings.reviews.excerpt.enabled' ) == 'yes' ) {
			$wordCount = $this->db->getOption( 'settings.reviews.excerpt.length', $wordCount );
			$text = wp_trim_words( $text, $wordCount, $more );
		}

		$text = wpautop( $text );

		return $text;
	}

	/**
	 * Build the reviews pagination
	 *
	 * @param int $maxPageNum
	 *
	 * @return string|null
	 */
	protected function buildPagination( $maxPageNum )
	{
		if( $maxPageNum < 2 )return;

		$paged = $this->app->make( 'Query' )->getPaged();
		$theme = wp_get_theme()->get( 'TextDomain' );

		if( in_array( $theme, ['twentyten','twentyeleven','twentytwelve','twentythirteen'] ) ) {

			$links = '';

			if( $paged > 1 ) {
				$links .= sprintf( '<div class="nav-previous"><a href="%s">%s</a></div>',
					get_pagenum_link( $paged - 1 ),
					__( '<span class="meta-nav">&larr;</span> Previous', 'site-reviews' )
				);
			}
			if( $paged < $maxPageNum ) {
				$links .= sprintf( '<div class="nav-next"><a href="%s">%s</a></div>',
					get_pagenum_link( $paged + 1 ),
					__( 'Next <span class="meta-nav">&rarr;</span>', 'site-reviews' )
				);
			}
		}
		else {
			$links = paginate_links([
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'site-reviews' ) . ' </span>',
				'current'            => $paged,
				'mid_size'           => 1,
				'next_text'          => __( 'Next &rarr;', 'site-reviews' ),
				'prev_text'          => __( '&larr; Previous', 'site-reviews' ),
				'total'              => $maxPageNum,
			]);
		}

		$links = apply_filters( 'site-reviews/reviews/navigation_links', $links, $paged, $maxPageNum );

		if( !$links )return;

		return $this->paginationTemplate( $links );
	}

	/**
	 * @return bool
	 */
	protected function hideReviews( array $args )
	{
		return wp_validate_boolean( $args['hide_author'] )
			&& wp_validate_boolean( $args['hide_date'] )
			&& wp_validate_boolean( $args['hide_excerpt'] )
			&& wp_validate_boolean( $args['hide_rating'] )
			&& wp_validate_boolean( $args['hide_title'] );
	}

	/**
	 * Get the correct pagination template
	 *
	 * @param string $links
	 *
	 * @return string
	 */
	protected function paginationTemplate( $links )
	{
		$class = '';
		$theme = wp_get_theme()->get( 'TextDomain' );

		switch( $theme ) {

			case 'twentyten':
			case 'twentyeleven':
			case 'twentytwelve':
				$template = '<nav class="navigation" role="navigation">%3$s</nav>';
				break;
			case 'twentyfourteen':
				$class    = 'paging-navigation';
				$template = '' .
				'<nav class="navigation %1$s" role="navigation">' .
					'<h2 class="screen-reader-text">%2$s</h2>' .
					'<div class="pagination loop-pagination">%3$s</div>' .
				'</nav>';
				break;
			default:
				$class    = 'pagination';
				$template = '' .
				'<nav class="navigation %1$s" role="navigation">' .
					'<h2 class="screen-reader-text">%2$s</h2>' .
					'<div class="nav-links">%3$s</div>' .
				'</nav>';
		}

		/**
		 * Filters the navigation markup template.
		 *
		 * @since WP 4.4.0
		 */
		$template = apply_filters( 'navigation_markup_template', $template, $class );

		$screenReaderText = __( 'Site Reviews navigation', 'site-reviews' );

		return sprintf( $template, $class, $screenReaderText, $links );
	}

	/**
	 * Build the review author string
	 *
	 * @param string $reviewAuthor
	 * @param string $hideAuthor
	 *
	 * @return null|string
	 */
	protected function reviewAuthor( $reviewAuthor, $hideAuthor )
	{
		if( wp_validate_boolean( $hideAuthor ) )return;

		$dash = $this->db->getOption( 'settings.reviews.avatars.enabled' ) != 'yes'
			? '&mdash;'
			: '';

		return sprintf( '<p class="glsr-review-author">%s%s</p>', $dash, $reviewAuthor );
	}

	/**
	 * Build the review avatar string
	 *
	 * @param string $reviewAvatar
	 *
	 * @return null|string
	 */
	protected function reviewAvatar( $reviewAvatar )
	{
		if( $this->db->getOption( 'settings.reviews.avatars.enabled' ) != 'yes' )return;

		return sprintf( '<div class="glsr-review-avatar"><img src="%s" width="36" /></div>', $reviewAvatar );
	}

	/**
	 * Build the review excerpt string
	 *
	 * @param object $review
	 *
	 * @return null|string
	 */
	protected function reviewExcerpt( $review, array $args )
	{
		if( $args['hide_excerpt'] )return;

		$excerpt     = $this->buildExcerpt( $review->content, $args['word_limit'] );
		$review_link = $this->reviewLink( $args['hide_link'], $review->url );

		$use_excerpt_as_link = apply_filters( 'site-reviews/reviews/use_excerpt_as_link', false ) && $review_link !== null;

		$show_excerpt_read_more = !apply_filters( 'site-reviews/reviews/hide_excerpt_read_more', false )
			? $review_link
			: '';

		$review_excerpt = $use_excerpt_as_link
			? sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $review->url ), $excerpt )
			: "{$excerpt} {$show_excerpt_read_more}";

		return !empty( $review_excerpt )
			? sprintf( '<div class="glsr-review-excerpt">%s</div>', $review_excerpt )
			: '';
	}

	/**
	 * Build the review url
	 *
	 * @param string $hideLink
	 * @param string $reviewLink
	 *
	 * @return null|string
	 */
	protected function reviewLink( $hideLink, $reviewLink )
	{
		if( wp_validate_boolean( $hideLink ) )return;

		return sprintf( '<span class="glsr-review-link">[<a href="%s" target="_blank">%s</a>]</span>',
			esc_url( $reviewLink ),
			__( 'read more', 'site-reviews' )
		);
	}

	/**
	 * Build the review meta string
	 *
	 * @param object $review
	 *
	 * @return string
	 */
	protected function reviewMeta( $review, array $args )
	{
		$date = '';

		if( !wp_validate_boolean( $args['hide_date'] ) ) {
			$format = $this->db->getOption( 'settings.reviews.date.enabled' ) == 'yes'
				? $this->db->getOption( 'settings.reviews.date.format', 'M j, Y' )
				: get_option( 'date_format' );

			$date = sprintf( '<span class="glsr-review-date">%s</span> ', date_i18n( $format, strtotime( $review->date )));
		}

		$rating = !wp_validate_boolean( $args['hide_rating'] )
			? $this->app->make( 'Html' )->renderPartial( 'rating', ['stars' => $review->rating ], 'return' )
			: '';

		return ( $rating || $date )
			? sprintf( '<p class="glsr-review-meta">%s%s</p>', $rating, $date )
			: '';
	}

	/**
	 * Build the review title string
	 *
	 * @param object $review
	 *
	 * @return null|string
	 */
	protected function reviewTitle( $review, array $args )
	{
		if( $args['hide_title'] )return;

		$review_link = $this->reviewLink( $args['hide_link'], $review->url );

		$use_title_as_link = apply_filters( 'site-reviews/reviews/use_title_as_link', false ) && $review_link !== null;

		$review_title = $use_title_as_link
			? sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $review->url ), $review->title )
			: $review->title;

		return !empty( $review_title )
			? sprintf( '<h3 class="glsr-review-title">%s</h3>', $review_title )
			: '';
	}
}
