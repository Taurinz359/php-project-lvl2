install:
	composer install

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src tests
	composer exec --verbose phpstan -- --level=8 analyse src tests

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 src tests