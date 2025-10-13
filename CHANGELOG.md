# Changelog

All notable changes to `laravel-forge-logs` will be documented in this file.

## v1.16 - 2025-10-08

### Added
- Nginx access logs fetching via `forge-nginx-access-logs` command
- Nginx error logs fetching via `forge-nginx-error-logs` command
- Unified log fetching command `forge-fetch-logs` (with `forge-all-logs` alias) that fetches all logs in sequence
- Customizable log file paths and filenames via configuration
- New `log_paths` configuration array for specifying custom storage paths

### Changed
- Renamed `forge-fetch-logs` to `forge-laravel-logs` for clarity
- Renamed `forge-nginx-logs` to `forge-nginx-access-logs` for clarity
- Updated `forge-fetch-logs` command to fetch all three log types (Laravel, Nginx access, Nginx error)
- Removed hardcoded `version` field from composer.json to properly use Git tags

### Fixed
- Removed non-existent `type` option from `forge-laravel-logs` command

## v1.10 - 2025-10-08

**Full Changelog**: https://github.com/FrankFlow/laravel-forge-logs/compare/v1.9...v1.10

## v1.9 - 2025-10-08

### Changes

- Fix PHPstan issues

### What's Changed

- Fixed PHPstan analysis errors

**Full Changelog**: https://github.com/FrankFlow/laravel-forge-logs/compare/v1.8...v1.9
