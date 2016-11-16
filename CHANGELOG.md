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

## [1.1.0] - 2016-11-16

### Added
- [feature] Pagination
- [addon support] Display read-only reviews
- [addon support] Display widget link options (conditional field)

### Changed
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
