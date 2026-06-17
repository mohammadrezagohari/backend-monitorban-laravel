# Monitorban Backend

Monitorban is a modular Laravel API for monitoring server rooms, installed sensors, sensor readings, alert thresholds, company-level dashboard settings, and scoped access control.

The project is built with Laravel 12, JWT authentication, Spatie permissions, Laravel Modules, Swagger/OpenAPI documentation, and a service/repository architecture.

## What This API Handles

- Multi-company monitoring domains
- Server room management per company
- Sensor type definitions such as temperature, humidity, motion, fire suppression, and air quality
- Units such as `C`, `F`, `%`, and `bool`
- Installed sensors inside server rooms
- Per-sensor thresholds for normal, warning, and critical ranges
- Bulk threshold profiles for applying one configuration to many sensors
- Sensor readings and dashboard summaries
- JWT authentication
- Role, permission, group, and resource-scoped access control
- Swagger documentation for API exploration

## Tech Stack

- PHP `^8.2`
- Laravel `^12`
- MySQL or PostgreSQL depending on `.env`
- JWT Auth: `tymon/jwt-auth`
- Permissions: `spatie/laravel-permission`
- DTO/Data layer: `spatie/laravel-data`
- Modules: `nwidart/laravel-modules`
- API docs: `darkaonline/l5-swagger`

## Project Structure

```text
app/
  Models/
  Providers/
  Repositories/
  Support/

Modules/
  Room/
  Sensor/
  Ticket/
  User/

database/
  migrations/
  seeders/

storage/api-docs/
```

Important layers:

```text
Controllers  -> HTTP request/response and Swagger annotations
Services     -> business rules and application flow
Repositories -> database access through interfaces
Models       -> Eloquent relations and casts
```

## Main Domain Model

```text
companies
company_user
company_dashboard_settings
server_rooms
sensor_types
units
sensors
sensor_threshold_profiles
sensor_thresholds
sensor_readings
groups
group_resource_access
```

Key ideas:

- A `Company` owns server rooms and sensors.
- A `ServerRoom` belongs to one company.
- A `SensorType` defines the sensor category, such as temperature or humidity.
- A `Sensor` is an installed sensor in a server room.
- A `SensorThresholdProfile` is a reusable/bulk threshold profile.
- A `SensorThreshold` overrides thresholds for a specific sensor.
- A `SensorReading` stores measured values over time.
- `group_resource_access` limits a group to specific rooms or sensors.

## Setup

Install dependencies:

```bash
composer install
```

Create environment file:

```bash
cp .env.example .env
php artisan key:generate
```

Set database values in `.env`, then run:

```bash
php artisan migrate
php artisan db:seed
php artisan l5-swagger:generate
```

Run tests:

```bash
php artisan test
```

Or through composer:

```bash
composer test
```

## Local Development

Run Laravel locally:

```bash
php artisan serve
```

Useful commands:

```bash
php artisan migrate
php artisan db:seed
php artisan route:list --path=api/v1
php artisan l5-swagger:generate
php artisan test
```

## Swagger

Swagger UI:

```text
http://localhost/api/documentation
```

When using Swagger Authorize, paste only the JWT token.

Correct:

```text
eyJ0eXAiOiJKV1QiLCJhbGciOi...
```

Wrong:

```text
Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi...
```

Swagger UI adds the `Bearer` prefix automatically.

## Authentication

Login:

```http
POST /api/v1/auth/login
```

Example body:

```json
{
  "mobile": "09100000002",
  "password": "password"
}
```

Authenticated requests:

```http
Authorization: Bearer <jwt-token>
```

## Seeded Users

All seeded users use:

```text
password
```

Seeded accounts:

```text
09100000001  superadmin@example.com   super-admin
09100000002  admin@example.com        admin
09100000003  supervisor@example.com   super-visor
09100000004  viewer@example.com       user
```

Company assignments:

```text
09100000001 => monitorban-demo(owner), acme-datacenter(owner)
09100000002 => monitorban-demo(owner)
09100000003 => monitorban-demo(member)
09100000004 => monitorban-demo(member)
```

## Roles And Permissions

Seeded permissions:

```text
companies.manage
rooms.view
rooms.manage
sensors.view
sensors.manage
sensor-types.manage
units.manage
thresholds.manage
sensor-readings.view
sensor-readings.manage
dashboard.view
user.create
```

Role behavior:

