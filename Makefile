analyze:
	sh vendor/bin/phpcbf
	sh vendor/bin/phpcs

.PHONY: tests vendor
tests: vendor
	make prepare-test
	sh vendor/bin/simple-phpunit

.PHONY: prepare-dev
prepare-dev: bin
	php bin/console cache:clear --env=dev
	php bin/console doctrine:database:drop --if-exists -f --env=dev
	php bin/console doctrine:database:create --env=dev
	php bin/console doctrine:schema:update -f --env=dev
	php bin/console doctrine:fixtures:load -n --env=dev

.PHONY: prepare-test
prepare-test: bin
	php bin/console cache:clear --env=test
	php bin/console doctrine:database:drop --if-exists -f --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:update -f --env=test
	php bin/console doctrine:fixtures:load -n --env=test