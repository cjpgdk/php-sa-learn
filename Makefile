test: composer
	vendor/bin/phpunit --coverage-text --colors=never;

composer:
	composer install -o;
