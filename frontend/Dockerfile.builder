# vi:syntax=dockerfile
FROM debian:bullseye as builder

# upgrade debian packages
ENV DEBIAN_FRONTEND="noninteractive"
# fix "September 30th problem"
# https://github.com/nodesource/distributions/issues/1266#issuecomment-931597235
RUN apt update; apt install -y ca-certificates && \
    apt update;
RUN apt install apt-utils -y
RUN apt upgrade -y

# install curl to install nvm
RUN apt install curl -y
# https://stackoverflow.com/a/57344191
WORKDIR /usr/src
SHELL ["/bin/bash", "--login", "-c"]
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.35.3/install.sh | bash
COPY ./.nvmrc .
RUN nvm i
# use latest npm
RUN npm install -g npm@latest

# extras
RUN apt install neovim -y

# build frontend application
COPY ./ ./frontend-app
WORKDIR /usr/src/frontend-app
RUN rm ./svelte.config.js
RUN mv ./svelte.config.static.js ./svelte.config.js
RUN npm i
RUN npx svelte-kit build export
