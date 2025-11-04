# Changelog

All notable changes to `laravel-forge-logs` will be documented in this file.

## v1.17 - Delete Logs Command - 2025-11-04

### New Features

#### Delete Site Logs Command

Added a new interactive Artisan command to delete logs on Laravel Forge:

```bash
php artisan forge-delete-logs

```
**Features:**

- Interactive menu to select which logs to delete
- Support for site/application logs, nginx access logs, nginx error logs, and all three
- Confirmation prompt before deletion (skippable with `--force` flag)
- Integration with Laravel Forge API
- Detailed success/failure feedback
- Uses Laravel Prompts for clean UX (like `forge-init`)

**Usage:**

```bash
# Interactive menu
php artisan forge-delete-logs

# Skip confirmation
php artisan forge-delete-logs --force

```
**API Endpoints:**

- DELETE `/orgs/{org}/servers/{server}/sites/{site}/logs/application`
- DELETE `/orgs/{org}/servers/{server}/sites/{site}/logs/nginx-access`
- DELETE `/orgs/{org}/servers/{server}/sites/{site}/logs/nginx-error`

See [CHANGELOG_DELETE_LOGS.md](CHANGELOG_DELETE_LOGS.md) for comprehensive documentation.

### Technical Details

- Added `deleteSiteLog()`, `deleteNginxAccessLog()`, `deleteNginxErrorLog()` methods to ForgeApiService
- Added corresponding methods to ForgeLogService with batch operation support
- New LaravelForgeDeleteLogsCommand with Laravel Prompts integration
- Full type hints and error handling
- 100% backward compatible

### Quality Assurance

✅ Code formatted with Pint
✅ PHPStan static analysis: No errors
✅ All tests passing

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
