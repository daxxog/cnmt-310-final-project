.PHONY: help
help:
	@echo "available targets -->\n"
	@cat Makefile | grep ".PHONY" | grep -v ".PHONY: _" | sed 's/.PHONY: //g'


.PHONY: dev-docker-backend
dev-docker-backend:
	docker run -i -t \
	-p 8080:80 \
	-e TOKEN_LOCATION=/mnt/backend/token.json \
	-v $$(pwd)/backend:/var/www/html \
	-v $$(pwd)/backend:/mnt/backend \
	php:7.4-apache
