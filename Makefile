start:
	docker-compose up -d

stop:
	docker-compose down

bash:
	docker exec -ti symfony_php bash

build:
	docker-compose up -d && \
	docker exec symfony_php composer install