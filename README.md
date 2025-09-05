# KFC-Food-Ordering-and-Management-System

# System Description
Presently, KFC uses paper menu, oral reception of order and hand written bill both on take away and dine in transactions. This manual process is a continuous source of incorrect orders, long service period and restrictions on visibility of the order process.

A theoretical system which is web based would aim at substituting these manual processes with a virtual menu in which, structured in categories, is shown to the customers to navigate items, use a shopping bag, take orders, make virtual payments, and leave reviews. Meanwhile, administrators will have an opportunity to view or delete menu items, menu categories, track live orders, update their statuses, and reviews regardless of whether an option is selected by the customer or not.

The system aims at eliminating the errors of order, speeding up the service, centralizing the customer reviews, and providing the customers and administrators with effective and open operations.

# Project Modules
User Module 

Menu and Review Management

Payment Module

Admin Dashboard Module

Order Module

# .env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:2k5JXy7RC0MESn2/ZoKsvoE+Q8lI+qPO1NOpN2UtrOQ=
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
/# APP_MAINTENANCE_STORE=database

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kfc_db
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
/# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

STRIPE_PUBLIC_KEY=pk_test_51S2o3uDmYLtEpymiiEHoTjudcrcRbWAqSYzMLTFVISZnpcyBF8uWUfSAIbSDLnAh7tf0MMDZaNudAASu5AKUkptw00Ro1kwJNN
STRIPE_SECRET_KEY=sk_test_51S2o3uDmYLtEpymiBS9VXYfvVfjr6uExQUlLumz5A3ERg6fYUtM7dWBvxKqr0NJRIwk5gnTS1hSAEP6zTCCH11ip00JXsXt0to

# Test Cards for Payment (Visa)
Number	Description
4242424242424242	Succeeds and immediately processes the payment.
4000000000003220	Requires 3D Secure 2 authentication for a successful payment.
4000000000009995	Always fails with a decline code of insufficient_funds.
# Mastercard
5555555555554444	Any 3 digits	Any future date