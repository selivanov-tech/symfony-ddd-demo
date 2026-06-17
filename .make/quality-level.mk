# Quality gates: static analysis, code style, tests.
#
# Every target runs through $(PHP_RUN). Locally that is the php docker service, so
# these commands match the rest of the Makefile. CI overrides it with an empty
# value (`make ready PHP_RUN=`) to run natively on a setup-php runtime — the exact
# same targets, no duplicated commands.
PHP_RUN ?= docker compose run --rm -T php

# php-cs-fixer lives in an isolated composer project (tools/php-cs-fixer) so its
# symfony/process ^7.2 requirement does not clash with the app's symfony 7.1 pin.
CS_FIXER = $(PHP_RUN) tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php

.PHONY: phpstan cs-check cs-fix test test-unit test-feature tools-install ready

phpstan: ## Run PHPStan static analysis
	@$(PHP_RUN) vendor/bin/phpstan analyse --no-progress --memory-limit=1G

cs-check: ## Check code style without changing files
	@$(CS_FIXER) --dry-run --diff

cs-fix: ## Fix code style in place
	@$(CS_FIXER)

test: ## Run the full PHPUnit suite
	@$(PHP_RUN) vendor/bin/phpunit

test-unit: ## Run only the unit test suite
	@$(PHP_RUN) vendor/bin/phpunit --testsuite unit

test-feature: ## Run only the feature test suite
	@$(PHP_RUN) vendor/bin/phpunit --testsuite feature

tools-install: ## Install isolated dev tools (php-cs-fixer)
	@$(PHP_RUN) composer install -d tools/php-cs-fixer --no-interaction

ready: cs-check phpstan test ## Run every quality gate (style + static analysis + tests)
	@printf "$(GREEN)✓ make ready: all quality gates passed$(RESET)\n"
