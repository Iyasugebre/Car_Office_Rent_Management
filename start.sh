#!/usr/bin/env bash

# Run migrations and seed data
php artisan migrate --force
php artisan db:seed --force


# Start the Apache server
apache2-foreground
