## MKA_TestExam
MKA Test running in docker

## About Apps
Apps is a test exam for company application of Ip Crud using vueJS and Axios Routing
Laravel PHP 8.4 running in docker 

## Commands to run if cloning

# Using Bash
git clone https://github.com/yourname/your-repo.git
cd your-repo
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app npm install
docker compose exec app npm run dev -- --host

# Using Powershell
git clone https://github.com/yourname/your-repo.git
cd your-repo
Copy-Item .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app npm install
docker compose exec app npm run dev -- --host
