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

# steps:

Docker composer Ports : 
ports:
  - "8080:80"    # Laravel
  - "5174:5173"  # Vite
  - "3307:3306"  # MySQL (Docker only)
  - "6380:6379"  # Redisl

php artisan queue:work

#  Test 
php artisan make:test ProductTest
php artisan make:test OrderTest

php artisan test



docker-compose exec app composer install

docker-compose exec app php artisan migrate --seed

Run queue worker:

docker-compose exec app php artisan queue:work

docker-compose exec app php artisan serve --host=0.0.0.0 --port=8000

# ####
5. Docker Setup (Required)
● Make the whole project run inside Docker.
● Use docker-compose with at least:
○ PHP + Laravel container
○ Database container (MySQL/PostgreSQL/SQLite in container)
○ Redis container (for queues & cache)

● Provide commands in README to:
docker-compose up -d
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan queue:work

Make sure the project can be run with: php artisan migrate --seed
php artisan serve
php artisan queue:work