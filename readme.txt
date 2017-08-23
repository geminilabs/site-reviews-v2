=== Site Reviews ===
Contributors: geminilabs, pryley
Donate link: https://www.paypal.me/pryley
Tags: best reviews, business ratings, business reviews, curated reviews, moderated reviews, rating widget, rating, ratings shortcode, review widget, reviews login, reviews shortcode, reviews, simple reviews, site reviews, star rating, star review, submit review, testimonial, user rating, user review, user reviews, wp rating, wp review, wp testimonials
Requires at least: 4.0.0
Tested up to: 4.8.1
Stable tag: 2.5.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Site Reviews is a WordPress plugin which allows you to easily receive and display reviews for your website and business.

== Description ==

Site Reviews is a plugin that allows your visitors to submit site reviews with a 1-5 star rating on your website, similar to the way you would on TripAdvisor or Yelp, and then allows you to display them using a widget or shortcode.

You can pin your best reviews so that they are always shown first, require approval before new review submissions are published, require visitors to be logged-in in order to write a review, send custom notifications on a new submission, and more. The plugin provides both widgets and shortcodes along with full shortcode documentation.

Add-ons are being developed to support syncing your TripAdvisor and Yelp reviews in order to display them locally on your website, as well as Post/Page/CPT/Comment ratings/reviews.

