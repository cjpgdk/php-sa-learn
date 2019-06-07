test: composer
	vendor/bin/box --compile;

composer:
	composer install -o;
	composer bin box require --dev humbug/box;
