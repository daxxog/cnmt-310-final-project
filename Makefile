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


.PHONY: dev-frontend
dev-frontend:
	cd ./frontend && npm run dev


.PHONY: _frontend-docker
_frontend-docker:
	cd ./frontend && docker build . -f Dockerfile.builder \
		-t cnmt-310-final-project-frontend

.PHONY: frontend
frontend: _frontend-docker
	docker run --entrypoint /bin/bash -t cnmt-310-final-project-frontend \
		-c 'tar cz build | base64' | base64 -d | tar xzv
