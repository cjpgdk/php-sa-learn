test: box
	vendor/humbug/box/bin/box --compile -d ../../../../;

box: composer
	chmod +x vendor/humbug/box/bin/box;
	cd vendor/humbug/box;composer dump-autoload;cd ../../../;

composer:
	composer install -o;
