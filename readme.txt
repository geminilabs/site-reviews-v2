=== Site Reviews ===
Contributors: geminilabs, pryley
Donate link: http://geminilabs.io/donate
Tags: best reviews, business ratings, business reviews, curated reviews, moderated reviews, rating widget, rating, ratings shortcode, review widget, reviews login, reviews shortcode, reviews, simple reviews, site reviews, star rating, star review, submit review, testimonial, user rating, user review, user reviews, wp rating, wp review, wp testimonials
Requires at least: 4.0.0
Tested up to: 4.7
Stable tag: 2.0.0 alpha
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

* [new] MCE shortcode button dropdown
* [new] Review avatars (gravatar.com)
* [new] Review categories
* Actively developed and supported
* Clean and easy-to-configure user interface
* Configurable Widgets
* Easy setup and implementation
* Filter reviews by rating
* Logging
* Minimal widget styling (tested with all official WP themes)
* Review Pagination
* Shortcodes: Display reviews in your post content and templates
* Webhook notifications for Slack
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

= I don't want form placeholders. How do I remove them? =

To remove the form placeholders, go to "Settings" -> "Submission Form" and replace all placeholder text with a single space character. Placeholders no more!

= How do I disable the plugin CSS and/or Javascript? =

To disable the plugin stylesheet or javascript from loading on your website, copy and paste the following WordPress Filter Hooks into your theme's functions.php file:

`add_filter( 'site-reviews/assets/css', '__return_false' );`
`add_filter( 'site-reviews/assets/js', '__return_false' );`

= The widgets look funny in my sidebar. What's happening? =

Some themes may have very small sidebars and/or CSS styles that conflict or alter the styles within Site Reviews. To correct any styling errors you can either disable the plugin's CSS altogether, or override the CSS selectors in use to make the widget or shortcode appear how you'd like. CSS-related issues are not actively supported as there are too many variations between the thousands of WordPress themes available.

== Screenshots ==

1. A view of the All Reviews page

2. A view of the Edit Review page

3. A view of the MCE shortcode dropdown button

4. A view of the Site Reviews &gt; Settings page

5. A view of the Site Reviews &gt; Get Help &gt; Documentation tab

6. A view of the Site Reviews &gt; Get Help &gt; System Info tab

7. A view of the Recent Site Reviews widget settings

8. A view of the Submit a Site Review widget settings

9. How the Recent Site Reviews widget looks like using the Twenty Sixteen WordPress theme

10. How the Submit a Site Review widget looks like using the Twenty Sixteen WordPress theme

== Changelog ==

= Unreleased =

* [feature] MCE shortcode button dropdown
* [feature] Review avatars (gravatar.com)
* [feature] Review categories
* Added option to choose the excerpt length
* Added option to enable excerpts
* Custom Published/Pending labels

= 1.2.2 (2017-01-06) =

* Added hook to change excerpt length
* Added hook to disable the excerpt and instead display the full review content

= 1.2.1 (2016-12-28) =

* Fix PHP 5.4 compatibility regression

= 1.2.0 (2016-12-27) =

* [feature] Send notifications to Slack
* Fix notifications to use the email template setting

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
