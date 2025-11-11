<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\Order;
use App\Observers\OrderObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Thêm ngày 6/11/2025:  Mail
        // Mail status badge directive
        Blade::directive('mailStatus', function ($status) {
            return "<?php echo \App\Helpers\BladeHelper::mailStatusBadge($status); ?>";
        });

        // Mail type badge directive
        Blade::directive('mailType', function ($type) {
        return "<?php echo \App\Helpers\BladeHelper::mailTypeBadge($type); ?>";
        });
         Order::observe(OrderObserver::class);
    }
}