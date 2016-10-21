=== Reviews Premium ===
Contributors: geminilabs, pryley
Donate link: http://geminilabs.io/donate
Tags: reviews, tripadvisor, tripadvisor reviews, yelp, yelp reviews, widget
Requires at least: 4.0.0
Tested up to: 4.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Reviews Premium makes it easy for you to fetch reviews from Tripadvisor and Yelp and display them on your website or blog via easy-to-use widgets and shortcodes.

== Description ==

Reviews Premium allows you to easily display the reviews from Tripadvisor and Yelp for any business on your website or blog using easy-to-use widgets and shortcodes. No API keys required!

Minimum plugin requirements:

* PHP 5.4
* WordPress 4.0.0

= Reviews Premium Features =

* Actively developed and supported
* Automatic updates
* Choose a minimum star-rating when fetching reviews (e.g. fetch only 5-star reviews!)
* Clean and easy-to-configure user interface
* Configurable Widgets
* Easy setup and implementation
* Fetch **ALL** site reviews in parallel from Tripadvisor and Yelp
* Fetch the reviewer avatars
* Filter reviews by star-rating/site
* Logging
* Minimal widget styling (tested with all official WP themes)
* No API keys required!
* Priority support and updates
* Shortcodes! Display reviews in your post content and templates
* Widget/Shortcode: Paginated reviews
* Widget/Shortcode: Show the site logo and link to the site page
* Widget/Shortcode: Themes
* WordPress.org support
* WP Filter Hooks

== Installation ==

1. Upload the `reviews` folder and it's contents to the `/wp-content/plugins/` directory or install via the WordPress plugins panel in your WordPress admin dashboard
2. Activate the plugin through the 'Plugins' menu in WordPress
3. That's it! You should now be able to access the Plugin's options via the new reviews panel.

== Frequently Asked Questions ==

= Why should I use this plugin? =

This is the *only* plugin for WordPress that allows you to sync your Tripadvisor and Yelp reviews and display them on your website with a widget or shortcode. If this is something you need, then this plugin is for you!

= The widgets look funny in my sidebar. What's happening? =

Some themes may have very small sidebars and CSS styles that conflict or alter the styles within Reviews. To correct any styling errors you can either disable the plugin's CSS altogether, or override the CSS selectors in use to make the widget appear how you'd like. CSS-related issues are not actively supported as there are too many variations between the thousands of WordPress themes available.

== Screenshots ==

1. A view of the Reviews Archive page

2. A view of the Fetch Reviews &gt; Status tab

3. A view of the Fetch Reviews &gt; Settings tab

4. A view of the Fetch Reviews &gt; System Info tab

5. A view of the Edit Review page

6. A view of the widget settings

7. How the widget looks in a website sidebar

== Changelog ==

= 1.0 =
* Initial plugin release

== Filter Hooks ==

Reviews comes with some Filter Hooks that allow you to set hidden options. Replace where it says "{$site_name}" with the name of the site name (i.e. tripadvisor or yelp).

1. Disable the widget stylesheet

	`add_filter( 'site-reviews/css', '__return_false' );`

2. Make the widget review title a link

	`add_filter( "site-reviews/{$site_name}/widget/use_title_as_link", '__return_true' );`

3. Make the widget review excerpt a link

	`add_filter( "site-reviews/{$site_name}/widget/use_excerpt_as_link", '__return_true' );`

4. Hide the widget review excerpt "read more" link

	`add_filter( "site-reviews/{$site_name}/widget/hide_excerpt_read_more", '__return_true' );`

= DIY Filter Hooks =

Disclaimer: The following filter hooks are advanced **DIY** filter hooks. You should only attempt to use them if the selectors stop working (i.e. If Tripadvisor or Yelp have changed their website) and simply changing the selectors in the Advanced Settings section does not work. If this ever happens, a plugin update will be released to fix it...so these filter will likely never need to be used!

1. Filter the review values before saving them to the database

	`
	add_filter( "site-reviews/{$site_name}/review", function( $review, $review_node ) {
		return $review;
	}, 10, 2 );
	`

2. Filter the fetched review selector value

	`
	add_filter( "site-reviews/{$site_name}/selector/children", function( $value, $key, $review_selector_node, $document_node ) {
		return $value;
	}, 10, 4 );
	`

3. Filter the fetched review ID value

	`
	add_filter( "site-reviews/{$site_name}/selector/reviewid", function( $review_id, $review_selector_node ) {
		return $review_id;
	}, 10, 2 );
	`
