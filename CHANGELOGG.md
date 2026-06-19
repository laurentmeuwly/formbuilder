# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and this project adheres to **Semantic Versioning (SemVer)**.

---

## [Unreleased]

### Added
### Changed
### Fixed
### Removed

---

## [1.1.0] – 2026-06-19

### Added

* Official support for Laravel 12.
* MIT license.
* Comprehensive project documentation.
* PHPStan static analysis support.
* Laravel Pint code style validation.
* Improved Pest test suite.
* GitHub Actions ready CI workflow.
* Improved package metadata and Composer configuration.

### Changed

* Updated Composer constraints for Laravel 11 and 12.
* Updated development dependencies.
* Standardized code style across the entire package.
* Improved maintainability and long-term compatibility.
* Improved test infrastructure using Orchestra Testbench.
* Improved package structure and documentation.

### Fixed

* Fixed various static analysis issues reported by PHPStan.
* Fixed test suite bootstrapping and compatibility issues.
* Fixed Laravel 12 compatibility problems.
* Fixed minor code style inconsistencies.

### Quality

* PHPStan analysis passes without errors.
* Pest test suite fully passing.
* Package validated for Laravel 11 and 12.

---

## [1.0.0] – 2026-01-20

### Added

* Canonical branching rule format.
* Deterministic branching engine.
* Support for conditional question visibility.
* Support for explicit show/hide actions.
* Consistent branching behavior across rendering, preview and validation.

### Changed

* Branching rules now use a single canonical structure.
* Questions targeted by a `show` rule are hidden by default.
* `hide` rules always take precedence over `show`.
* Branching engine is now UI-agnostic.

### Removed

* Legacy UI-driven branching rule persistence format.

### Breaking Changes

* Branching rules must now be stored using the canonical format:

```json
{
  "if": {
    "field": "Qx",
    "op": "=",
    "value": "other"
  },
  "then": {
    "show": ["Qy", "Qz"]
  }
}
```

The previous format based on `action` and `targets` is no longer supported.

---

## [0.1.4] – 2026-01-14

### Added

* Visibility support in the branching evaluator.

---

## [0.1.3] – 2026-01-06

### Added

* `is_required` column on form items.
* Simple required flag support without custom validation rules.

---

## [0.1.2] – 2025-09-21

### Fixed

* Internal improvements and bug fixes.

---

## [0.1.1] – 2025-09-09

### Fixed

* Initial stabilization release.

---

## [0.1.0] – 2025-09-09

### Added

* Initial public release.
* Dynamic form engine.
* Form items and answer sets.
* Branching rules.
* Validation rule builder.
* Laravel 10 and 11 compatibility.
* Filament renderer integration.
