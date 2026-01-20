# Changelog

All notable changes to this package will be documented in this file.

The format is inspired by [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to Semantic Versioning **SemVer**.

---

## [Unreleased]
### Ajouté
- (exemple) Support de fichiers supplémentaires

### Modifié
- (exemple) Amélioration des performances dans les relations

### Supprimé
- (exemple) Retrait d’une méthode dépréciée

---

## [0.2.0] – 2026-01-20

### Breaking change – Branching rules normalization

This release introduces a **canonical format for branching rules** and simplifies
the branching engine accordingly.

This change affects how conditional questions (branching logic) are evaluated
and stored, in order to guarantee consistent behavior across preview, validation
and form rendering.

#### What changed

- The `BranchingEvaluator` now supports **one single canonical format** for the `then` clause of branching rules.
- The engine is now **strict, deterministic and UI-agnostic**.
- Questions targeted by a `show` rule are now **hidden by default**, and only become visible when the condition is met.
- `hide` rules are now enforced deterministically and always take precedence over `show`.
- When both `show` and `hide` apply to the same question, **`hide` always has priority**.

### Canonical branching rule format

Branching rules must now be stored using the following **canonical format**:

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

The following UI-driven format is no longer supported by the engine and must
not be persisted:

```json
{
  "then": {
    "action": "show",
    "targets": ["Qy"]
  }  
}
```

UI layers (e.g. Filament RelationManagers) must normalize data before saving.

## [0.1.4] – 2026-01-14
- Improve branching evaluator with visibility of items

## [0.1.3] – 2026-01-06
- Added is_required boolean column to form items
- Allows simple required flag instead of validation rule
- Existing items default to non-required

## [0.1.2] – 2025-09-21

## [0.1.1] – 2025-09-09

## [0.1.0] – 2025-09-09  

### Firts stable release
- Laravel 10 & 11 compatible