```text
super-admin  all permissions
admin        company, room, sensor, threshold, reading, dashboard management
super-visor  scoped view and dashboard
user         basic view and dashboard
```

## Group-Scoped Access

Groups can be limited to specific server rooms and sensors.

Endpoint:

```http
PUT /api/v1/groups/{id}/resource-access
```

Example body:

```json
{
  "server_room_ids": [1],
  "sensor_ids": [1, 2, 3]
}
```

Rules:

- `admin` and `super-admin` can access all resources in their company.
- Other users only see rooms/sensors allowed through their groups.
- Access to a room grants access to sensors inside that room.
- Direct sensor access can be granted independently.

Seeded scoped group:

```text
main-room-operators
```

The seeded supervisor is attached to this group and can access only the main server room and its sensors.

## Core API Endpoints

Companies:

```http
GET    /api/v1/companies
POST   /api/v1/companies
GET    /api/v1/companies/{company}
PUT    /api/v1/companies/{company}
DELETE /api/v1/companies/{company}
POST   /api/v1/companies/{company}/users
```

Rooms:

```http
GET    /api/v1/rooms
POST   /api/v1/rooms
GET    /api/v1/rooms/{room}
PUT    /api/v1/rooms/{room}
DELETE /api/v1/rooms/{room}
```

Sensor catalog:

```http
GET    /api/v1/sensor-types
POST   /api/v1/sensor-types
GET    /api/v1/units
POST   /api/v1/units
```

Sensors:

```http
GET    /api/v1/sensors
POST   /api/v1/sensors
GET    /api/v1/sensors/{sensor}
PUT    /api/v1/sensors/{sensor}
DELETE /api/v1/sensors/{sensor}
```

Threshold profiles:

```http
GET  /api/v1/threshold-profiles
POST /api/v1/threshold-profiles
POST /api/v1/threshold-profiles/{threshold_profile}/apply
```

Readings:

```http
GET  /api/v1/sensors/{sensor}/readings
POST /api/v1/sensors/{sensor}/readings
```

Dashboard:

```http
GET /api/v1/sensors/dashboard/summary
```

## Pagination Format

List endpoints return a consistent shape:

```json
{
  "status": "success",
  "data": [],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "from": 1,
    "to": 10,
    "total": 100,
    "last_page": 10,
    "has_more_pages": true
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

Use:

```http
?page=1&per_page=20
```

## Service And Repository Layer

The project has started moving controller database logic into services and repositories.

Base repository:

```text
app/Repositories/Contracts/BaseRepositoryInterface.php
app/Repositories/Eloquent/BaseRepository.php
```

Repository binding:

```text
app/Providers/AppServiceProvider.php
```

Examples:

```text
CompanyRepositoryInterface -> CompanyRepository
ServerRoomRepositoryInterface -> ServerRoomRepository
SensorRepositoryInterface -> SensorRepository
SensorTypeRepositoryInterface -> SensorTypeRepository
```

Service examples:

```text
Modules/Room/Services/RoomService.php
Modules/Sensor/Services/SensorService.php
Modules/User/Services/CompanyService.php
```

Controllers should stay thin:

```text
validate/request context -> service call -> response
```

## Docker Notes

The repository root contains `Dockerfile`, `docker-compose.yml`, and `nginx/default.conf`.

Current compose services:

```text
app
nginx
db
```

The Laravel application source lives in `src/`. If you run Docker from the repository root, make sure the container working directory, Nginx root, and mounted paths point to the Laravel app directory correctly.

Expected Laravel public path:

```text
src/public
```

## Troubleshooting

Duplicate migration column:

```text
Duplicate column name 'email'
```

The users migration has already been fixed to define `email` once.

Sanctum guard error:

```text
Auth guard [sanctum] is not defined.
```

This project uses JWT, not Sanctum. API routes should use:

```text
jwt.auth
```

Module seeder direct execution:

```bash
php artisan db:seed --class=Modules\\User\\Database\\Seeders\\UserDatabaseSeeder
```

may hit module autoload redeclare issues. Prefer:

```bash
php artisan db:seed
```

Swagger auth failure:

Paste only the JWT token in Swagger Authorize. Do not include `Bearer`.

## Verification Checklist

After pulling changes:

```bash
composer install
php artisan migrate
php artisan db:seed
php artisan l5-swagger:generate
php artisan test
```

Expected test result:

```text
Tests: 7 passed
```
