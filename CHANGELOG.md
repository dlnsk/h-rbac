# Changelog

All Notable changes to `h-rbac` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [1.1.0] - 2025-05-22
### Changed
- getUserRoles() now rises UserHasNoBuiltInRolesException. Backward compatible if you don't use this function directly

### Added
- HRBACHelper class added and gives some useful information

## [1.0.0] - 2024-05-14
### Changed
- The module was fully rewritten with policy support

### Added
- Ability to use user's extra permission from DB

## [0.4.3] - 2023-12-08
### Fixed
- Small type fix

## [0.4.2] - 2020-01-15
### Added
- Allow to configure single-role attribute of model

### Fixed
- Use own autorization class for tests

## [0.4.1] - 2020-01-15
### Fixed
- Fix getting wrong roles from example file

## [0.4.0] - 2020-01-15
### Added
- Support for multiply roles (many-to-many relationship). Backward compatible.
- Covered by tests.

## [0.3.4] - 2019-10-24
### Changed
- Since Laravel 5.7 helpers was changed

## [0.1] - 2016-03-14
### Added
- Initial version
