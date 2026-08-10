# Security and Disclosure Boundaries

## Included

- Laravel Sanctum middleware boundary on all 47 business operations
- Generic user model and default token table required to demonstrate authenticated tests
- Synthetic factories and demonstration seed records
- Default configuration templates that read values from environment variables
- Business validation, safe deletion rules, and stable error responses

## Intentionally excluded

- Login, logout, registration, password reset, OTP, session management, and token issuance APIs
- Role, permission, policy, and custom authorization implementation
- Tenant or company switching and request-context resolution
- Real users, customer records, employee records, suppliers, contracts, and documents
- Attendance, leave, payroll, compensation, and employee performance
- Chart of accounts, journal entries, invoices, payments, cash, bank, tax, and financial reports
- Stock documents, receipts, issues, transfers, FIFO/cost layers, reconciliation, and valuation
- Product import/export, attachments, images, external publishing, reports, and account mappings
- API credentials, database credentials, mail credentials, storage keys, signing keys, webhooks, and integration endpoints
- Production `.env`, logs, caches, backups, generated files, and database dumps

## Data handling

All included seed and factory values are fictional. `reviewer@example.test` uses the reserved `.test` domain, and its factory password is a randomly generated value that is not disclosed. `.env.example` contains blanks or local placeholders only. No `.env` file is included.

## Integration responsibilities

The receiving team must supply authentication, authorization, tenancy enforcement, secret management, audit-log retention, rate limiting, monitoring, backup, and deployment controls. Passing Sanctum authentication alone is not sufficient authorization for a production ERP.

## Safe review checklist

- Review the ZIP contents before forwarding.
- Keep it separate from production repositories and data stores.
- Do not add real secrets to `.env.example`.
- Treat synthetic tokens or users created during local testing as disposable.