The plugin [roadmap](https://github.com/geminilabs/site-reviews/blob/develop/ROADMAP.md) includes tentative upcoming features.

Follow plugin development on github at: https://github.com/geminilabs/site-reviews/

= Current Features =

- [new] Honeypot (spam trap) implemented in the submission form
- Actively developed and supported
- Assign reviews to a Post/Page ID
- Clean and easy-to-configure user interface
- Configurable Widgets
- Custom notifications (including Slack support)
- Easy setup and implementation
- Filter reviews by rating
- Helper functions to easily access review meta
- Logging
- MCE shortcode button dropdown
- Minimal widget styling (tested with all official WP themes)
- Publicly respond to a review
- Relative dates option
- Review avatars (gravatar.com)
- Review categories
- Review pagination
- Reviews Summary shortcode: [site_reviews_summary]
- Rich snippets for reviews (schema.org)
- Shortcodes: Display reviews in your post content and templates
- Translate any plugin text
- Use Google's Invisible reCAPTCHA on submission forms
- WordPress.org support
- WP Filter Hooks

== Installation ==

= Minimum plugin requirements =

- WordPress 4.0.0
- PHP 5.4

= Automatic installation =

Log in to your WordPress dashboard, navigate to the Plugins menu and click "Add New".

In the search field type "Site Reviews" and click Search Plugins. Once you have found the plugin you can view details about it such as the point release, rating and description. You can install it by simply clicking "Install Now".

= Manual installation =

Download the Site Reviews plugin and uploading it to your server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

All documentation and FAQ can be found in the "Get Help" page of the plugin.

== Screenshots ==

1. A view of the All Reviews page

2. A view of the Edit Review page

3. A view of the MCE shortcode dropdown button

4. A view of the Site Reviews &gt; Settings &gt; General page

5. A view of the Site Reviews &gt; Settings &gt; Translations page

6. A view of the Site Reviews &gt; Get Help &gt; Documentation tab

7. A view of the Site Reviews &gt; Get Help &gt; System Info tab

8. A view of the Recent Site Reviews widget settings

9. A view of the Submit a Site Review widget settings

10. How the Recent Site Reviews widget/shortcode looks like using the Twenty Sixteen WordPress theme

11. How the Submit a Site Review widget/shortcode looks like using the Twenty Sixteen WordPress theme

12. How the Site Reviews Summary shortcode looks like using the Twenty Sixteen WordPress theme

13. How the Slack notifications look like

14. Add-Ons are being built to extend the functionality on the Site Reviews plugin

== Changelog ==

= 2.5.2 (2017-08-21) =
- Fix plugin localization

= 2.5.1 (2017-08-10) =
- Added "site-reviews/validate/review/submission" hook

= 2.5.0 (2017-08-08) =
- [feature] Added a Honeypot (spam trap) to the submission form
- Fix Translator to use UTF-8 encoding when converting html entities

= 2.4.5 (2017-08-07) =
- Fix Translator to correctly handle htmlentities in plugin strings

= 2.4.3 (2017-07-29) =
- Fix a possible Translator bug
- Fix "Assigned To" input from updating page on Enter key
- Fix "hide_response" from showing unnecessarily with the TMCE button [site_reviews] shortcode
- Show plugin settings in system info

= 2.4.1 (2017-07-22) =
- Fix the schema URL for a page
- Update screenshots

= 2.4.0 (2017-07-05) =
- [feature] Publicly respond to a review
- Allow multi-line reviews

= 2.3.2 (2017-07-02) =
- Fix a possible translation error from occurring

= 2.3.1 (2017-06-30) =
- Fix hooks documentation

= 2.3.0 (2017-06-26) =
- [feature] Reviews Summary shortcode: [site_reviews_summary]
- [feature] Relative dates option
- [feature] Rich snippets for reviews (schema.org)
- [feature] Translate any plugin text
- [changed] The default minimum rating for displaying reviews has been changed to 1 (instead of 5)
- Added "show more" links to review excerpts
- Extended "assign_to" and "assigned_to" attributes in the widgets and shortcodes to accept "post_id" as a value which automatically equals the current post ID
- Removed "Submission Form" custom text options (replaced by the new Translation options)
- Fix tinymce shortcode dialog tooltips

= 2.2.3 (2017-05-07) =
- Added option to change submit button text

= 2.2.2 (2017-05-06) =
- Added JS event that is triggered on form submission response (site-reviews/after/submission)
- Fix form submission without ajax

= 2.2.1 (2017-05-06) =
- Added hook that runs immediately after a review has successfully been submitted (site-reviews/local/review/submitted)
- Use new IP detection when submitting a review

= 2.2.0 (2017-05-03) =
- [feature] use Google's Invisible reCAPTCHA on submission forms

= 2.1.8 (2017-04-19) =
- Fix [site_reviews] shortcode pagination option
- Fix possible JS race condition which breaks the star rating functionality

= 2.1.6 (2017-04-02) =
- Fix the category feature to work properly when a user was not logged in
- Corectly remove the "create_site-review" capability

= 2.1.3 (2017-04-01) =
- Changed capability requirement to "edit_others_pages"

= 2.1.1 (2017-03-21) =
- Fixed a bug causing reviews to not load correctly introduced by v2.1.0 (sorry!)

= 2.1.0 (2017-03-19) =

- [feature] Assign reviews to a page/post
- [deprecated] The 'post_id' review key is deprecated in favour of 'ID' in reviews returned from the `glsr_get_review()` and `glsr_get_review()` functions
- Added hook that runs immediately after a review has been created

= 2.0.4 (2017-03-09) =

- Fix WordPress customizer compatibility (see: https://codex.wordpress.org/Function_Reference/get_current_screen#Usage_Restrictions)

= 2.0.3 (2017-03-09) =

- Fix incorrect plugin update check

= 2.0.2 (2017-01-24) =

- Added hook to filter metabox details

= 2.0.1 (2017-01-23) =

- Prevent the taxonomy object from containing recursion

= 2.0.0 (2017-01-12) =

- [feature] Helper functions to easily access review meta
- [feature] MCE shortcode button dropdown
- [feature] Review avatars (gravatar.com)
- [feature] Review categories
- [breaking] Changed internal widget/shortcode hook names
- [breaking] Changed shortcode variables
- [breaking] Consolidated all plugin settings into a single setting variable
- Ajaxified approve/unapprove
- Custom Published/Pending labels
- New settings page for reviews
- Removed "site-reviews/reviews/excerpt_length" filter hook
- Removed "site-reviews/reviews/use_excerpt" filter hook

= 1.2.2 (2017-01-06) =

- Added hook to change excerpt length
- Added hook to disable the excerpt and instead display the full review content

= 1.2.1 (2016-12-28) =

- Fix PHP 5.4 compatibility regression

= 1.2.0 (2016-12-27) =

- [feature] Send notifications to Slack
- Fix notifications to use the email template setting

= 1.1.1 (2016-12-05) =

- Remove ".star-rating" class on frontend which conflicts with the woocommerce plugin CSS
- Added hooks to modify rendered fields/partials HTML

= 1.1.0 (2016-11-16) =

- [feature] Pagination
- [breaking] Changed internal widget hook names
- [breaking] Changed text-domain to "site-reviews"
- Set post_meta defaults when creating a review
- [addon support] Display read-only reviews
- [addon support] Display widget link options (conditional field)
- [addon support] Show all review types by default in widgets and shortcodes

= 1.0.4 (2016-11-14) =

- use the logged-in user's display_name by default instead of "Anonymous" when submitting reviews
- Fix shortcodes to insert in the_content correctly

= 1.0.3 (2016-11-09) =

- Updated plugin description
- Fix plain-text emails
- Fix inconsistencies with plugin settings form fields
- Fix internal add-on integration code

= 1.0.2 (2016-10-24) =

- Set widget and settings defaults
- Fix PHP error that is thrown when settings have not yet been saved to DB

= 1.0.0 (2016-10-21) =

- Initial plugin release
