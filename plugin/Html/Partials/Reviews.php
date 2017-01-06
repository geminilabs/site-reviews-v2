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
		global $post;

		$defaults = [
			'class'       => '',
			'display'     => 'title',
			'max_reviews' => '',
			'min_rating'  => '',
			'order_by'    => '',
			'pagination'  => false,
			'show_author' => false,
			'show_date'   => false,
			'show_link'   => false,
			'show_rating' => false,
			'site_name'   => '',
			'word_limit'  => 55,
		];

		$args = shortcode_atts( $defaults, $this->args );

		$reviews = $this->app->make( 'Database' )->getReviews( $args );

		if( $reviews->have_posts() ) {

			$html = '';

			while( $reviews->have_posts() ) :

				$reviews->the_post();

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

			endwhile;

			if( $args['pagination'] ) {
				$html .= $this->buildPagination( $reviews->max_num_pages );
			}

			wp_reset_postdata();
		}
		else {
			$html = sprintf( '<p class="glsr-review glsr-no-reviews">%s</p>',
				__( 'No reviews were found.', 'site-reviews' )
			);
		}

		return sprintf( '<div class="glsr-reviews-wrap"><div class="glsr-reviews %s">%s</div></div>',
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

		if( apply_filters( 'site-reviews/reviews/use_excerpt', true ) ) {
			$wordCount = apply_filters( 'site-reviews/reviews/excerpt_length', $wordCount );
			$text = wp_trim_words( $text, $wordCount, $more );
		}

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

		$paged = $this->app->make( 'Database' )->getCurrentPageNumber();
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

		$template = apply_filters( 'navigation_markup_template', $template, $class );

		$screenReaderText = __( 'Site Reviews navigation', 'site-reviews' );

		return sprintf( $template, $class, $screenReaderText, $links );
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

		$use_excerpt_as_link = apply_filters( 'site-reviews/reviews/use_excerpt_as_link', false )
			&& in_array( $args['display'], ['both', 'excerpt'] )
			&& !empty( $review_link );

		$show_excerpt_read_more = !apply_filters( 'site-reviews/reviews/hide_excerpt_read_more', false )
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

		$use_title_as_link = apply_filters( 'site-reviews/widget/use_title_as_link', false )
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
