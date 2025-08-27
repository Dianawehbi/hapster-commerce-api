# hapster-commerce-api
Backend API built with Laravel for managing products and orders. Includes CRUD operations, caching, Redis queues, job processing, and full Docker setup.
---

## Prerequisites

- PHP = 8.2.12
- Composer = 2.8.6
- MySQL = 8.0 
- Redis >= 7.2 (for queues)

---

## Clone the repository

```bash
git clone https://github.com/Dianawehbi/hapster-commerce-api.git
cd hapster-commerce-api/hapster-backend

```

## CMD

docker compose up -d

## UBUNTO

./vendor/bin/sail up -d
./vendor/bin/sail artisan tinker 
./vendor/bin/sail artisan make:job ProccesOrderJob 
./vendor/bin/sail artisan queue:work 

## Job

./vendor/bin/sail artisan queue:work --once
./vendor/bin/sail artisan queue:work --stop-when-empty

##  Test 
php artisan make:test ProductTest
php artisan make:test OrderTest

php artisan test
