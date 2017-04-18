# Change Log

All notable changes to Site Reviews will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed

### Security

## [2.1.7] = 2017-04-18

### Fixed
- Fix [site_reviews] shortcode pagination option
- Fix possible JS race condition which breaks the star rating functionality

## [2.1.6] = 2017-04-02

### Fixed
- Fix the category feature to work properly when a user was not logged in
- Corectly remove the "create_site-review" capability

## [2.1.3] = 2017-04-01

### Changed
- Changed capability requirement to "edit_others_pages"

## [2.1.1] = 2017-03-21

### Fixed
- Fixed a bug causing reviews to not load correctly introduced by v2.1.0 (sorry!)

## [2.1.0] = 2017-03-19

### Added
- [feature] Assign reviews to a page/post
- Added hook that runs immediately after a review has been created

### Deprecated
- The 'post_id' review key is deprecated in favour of 'ID' in reviews returned from the `glsr_get_review()` and `glsr_get_review()` functions

## [2.0.4] - 2017-03-09

### Fixed
- Fix WordPress customizer compatibility (see: [`get_current_screen()` usage restrictions](https://codex.wordpress.org/Function_Reference/get_current_screen#Usage_Restrictions))

## [2.0.3] - 2017-03-09

### Fixed
- Fix incorrect plugin update check

## [2.0.2] - 2017-01-24

### Added
- Added hook to filter metabox details

## [2.0.1] - 2017-01-23

### Fixed
- Prevent the taxonomy object from containing recursion

## [2.0.0] - 2017-01-12

### Added
- [feature] Helper functions to easily access review meta
- [feature] MCE shortcode button dropdown
- [feature] Review avatars (gravatar.com)
- [feature] Review categories
- Ajaxified approve/unapprove
- Custom Published/Pending labels
- New settings page for reviews

### Changed
- [breaking] Changed internal widget/shortcode hook names
- [breaking] Changed shortcode variables
- [breaking] Consolidated all plugin settings into a single setting variable

### Removed
- Removed "site-reviews/reviews/excerpt_length" filter hook
- Removed "site-reviews/reviews/use_excerpt" filter hook

## [1.2.2] - 2017-01-06

### Added
- Added hook to change excerpt length
- Added hook to disable the excerpt and instead display the full review content

## [1.2.1] - 2016-12-28

### Fixed
- Fix PHP 5.4 compatibility regression

## [1.2.0] - 2016-12-27

### Added
- [feature] Send notifications to Slack

### Fixed
- Fix notifications to use the email template setting

## [1.1.1] - 2016-12-05

### Added
- Added hooks to modify rendered fields/partials HTML

### Changed
- Remove ".star-rating" class on frontend which conflicts with the woocommerce plugin CSS

## [1.1.0] - 2016-11-16

### Added
- [feature] Pagination
- [addon support] Display read-only reviews
- [addon support] Display widget link options (conditional field)

### Changed
- [breaking] Changed internal widget hook names
- [breaking] Changed text-domain to "site-reviews"
- [addon support] Show all review types by default in widgets and shortcodes

### Fixed
- Set post_meta defaults when creating a review

## [1.0.4] - 2016-11-14

### Changed
- Use the logged-in user's display_name by default instead of "Anonymous" when submitting reviews

### Fixed
- Fix shortcodes to insert in the_content correctly

## [1.0.3] - 2016-11-09

### Added
- [addon support] Internal add-on integration

### Changed
- Updated plugin description

### Fixed
- Do not wrap hidden form inputs with HTML
- Fix plain-text emails
- Fix form field values with a falsey attribute
- Prevent a possible infinite recursion loop when setting default settings

## [1.0.2] - 2016-10-24

### Changed
- Set widget and settings defaults

### Fixed
- Fix PHP error that is thrown when settings have not yet been saved to DB

## [1.0.1] - 2016-10-21

### Fixed
- Fix WP screenshots.

## [1.0.0] - 2016-10-21
- Initial release
