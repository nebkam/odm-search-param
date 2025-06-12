#!/bin/bash

cd $(dirname $0) \
  && docker compose down --volumes \
  && docker compose up -d \
  && docker compose exec php php vendor/bin/phpunit ./tests