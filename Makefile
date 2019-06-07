test: box
	vendor/humbug/box/bin/box --compile -d ../../../../;

box: composer
	chmod +x vendor/humbug/box/bin/box;
	composer --no-dev -o --quiet --working-dir=vendor/humbug/box install

composer:
	composer install -o;
