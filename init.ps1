docker run --rm -v "${PWD}\wordpress\wordpress:/target" wordpress:6.8.3-php8.3-apache sh -c "cp -a /usr/src/wordpress/. /target/"
docker compose up -d