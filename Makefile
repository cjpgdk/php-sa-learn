test: box
	vendor/humbug/box/bin/box --compile -d ../../../../;

box: composer
	chmod +x vendor/humbug/box/bin/box;

composer:
	composer install -o;
