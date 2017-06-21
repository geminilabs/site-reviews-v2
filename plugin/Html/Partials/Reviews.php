<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Html\Partials;

use GeminiLabs\SiteReviews\Rating;
use GeminiLabs\SiteReviews\Html\Partials\Base;

class Reviews extends Base
{
	/**
	 * @return null|string
	 */
	public function render()
	{
		$this->normalize();
		if( $this->shouldHideReviews() )return;
		$html = '';
		$reviews = $this->db->getReviews( $this->args );
		$schema = $this->getSchema( ' itemprop="review" itemscope itemtype="http://schema.org/Review"' );

		foreach( $reviews->reviews as $review ) {
			$html .= sprintf( '<div class="glsr-review"%s>%s</div>',
				$schema,
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
			'<div class="glsr-reviews-wrap"><div class="glsr-reviews %s">%s</div>%s</div>',
			$this->args['class'],
			$html,
			$this->buildSchema()
		);
	}

	/**
	 * @param string $author
	 * @return null|string
	 */
	protected function buildAuthor( $author )
	{
		$hidden = in_array( 'author', $this->args['hide'] );
		if( $hidden && $this->args['schema'] ) {
			return sprintf( '<span itemprop="author" itemscope itemtype="http://schema.org/Person"><meta itemprop="name" content="%s"></span>', $author );
		}
		$dash = $this->db->getOption( 'settings.reviews.avatars.enabled' ) != 'yes'
			? '&mdash;'
			: '';
		if( !$hidden && $this->args['schema'] ) {
			return sprintf( '<p class="glsr-review-author" itemprop="author" itemscope itemtype="http://schema.org/Person">%s<span itemprop="name">%s</span></p>', $dash, $author );
		}
		if( !$hidden ) {
			return sprintf( '<p class="glsr-review-author">%s<span>%s</span></p>', $dash, $author );
		}
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
		$hidden = in_array( 'date', $this->args['hide'] );
		$schema = $this->getSchema( sprintf( ' itemprop="datePublished" content="%s"', date( 'c', strtotime( $date ))));
		if( $this->args['schema'] && $hidden ) {
			return sprintf( '<meta%s>', $schema );
		}
		if( !$hidden ) {
			$format = $this->db->getOption( 'settings.reviews.date.enabled' ) == 'yes'
				? $this->db->getOption( 'settings.reviews.date.format', 'M j, Y' )
				: get_option( 'date_format' );
			return sprintf( '<span class="glsr-review-date"%s>%s</span>', $schema, date_i18n( $format, strtotime( $date )));
		}
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
			'schema' => $this->args['schema'],
		]);
	}

	/**
	 * @param object $review
	 * @return null|string
	 */
	protected function buildText( $review )
	{
		$hidden = in_array( 'excerpt', $this->args['hide'] );
		$schema = $this->getSchema( ' itemprop="reviewBody"' );
		$text = $this->normalizeText( $review->content );
		if( $this->args['schema'] && $hidden ) {
			return sprintf( '<meta%s content="%s">', $schema, $text );
		}
		$text = $this->getExcerpt( $text );
		$text = apply_filters( 'site-reviews/reviews/review/text', $text, $review, $this->args );
		$text = wpautop( $text );
		if( !$hidden ) {
			return sprintf( '<div class="glsr-review-excerpt"%s>%s</div>', $schema, $text );
		}
	}

	/**
	 * @param object $review
	 * @return null|string
	 */
	protected function buildTitle( $review )
	{
		if( empty( $review->title )) {
			$review->title = __( 'No Title', 'site-reviews' );
		}
		$hidden = in_array( 'title', $this->args['hide'] );
		$schema = $this->getSchema( ' itemprop="name"' );
		if( $this->args['schema'] && $hidden ) {
			return sprintf( '<meta%s content="%s">', $schema, $review->title );
		}
		if( !$hidden ) {
			$title = sprintf( '<h3 class="glsr-review-title"%s>%s</h3>', $schema, $review->title );
			return apply_filters( 'site-reviews/reviews/review/title', $title, $review, $this->args );
		}
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
		return sprintf( '%s<span class="glsr-hidden" data-read-more="%s">%s</span>',
			$excerpt,
			__( 'read more', 'site-reviews' ),
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
	 * @param string $schema
	 * @return string
	 */
	protected function getSchema( $schema )
	{
		return $this->args['schema']
			? $schema
			: '';
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
			'rating'      => '',
			'schema'      => true,
			'type'        => '',
			'word_limit'  => 55,
		];
		$this->args = shortcode_atts( $defaults, $this->args );
		$this->args['hide'] = (array) $this->args['hide'];
		array_walk( $this->args, function( &$value, $key ) {
			if( in_array( $key, ['pagination','schema'] )) {
				$value = wp_validate_boolean( $value );
			}
		});
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
	protected function shouldHideReviews()
	{
		return !array_diff( ['author', 'date', 'excerpt', 'rating', 'title'], $this->args['hide'] );
	}






	protected function buildSchema()
	{
		$args = $this->args;
		$args['count'] = false;
		$reviews = $this->db->getReviews( $args )->reviews;

		$rating = $this->app->make( 'Rating' );

		$count = count( $reviews );
		$averageRating = $rating->getAverage( $reviews );
		$percentages = preg_filter( '/$/', '%', $rating->getPercentages( $reviews ));

		glsr_debug(
			'rank: ' . $rating->getRanking( $reviews ),
			'imdb rank: ' . $rating->getRankingImdb( $reviews ),
			$averageRating . ' out of 5 stars',
			$count . ' reviews',
			'Excellent: ' . $percentages[5],
			'Very good: ' . $percentages[4],
			'Average: ' . $percentages[3],
			'Poor: ' . $percentages[2],
			'Terrible: ' . $percentages[1]
		);
		return;
	}
}
