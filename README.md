# Symfony DDD demo — loan eligibility service

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

## Notes

- `.env` holds **non-secret dev defaults** only. Put real secrets in `.env.local`
  (git-ignored) or in real environment variables — never commit them.
- Some `// todo:` comments mark known simplifications (for example, the address is
  stored as JSON and the US states are hard-coded) kept on purpose to keep the focus
  on the architecture.
