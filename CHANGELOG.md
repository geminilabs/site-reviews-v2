# Change Log

All notable changes to Site Reviews will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added
- [feature] MCE shortcode button dropdown
- [feature] Review avatars (gravatar.com)
- [feature] Review categories
- Added option to choose the excerpt length
- Added option to enable excerpts
- Custom Published/Pending labels

### Changed

### Deprecated

### Removed

### Fixed

### Security

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
