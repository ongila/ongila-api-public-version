# Audit Notes

## Purpose and provenance

This package is a focused example extracted conceptually from the legacy ONGILA Laravel backend. It preserves representative ERP domain behavior while replacing project-wide coupling with a small, reviewable Laravel 8 application. Names and structures needed to explain the selected features remain; production data and sensitive implementation areas do not.

The original source archive was not modified. This directory is a separately built review package.

## Selection rationale

The included features are recognizable ERP reference and master-data workflows with useful business rules, but relatively low disclosure risk:

1. Inventory master data and a restricted product catalogue
2. HR shifts, recurring holidays, and yearly work-calendar generation
3. Currency reference data, dated exchange-rate history, and cross-rate calculation

They demonstrate CRUD, validation, localization, hierarchy rules, date generation, period handling, database transactions, aggregate stock protection, query filtering, and API resource contracts.

## Improvements made for review

- Replaced large controller methods with request, controller, service, model, and resource layers.
- Added explicit allow-lists for filtering, searching, sorting, and response-field selection.
- Added database transactions around multi-table translation and calendar operations.
- Added stable HTTP 409 domain errors instead of leaking database exceptions.
- Added category-cycle detection and deletion guards for referenced master data.
- Added stock and reservation guards for product and warehouse deletion.
- Added currency-code normalization before validation and persistence.
- Added referential constraints for product and rate currency codes.
- Removed the unsafe implicit 1:1 fallback when an exchange rate is missing.
- Added non-overlapping effective-rate period rebuilding, reverse lookup, base cross-rate calculation, and previous-rate comparison.
- Added deterministic synthetic seed data and model factories.
- Added feature tests around high-value rules and authentication boundaries.
- Restored standard Laravel configuration files with environment placeholders only.

## Deliberate simplifications

- Authorization policies and permissions are not shown because the legacy role model is sensitive and application-specific. The integration boundary is `auth:sanctum`.
- Company is a minimal warehouse reference, not a complete organization or tenant module.
- Product stock is present only to demonstrate safe deletion. Stock movement and costing are excluded.
- Holidays recur by month and day (`MM-DD`); jurisdiction, half-day, and observed-day rules are not included.
- Exchange rates use the configured base currency and one active period per pair. Central-bank imports and accounting revaluation are excluded.
- `PUT` is treated as full replacement for the fields defined by each request class.

## Review guidance

The sample should be assessed as evidence of representative coding patterns, not as proof that every production workflow uses the same implementation. Before adopting any portion in production, map it to current Laravel/PHP versions, the real identity and tenancy model, operational database conventions, and the organization's test and deployment pipeline.
