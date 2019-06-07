test: box
	vendor/humbug/box/bin/box --compile -d ../../../../;

box: composer
	chmod +x vendor/humbug/box/bin/box;
	cd vendor/humbug/box;composer --no-dev -o install;cd ../../../;

composer:
	composer install -o;
