# API Catalogue

All paths below are relative to `/api` and return JSON. The 47 business operations require a valid Laravel Sanctum identity.

## Endpoint summary

| ERP feature | Methods and paths | Operations |
| --- | --- | ---: |
| Category | `GET, POST organization/wh/category`; `GET, PUT, DELETE organization/wh/category/{id}` | 5 |
| Unit | `GET, POST organization/wh/unit`; `GET, PUT, DELETE organization/wh/unit/{id}` | 5 |
| Warehouse | `GET, POST organization/wh/warehouse`; `GET, PUT, DELETE organization/wh/warehouse/{id}` | 5 |
| Product | `GET, POST organization/wh/product`; `GET, PUT, DELETE organization/wh/product/{id}` | 5 |
| Shift | `GET, POST organization/hr/shift`; `GET, PUT, DELETE organization/hr/shift/{id}` | 5 |
| Holiday | `GET, POST organization/hr/holiday`; `GET, PUT, DELETE organization/hr/holiday/{id}` | 5 |
| Yearly calendar | `GET organization/hr/yearly-calendar`; `GET organization/hr/yearly-calendar/{year}`; `GET organization/hr/get-month/{date}`; `PUT organization/hr/yearly-calendar/{dayId}`; `PUT organization/hr/generate-calendar/{year}` | 5 |
| Currency | `GET, POST organization/fi/currency`; `GET, PUT, DELETE organization/fi/currency/{id}` | 5 |
| Currency rate | `GET, POST organization/currency-rate`; `GET, PUT, DELETE organization/currency-rate/{id}`; `GET organization/exchange-rate`; `GET organization/last-exchange-rate` | 7 |
| **Total** |  | **47** |

`GET /api/health` is public and is not part of the business-operation count.

## Common list queries

List endpoints accept:

| Query | Meaning |
| --- | --- |
| `pagination=0` | Return the full filtered collection instead of a paginator |
| `page=2&rows=20` | Select a page; page size is capped at 100 |
| `s=chair` | Search only an allow-listed set of fields |
| `sort=-created_at` | Sort descending; unsupported sort columns fall back to `id` |
| `only=id,name` | Return only selected resource fields |
| `except=created_at,updated_at` | Remove selected resource fields |
| `language_code=en` | Choose a translated display value, with configured fallback |

Feature-specific filters include `parent_id`, `category_id`, `unit_id`, `currency_code`, `company_id`, `type`, `is_market_visible`, `is_published`, `status_id`, `from_currency`, and `to_currency`.

## Selected request examples

### Create a category

```http
POST /api/organization/wh/category
Authorization: Bearer <token>
Content-Type: application/json

{
  "parent_id": null,
  "double_unit": false,
  "translations": [
    {"language_code": "en", "name": "Office Equipment"},
    {"language_code": "uz", "name": "Ofis jihozlari"}
  ]
}
```

The service synchronizes translations in one database transaction. Updating a category beneath itself or a descendant returns `409 category_cycle`.

### Create a product

```json
{
  "name": "Ergonomic Office Chair",
  "model": "CHAIR-001",
  "code": "PRD-001",
  "expiration_days": 0,
  "category_id": 1,
  "unit_id": 1,
  "currency_code": "UZS",
  "price": 1500000,
  "buy_price": 1100000,
  "min_stock": 2,
  "max_stock": 20,
  "is_published": true
}
```

This sample intentionally omits product files, import/export, reports, account mappings, stock movements, costing, and reconciliation. A product with positive stock or reservations cannot be deleted.

### Generate a work calendar

```http
PUT /api/organization/hr/generate-calendar/2028
```

The generator uses `ERP_WORK_DAYS`, active recurring holidays, and a transaction. It will not overwrite an already generated year. `GET /api/organization/hr/get-month/2028-01-15` returns `days`, `work_days`, `non_work_days`, `weekend_days`, and `holidays`.

### Create an exchange rate

The configured base currency defaults to `UZS`.

```json
{
  "to_currency": "USD",
  "value": 0.000075,
  "main_value": 1,
  "begin_date": "2025-02-01 00:00:00"
}
```

Alternatively, set `value` to `1` and provide the base amount in `main_value`; the service stores the inverse pair direction. Creating, moving, or deleting a dated rate rebuilds the pair's non-overlapping effective periods. Calculated conversions raise `409 currency_rate_not_found` instead of silently assuming parity; the lookup-only `last-exchange-rate` endpoint returns null amounts when no direct pair exists.

Direct or reverse lookup:

```http
GET /api/organization/last-exchange-rate?from_currency=USD&to_currency=UZS&date=2025-03-01
```

Current base-currency rates:

```http
GET /api/organization/exchange-rate
```

## Conflict codes

| Error code | Rule |
| --- | --- |
| `category_cycle` | Category hierarchy would become cyclic |
| `category_in_use` | Category has children or assigned products |
| `unit_in_use` | Unit is assigned to products |
| `product_has_stock` | Product has stock or reservations |
| `warehouse_has_stock` | Warehouse has stock or reservations |
| `currency_in_use` | Currency is referenced |
| `currency_code_in_use` | Referenced currency code cannot change |
| `base_currency_protected` | Configured base currency cannot be renamed or deleted |
| `base_currency_missing` | Configured base currency record has not been created |
| `same_currency` | Rate target equals base currency |
| `invalid_currency_ratio` | Neither submitted side represents one unit |
| `currency_rate_date_exists` | A pair already has a rate at the submitted start time |
| `currency_rate_not_found` | No direct, reverse, or base cross-rate exists |
| `holiday_in_use` | Holiday is referenced by a generated calendar |
| `holiday_date_in_use` | A referenced holiday's recurring date cannot change |
| `calendar_exists` | Requested year is already generated |
| `invalid_year` | Requested year is outside 2000–2100 |
