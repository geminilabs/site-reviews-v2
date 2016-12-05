=== Site Reviews ===
Contributors: geminilabs, pryley
Donate link: http://geminilabs.io/donate
Tags: best reviews, business ratings, business reviews, curated reviews, moderated reviews, rating widget, rating, ratings shortcode, review widget, reviews login, reviews shortcode, reviews, simple reviews, site reviews, star rating, star review, submit review, testimonial, user rating, user review, user reviews, wp rating, wp review, wp testimonials
Requires at least: 4.0.0
Tested up to: 4.6
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Site Reviews is a WordPress plugin which allows you to easily receive and display reviews for your business or website.

== Description ==

Site Reviews is a plugin that allows your visitors to submit site reviews with a 1-5 star rating on your website, similar to the way you would on TripAdvisor or Yelp, and display them using a widget or shortcode.

You can pin your best reviews so that they are always shown first, require approval before new review submissions are published, require visitors to be logged-in in order to write a review, send custom notifications on a new submission, and more. The plugin provides both widgets and shortcodes along with full shortcode documentation.

Add-ons will be released shortly that allow you to sync your TripAdvisor and Yelp reviews in order to display them locally on your own website.

The plugin [roadmap](https://github.com/geminilabs/site-reviews/blob/develop/ROADMAP.md) defines the upcoming features which include support for custom review categories, webhook notifications, paginated reviews, allowing visitors to up/down vote submitted reviews, reviewer avatars, percentage/fraction ratings, and more.

Follow plugin development on github at: https://github.com/geminilabs/site-reviews/

= Current Features =

* [new] Review Pagination
* Actively developed and supported
* Clean and easy-to-configure user interface
* Configurable Widgets
* Easy setup and implementation
* Filter reviews by rating
* Logging
* Minimal widget styling (tested with all official WP themes)
* Shortcodes: Display reviews in your post content and templates
* WordPress.org support
* WP Filter Hooks

== Installation ==

= Minimum plugin requirements =

* WordPress 4.0.0
* PHP 5.4

= Automatic installation =

Log in to your WordPress dashboard, navigate to the Plugins menu and click "Add New".

In the search field type "Site Reviews" and click Search Plugins. Once you have found the plugin you can view details about it such as the point release, rating and description. You can install it by simply clicking "Install Now".

= Manual installation =

Download the Site Reviews plugin and uploading it to your server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

= The widgets look funny in my sidebar. What's happening? =

Some themes may have very small sidebars and CSS styles that conflict or alter the styles within Site Reviews. To correct any styling errors you can either disable the plugin's CSS altogether, or override the CSS selectors in use to make the widget or shortcode appear how you'd like. CSS-related issues are not actively supported as there are too many variations between the thousands of WordPress themes available.

== Screenshots ==

1. A view of the All Reviews page

2. A view of the Edit Review page

3. A view of the Site Reviews &gt; Settings page

4. A view of the Site Reviews &gt; Get Help &gt; Documentation tab

5. A view of the Site Reviews &gt; Get Help &gt; System Info tab

6. A view of the Site Reviews &gt; Add-Ons page

7. A view of the Recent Site Reviews widget settings

8. A view of the Submit a Site Review widget settings

9. How the Recent Site Reviews widget looks like using the Twenty Sixteen WordPress theme

10. How the Submit a Site Review widget looks like using the Twenty Sixteen WordPress theme

== Changelog ==

= 1.1.1 (2016-12-05) =

* Remove ".star-rating" class on frontend which conflicts with the woocommerce plugin CSS
* Added hooks to modify rendered fields/partials HTML

= 1.1.0 (2016-11-16) =

* [feature] Pagination
* [breaking] Changed internal widget hook names
* [breaking] Changed text-domain to "site-reviews"
* Set post_meta defaults when creating a review
* [addon support] Display read-only reviews
* [addon support] Display widget link options (conditional field)
* [addon support] Show all review types by default in widgets and shortcodes

= 1.0.4 (2016-11-14) =

* use the logged-in user's display_name by default instead of "Anonymous" when submitting reviews
* Fix shortcodes to insert in the_content correctly

= 1.0.3 (2016-11-09) =

* Updated plugin description
* Fix plain-text emails
* Fix inconsistencies with plugin settings form fields
* Fix internal add-on integration code

= 1.0.2 (2016-10-24) =

* Set widget and settings defaults
* Fix PHP error that is thrown when settings have not yet been saved to DB

= 1.0.0 (2016-10-21) =

* Initial plugin release
