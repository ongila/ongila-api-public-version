# ONGILA ERP Sample

This is a deliberately limited ONGILA API sample

## Included scope

| ERP area | Features | Business operations |
| --- | --- | ---: |
| Inventory and catalog | Categories, units, warehouses, restricted product catalog | 20 |
| Human resources | Shifts, holidays, yearly work calendar | 15 |
| Finance reference data | Currencies and exchange rates | 12 |

The API also has one public health endpoint.

## Code layout

- `app/Http/Controllers/Api`: thin HTTP controllers
- `app/Http/Requests`: validation and input normalization
- `app/Http/Resources`: stable API response mapping and optional field selection
- `app/Services`: transactions and ERP business rules
- `app/Models`: Eloquent models and relationships
- `database/migrations`: self-contained schema for the selected features
- `database/factories` and `database/seeders`: synthetic demonstration data only
- `tests/Feature`: API and business-rule regression examples
- `docs`: API catalogue, audit decisions, security boundary, and verification record

## Quick start

Requirements: PHP 8.0+, Composer 2, and a supported Laravel database. PostgreSQL is the default in `.env.example`; the tests use in-memory SQLite.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Run the test suite:

```bash
composer test
```

All business routes are protected by `auth:sanctum`. This package deliberately does not include registration, login, password reset, OTP, role management, or tenant-selection endpoints. Integrate the sample with the reviewing environment's identity provider or use `Sanctum::actingAs(...)` in tests.

## API conventions

- Base path: `/api/organization`
- JSON response envelope: `data`
- Validation errors: HTTP 422
- Missing records: HTTP 404
- Business-rule conflicts: HTTP 409 with `message` and stable `error_code`
- List query options: `pagination`, `page`, `rows`, `s`, `sort`, feature filters, `only`, `except`, and `language_code`
- A sort prefixed by `-` is descending, for example `sort=-created_at`.

Detailed endpoints and examples are in [docs/API.md](docs/API.md). Audit scope and exclusions are documented in [docs/AUDIT_NOTES.md](docs/AUDIT_NOTES.md) and [docs/SECURITY_BOUNDARIES.md](docs/SECURITY_BOUNDARIES.md).

## Important status

This package is limited example, not a complete deployment artifact. It contains default Laravel configuration templates and synthetic seed records, but no production environment file, secret, access token, customer data, or operational configuration.
