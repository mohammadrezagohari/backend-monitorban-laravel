<?php

namespace App\Providers;

use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Eloquent\CompanyRepository;
use Illuminate\Support\ServiceProvider;
use Modules\Room\Repositories\Contracts\ServerRoomRepositoryInterface;
use Modules\Room\Repositories\Eloquent\ServerRoomRepository;
use Modules\Sensor\Repositories\Contracts\SensorRepositoryInterface;
use Modules\Sensor\Repositories\Contracts\SensorTypeRepositoryInterface;
use Modules\Sensor\Repositories\Eloquent\SensorRepository;
use Modules\Sensor\Repositories\Eloquent\SensorTypeRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(ServerRoomRepositoryInterface::class, ServerRoomRepository::class);
        $this->app->bind(SensorRepositoryInterface::class, SensorRepository::class);
        $this->app->bind(SensorTypeRepositoryInterface::class, SensorTypeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
