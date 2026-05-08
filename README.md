# 🏥 MediCare Hospital Management System

## Requirements
- Docker & Docker Compose
- Git

## Installation

### 1. Clone the repository
git clone https://github.com/rockeycse/medicare.git
cd medicare

### 2. Environment setup
cp .env.example .env

### 3. Pull Docker images
docker pull rockeycse/medicare-app:latest
docker pull rockeycse/medicare-queue:latest
docker pull rockeycse/medicare-reverb:latest

### 4. Start containers
docker-compose up -d

### 5. Laravel setup
docker exec -it medicare_app composer install
docker exec -it medicare_app cp .env.example .env
docker exec -it medicare_app php artisan key:generate
docker exec -it medicare_app php artisan passport:keys
docker exec -it medicare_app php artisan migrate --seed

### 6. Access
API: http://localhost:8000
API Docs: http://localhost:8000/api/documentation
WebSocket: ws://localhost:8080
