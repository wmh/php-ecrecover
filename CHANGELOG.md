# Changelog

All notable changes to this project will be documented in this file.

## [2.0.0] - 2025-12-03

### 🎉 Major Upgrade - Modernization Release

This release completely modernizes the codebase to 2024 standards while maintaining backward compatibility through a migration path.

### Added
- Modern PSR-4 autoloading with proper namespace (`Wmh\EcRecover`)
- PHP 8.1+ type declarations (strict types, typed properties, return types)
- PHPUnit 10 test suite with full coverage
- GitHub Actions CI/CD pipeline
- Comprehensive API documentation
- Modern example files with clear usage patterns
- Composer autoload configuration
- `.gitignore` with common PHP patterns

### Changed
- **BREAKING**: Minimum PHP version increased from 7.1 to 8.1
- **BREAKING**: Functions converted to static methods on `EcRecover` class
- **BREAKING**: Function names changed:
  - `ecRecover()` → `EcRecover::recover()`
  - `personal_ecRecover()` → `EcRecover::personalRecover()`
  - `keccak256()` → `EcRecover::keccak256()`
- Updated `kornrunner/keccak` dependency to ^1.1
- Reorganized code structure:
  - Core logic moved to `src/` directory
  - Tests moved to `tests/` directory
  - Examples moved to `example.php`
- Enhanced README with modern documentation and migration guide
- Improved code quality with strict typing and better error handling

### Improved
- Better error messages and exception handling
- More efficient elliptic curve operations
- Cleaner, more maintainable code structure
- Better separation of concerns

### Deprecated
- Old procedural functions in `ecrecover_helper.php` (still available but deprecated)
- Old `index.php` example file (use `example.php` instead)

### Migration Guide
See README.md for detailed migration instructions from v1.x to v2.0.

## [1.0.0] - 2018 (Estimated)

### Initial Release
- Basic ECRecover functionality
- Support for eth_sign, personal_sign, and eth_signTypedData
- PHP 7.1+ support
- Integration with CryptoCurrencyPHP submodule
