# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.0] - 2026-10-30

### Added

- Laravel 13 support.
- PHP 8.3 and 8.4 officially supported and tested.
- Filament 4 and 5 support for the optional `FilamentFormRenderer`.
- `FormItem::isRequired()`: true when `is_required` is set or
  `validation.required` is `true`.
- The configured renderer is validated: a class that does not implement
  `RendersForm` throws an `InvalidArgumentException`.
- GitHub Actions workflow (PHP × Laravel matrix, PHPStan, Pint,
  `composer audit`) and Dependabot for the workflow actions.
- PHPStan configuration (level 5) and PHPDoc annotations on all models.
- Test suite rewritten and extended: branching operators and precedence,
  validation rules (checked against Laravel's validator), models, service
  provider, migration publishing and the Filament renderer.
- Complete README.

### Changed

- **Minimum Laravel version is now 12.** Laravel 11 is end-of-life and its
  releases are blocked by Composer's security-advisory policy.
- `ValidationRuleBuilder` adds `numeric`, `date` and `array` rules to number,
  date and checkbox items, so that `min` / `max` compare values instead of
  string lengths.
- `php` constraint narrowed from `>=8.2` to `^8.2`; `illuminate/filesystem`
  declared as a dependency.
- Development dependencies aligned on Laravel 12/13: Testbench 10/11,
  Pest 3/4, PHPUnit 11.5/12, Larastan 3.
- Tests run the real migration stubs instead of a copy.
- Publishing is only registered when running in the console.
- `BranchingEvaluator::isValidCondition()` (protected) inlined.

### Fixed

- **Fresh installs could fail to migrate.** Both migrations were published with
  the same timestamp and ran in alphabetical order, so the `is_required`
  migration ran before the tables existed. Each migration now gets its own
  timestamp, in the right order.
- `hide` now always takes precedence over `show`, as documented in 1.0.0.
  Previously the result depended on the order of the rules.
- The `is_required` column was ignored by `ValidationRuleBuilder` and
  `FilamentFormRenderer` (only `validation.required` was used), and the Filament
  renderer applied `required` to text fields only.
- The `in` operator with a list of values (`"value": ["a", "b"]`) triggered an
  "Array to string conversion" warning and never matched.
- Generated form keys accumulated suffixes (`title_1_2`) instead of
  incrementing (`title_2`).
- `HasDynamicForm` ignored the `answer_set` model configured in
  `formbuilder.models`.
- `FilamentFormRenderer` no longer throws on an item without a type.
- Relations declare their foreign keys explicitly, so subclasses with another
  class name keep working.

### Removed

- Laravel 11 support.
- Empty `routes/web.php` and its registration.
- Unused `$tableConfigKey` property of `UsesConfiguredTable`.
- `composer.lock` and `.phpunit.result.cache` from the repository.

## [1.1.0] - 2026-06-19

### Added

- Official support for Laravel 12.
- MIT license.
- Comprehensive project documentation.
- PHPStan static analysis support.
- Laravel Pint code style validation.
- Improved Pest test suite.
- GitHub Actions ready CI workflow.
- Improved package metadata and Composer configuration.

### Changed

- Updated Composer constraints for Laravel 11 and 12.
- Updated development dependencies.
- Standardized code style across the entire package.
- Improved maintainability and long-term compatibility.
- Improved test infrastructure using Orchestra Testbench.
- Improved package structure and documentation.

### Fixed

- Fixed various static analysis issues reported by PHPStan.
- Fixed test suite bootstrapping and compatibility issues.
- Fixed Laravel 12 compatibility problems.
- Fixed minor code style inconsistencies.

### Quality

- PHPStan analysis passes without errors.
- Pest test suite fully passing.
- Package validated for Laravel 11 and 12.

## [1.0.0] - 2026-01-20

### Added

- Canonical branching rule format.
- Deterministic branching engine.
- Support for conditional question visibility.
- Support for explicit show/hide actions.
- Consistent branching behavior across rendering, preview and validation.

### Changed

- Branching rules now use a single canonical structure.
- Questions targeted by a `show` rule are hidden by default.
- `hide` rules always take precedence over `show`.
- Branching engine is now UI-agnostic.

### Removed

- Legacy UI-driven branching rule persistence format.

### Breaking Changes

- Branching rules must now be stored using the canonical format:

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

## [0.1.4] - 2026-01-14

### Added

- Visibility support in the branching evaluator.

## [0.1.3] - 2026-01-06

### Added

- `is_required` column on form items.
- Simple required flag support without custom validation rules.

## [0.1.2] - 2025-09-21

### Fixed

- Internal improvements and bug fixes.

## [0.1.1] - 2025-09-09

### Fixed

- Initial stabilization release.

## [0.1.0] - 2025-09-09

### Added

- Initial public release.
- Dynamic form engine.
- Form items and answer sets.
- Branching rules.
- Validation rule builder.
- Laravel 10 and 11 compatibility.
- Filament renderer integration.

[Unreleased]: https://github.com/laurentmeuwly/formbuilder/compare/v1.2.0...HEAD
[1.2.0]: https://github.com/laurentmeuwly/formbuilder/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/laurentmeuwly/formbuilder/compare/v1.0.0...v1.1.0
