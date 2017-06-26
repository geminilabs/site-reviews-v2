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
	const HIDDEN_KEYS = ['author', 'date', 'excerpt', 'rating', 'title'];

	/**
	 * @return null|string
	 */
	public function render()
	{
		$this->normalize();
		$this->buildSchema();
		if( $this->isHidden() )return;
		$html = '';
		$reviews = $this->db->getReviews( $this->args );
		foreach( $reviews->reviews as $review ) {
			$html .= sprintf( '<div class="glsr-review">%s</div>',
				$this->buildTitle( $review ) .
				$this->buildMeta( $review ) .
				$this->buildText( $review ) .
				$this->buildAvatar( $review->avatar ) .
				$this->buildAuthor( $review->author )
			);
		}
		if( empty( $reviews->reviews )) {
			$html = sprintf( '<p class="glsr-review glsr-no-reviews">%s</p>',
				__( 'No reviews were found.', 'site-reviews' )
			);
		}
		else if( $this->args['pagination'] ) {
			$html .= $this->buildPagination( $reviews->max_num_pages );
		}
		return sprintf(
			'<div class="glsr-reviews-wrap"><div class="glsr-reviews %s">%s</div></div>',
			$this->args['class'],
			$html
		);
	}

	/**
	 * @param string $author
	 * @return null|string
	 */
	protected function buildAuthor( $author )
	{
		if( in_array( 'author', $this->args['hide'] ))return;
		$dash = $this->db->getOption( 'settings.reviews.avatars.enabled' ) != 'yes'
			? '&mdash;'
			: '';
		return sprintf( '<p class="glsr-review-author">%s<span>%s</span></p>', $dash, $author );
	}

	/**
	 * @param string $avatar
	 * @return null|string
	 */
	protected function buildAvatar( $avatar )
	{
		if( $this->db->getOption( 'settings.reviews.avatars.enabled' ) != 'yes' )return;
		return sprintf( '<div class="glsr-review-avatar"><img src="%s" width="36"></div>', $avatar );
	}

	/**
	 * @param string $date
	 * @return null|string
	 */
	protected function buildDate( $date )
	{
		if( in_array( 'date', $this->args['hide'] ))return;
		$dateFormat = $this->db->getOption( 'settings.reviews.date.format', 'default' );
		if( $dateFormat == 'relative' ) {
			$date = $this->app->make( 'Date' )->relative( $date );
		}
		else {
			$format = $dateFormat == 'custom'
				? $this->db->getOption( 'settings.reviews.date.custom', 'M j, Y' )
				: get_option( 'date_format' );
			$date = date_i18n( $format, strtotime( $date ));
		}
		return sprintf( '<span class="glsr-review-date">%s</span>', $date );
	}

	/**
	 * @param object $review
	 * @return string
	 */
	protected function buildMeta( $review )
	{
		return sprintf( '<p class="glsr-review-meta">%s%s</p>',
			$this->buildRating( $review->rating ),
			$this->buildDate( $review->date )
		);
	}

	/**
	 * @param int $maxPageNum
	 * @return string|null
	 */
	protected function buildPagination( $maxPageNum )
	{
		if( $maxPageNum < 2 )return;
		$paged = $this->app->make( 'Query' )->getPaged();
		$theme = wp_get_theme()->get( 'TextDomain' );
		if( in_array( $theme, ['twentyten','twentyeleven','twentytwelve','twentythirteen'] )) {
			$links = '';
			if( $paged > 1 ) {
				$links .= sprintf( '<div class="nav-previous"><a href="%s"><span class="meta-nav">&larr;</span> %s</a></div>',
					get_pagenum_link( $paged - 1 ),
					__( 'Previous', 'site-reviews' )
				);
			}
			if( $paged < $maxPageNum ) {
				$links .= sprintf( '<div class="nav-next"><a href="%s">%s <span class="meta-nav">&rarr;</span></a></div>',
					get_pagenum_link( $paged + 1 ),
					__( 'Next', 'site-reviews' )
				);
			}
		}
		else {
			$links = paginate_links([
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'site-reviews' ) . ' </span>',
				'current' => $paged,
				'mid_size' => 1,
				'next_text' => __( 'Next &rarr;', 'site-reviews' ),
				'prev_text' => __( '&larr; Previous', 'site-reviews' ),
				'total' => $maxPageNum,
			]);
		}
		$links = apply_filters( 'site-reviews/reviews/navigation_links', $links, $paged, $maxPageNum );
		if( !$links )return;
		return $this->getPaginationTemplate( $links );
	}

	/**
	 * @param int $rating
	 * @return string
	 */
	protected function buildRating( $rating )
	{
		return $this->app->make( 'Html' )->renderPartial( 'star-rating', [
			'hidden' => in_array( 'rating', $this->args['hide'] ),
			'rating' => $rating,
		]);
	}

	/**
	 * @return void
	 */
	protected function buildSchema()
	{
		if( !$this->args['schema'] )return;
		$this->app->make( 'Schema' )->build( wp_parse_args( ['count' => -1], $this->args ));
	}

	/**
	 * @param object $review
	 * @return null|string
	 */
	protected function buildText( $review )
	{
		if( in_array( 'excerpt', $this->args['hide'] ))return;
		$text = $this->normalizeText( $review->content );
		$text = $this->getExcerpt( $text );
		$text = apply_filters( 'site-reviews/reviews/review/text', $text, $review, $this->args );
		$text = wpautop( $text );
		return sprintf( '<div class="glsr-review-excerpt">%s</div>', $text );
	}

	/**
	 * @param object $review
	 * @return null|string
	 */
	protected function buildTitle( $review )
	{
		if( in_array( 'title', $this->args['hide'] ))return;
		if( empty( $review->title )) {
			$review->title = __( 'No Title', 'site-reviews' );
		}
		$title = sprintf( '<h3 class="glsr-review-title">%s</h3>', $review->title );
		return apply_filters( 'site-reviews/reviews/review/title', $title, $review, $this->args );
	}

	/**
	 * @param string $text
	 * @return string
	 */
	protected function getExcerpt( $text )
	{
		if( $this->db->getOption( 'settings.reviews.excerpt.enabled' ) != 'yes' ) {
			return $text;
		}
		$wordCount = $this->db->getOption( 'settings.reviews.excerpt.length', $this->args['word_limit'] );
		$excerpt = wp_trim_words( $text, $wordCount, '' );
		$hiddenText = substr( $text, strlen( $excerpt ));
		if( empty( $hiddenText )) {
			return $excerpt;
		}
		return sprintf( '%s<span class="glsr-hidden" data-show-more="%s" data-show-less="%s">%s</span>',
			$excerpt,
			__( 'Show more', 'site-reviews' ),
			__( 'Show less', 'site-reviews' ),
			$hiddenText
		);
	}

	/**
	 * @param string $links
	 * @return string
	 */
	protected function getPaginationTemplate( $links )
	{
		$theme = wp_get_theme()->get( 'TextDomain' );
		switch( $theme ) {
			case 'twentyten':
			case 'twentyeleven':
			case 'twentytwelve':
				$class = '';
				$template = '<nav class="navigation" role="navigation">%3$s</nav>';
				break;
			case 'twentyfourteen':
				$class = 'paging-navigation';
				$template = '<nav class="navigation %1$s" role="navigation"><h2 class="screen-reader-text">%2$s</h2><div class="pagination loop-pagination">%3$s</div></nav>';
				break;
			default:
				$class = 'pagination';
				$template = '<nav class="navigation %1$s" role="navigation"><h2 class="screen-reader-text">%2$s</h2><div class="nav-links">%3$s</div></nav>';
		}
		$template = apply_filters( 'navigation_markup_template', $template, $class );
		$screenReaderText = __( 'Site Reviews navigation', 'site-reviews' );
		return sprintf( $template, $class, $screenReaderText, $links );
	}

	/**
	 * @return void
	 */
	protected function normalize()
	{
		$defaults = [
			'assigned_to' => '',
			'category'    => '',
			'class'       => '',
			'count'       => '',
			'hide'        => '',
			'orderby'     => 'date',
			'pagination'  => false,
			'rating'      => '1',
			'schema'      => false,
			'type'        => '',
			'word_limit'  => 55,
		];
		$this->args = shortcode_atts( $defaults, $this->args );
		array_walk( $this->args, function( &$value, $key ) {
			$methodName = $this->app->make( 'Helper' )->buildMethodName( $key, 'normalize' );
			if( !method_exists( $this, $methodName ))return;
			$value = $this->$methodName( $value );
		});
	}

	/**
	 * @return array
	 */
	protected function normalizeHide( $hide )
	{
		return array_filter(( array ) $hide );
	}

	/**
	 * @return bool
	 */
	protected function normalizePagination( $pagination )
	{
		return wp_validate_boolean( $pagination );
	}

	/**
	 * @return bool
	 */
	protected function normalizeSchema( $schema )
	{
		return wp_validate_boolean( $schema );
	}

	/**
	 * @return string
	 */
	protected function normalizeText( $text )
	{
		$text = strip_shortcodes( $text );
		$text = wptexturize( $text );
		$text = convert_smilies( $text );
		$text = str_replace( ']]>', ']]&gt;', $text );
		return $text;
	}

	/**
	 * @return bool
	 */
	protected function isHidden( array $values = [] )
	{
		if( empty( $values )) {
			$values = static::HIDDEN_KEYS;
		}
		return !array_diff( $values, $this->args['hide'] );
	}
}
