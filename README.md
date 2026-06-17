# Symfony DDD demo — loan eligibility service

[![CI](https://github.com/selivanov-tech/symfony-ddd-demo/actions/workflows/ci.yml/badge.svg)](https://github.com/selivanov-tech/symfony-ddd-demo/actions/workflows/ci.yml)

A small **Symfony 7.1** backend that shows a clean **Domain-Driven Design** layout.
It models a simple lending flow: create customers, define loan products, and check
whether a customer is eligible for a loan.

This is a reference / demo project. The goal is to show architecture and code style,
not to be a finished product.

## What it does

- **Customers** — create, read, and update a customer (email, phone, SSN, birthday,
  FICO score, monthly income, US address).
- **Loan products** — each product has its own rules: minimum FICO score, minimum
  monthly income, an age range, allowed US states, and per-state score multipliers.
- **Loan eligibility** — apply for a loan against a product. A domain service checks
  all the rules and returns *approved* or *denied* with a clear reason.
- **Events & notifications** — a processed loan request fires a domain event; a
  subscriber then sends a notification (multi-channel, e.g. email / SMS).

## Architecture

Layered DDD with a one-way dependency direction (Infrastructure → Application → Domain):

```
src/
├── Domain/          # business core — no framework code
│   ├── Customer/    # Entity, Address value object, Factory, repository interface
│   ├── Loan/        # Entity, LoanEligibilityChecker service, rules, exceptions
│   ├── Product/     # Entity, state score-multiplier value objects
│   └── Shared/      # shared traits (UUID id)
├── Application/     # use cases — services, DTOs, requests, events
│   └── Service/     # CustomerCreator/Editor/Getter, LoanApplier, notifications
└── Infrastructure/  # framework & I/O
    ├── Http/Controller/      # Customer, Loan, Test controllers (attribute routes)
    ├── Persistence/Doctrine/ # Doctrine repositories (implement domain interfaces)
    ├── Event/Subscriber/     # API + Loan event subscribers
    └── Service/Notification/
```

- **Domain** holds the business rules. Its core logic has no Symfony or Doctrine code.
  Repository **interfaces** live here; the Doctrine implementations live in Infrastructure.
- **Application** orchestrates the use cases (e.g. `LoanApplier`) and dispatches events.
- **Infrastructure** wires HTTP, Doctrine, and notifications to the application.

## Tech stack

- PHP 8.2+ · Symfony 7.1 (framework-bundle, serializer, validator, uid, notifier)
- Doctrine ORM 3 + migrations
- SQLite by default (PostgreSQL-ready — see `DATABASE_URL` in `.env`)
- Docker + Docker Compose, with `make` helpers
- Quality: PHPUnit (unit + feature), PHPStan (level 6), PHP-CS-Fixer, GitHub Actions CI

## Run it

```bash
make start      # build & start the PHP container (Symfony on :8000)
make db-init    # create the database and schema
# or: make db-migrate   to run the migrations instead
```

The app runs at `http://localhost:8000`.

### API (v0)

| Method | Path | What it does |
|---|---|---|
| `POST`  | `/customer/create` | create a customer |
| `GET`   | `/customer/{id}`   | get a customer |
| `PATCH` | `/customer/{id}`   | update a customer |
| `POST`  | `/loan/apply`      | apply for a loan / check eligibility |

Ready-to-run request samples are in [`http/v0/`](http/v0/) (JetBrains HTTP client
format). Run them with `make tests-http`.

## Quality & CI

One command runs every quality gate — code style, static analysis, and tests:

```bash
make ready
```

It runs, in order:

| Gate | Command | What it checks |
|---|---|---|
| Code style | `make cs-check` (`make cs-fix` to apply) | PHP-CS-Fixer (`@PSR12` + safe rules) |
| Static analysis | `make phpstan` | PHPStan level 6 |
| Tests | `make test` (`test-unit` / `test-feature`) | PHPUnit suites |

Tests are split into two suites:

- **`tests/Unit/`** — fast, no container or DB (domain rules, value objects).
- **`tests/Feature/`** — boot the kernel and hit the HTTP API against a throwaway
  SQLite schema (customer create / validation / read).

**CI** ([`.github/workflows/ci.yml`](.github/workflows/ci.yml)) runs each gate as a
**separate, parallel job** (`php-cs-fixer`, `phpstan`, `unit tests`, `feature tests`)
on every push and pull request. Each job reuses the matching `make` target
(`make <target> PHP_RUN=`), so local and CI use the same commands — `make ready` is
just the local shortcut that runs them all in one go.

A few notes on the setup:

- **PHPStan baseline.** `phpstan-baseline.neon` captures pre-existing findings so the
  build is green today while new code is held to level 6. Burn it down over time.
- **Isolated PHP-CS-Fixer.** It lives in its own composer project under
  `tools/php-cs-fixer/` because its `symfony/process` requirement does not fit the
  app's Symfony 7.1 pin — this keeps the app's dependency graph clean.

## Notes

- `.env` holds **non-secret dev defaults** only. Put real secrets in `.env.local`
  (git-ignored) or in real environment variables — never commit them.
- Some `// todo:` comments mark known simplifications (for example, the address is
  stored as JSON and the US states are hard-coded) kept on purpose to keep the focus
  on the architecture.
