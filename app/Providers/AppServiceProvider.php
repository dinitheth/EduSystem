<?php

namespace App\Providers;

use App\Models\PortalNotification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('portal.layout', function ($view) {
            $type = session('student_id') ? 'student' : (session('teacher_id') ? 'teacher' : null);
            $id = session('student_id') ?: session('teacher_id');

            $notifications = collect();
            $unreadCount = 0;

            if ($type && $id) {
                $baseQuery = PortalNotification::forRecipient($type, (int) $id);
                $unreadCount = (clone $baseQuery)->whereNull('read_at')->count();
                $notifications = (clone $baseQuery)->latest()->take(8)->get();
            }

            $view->with([
                'portalNotifications' => $notifications,
                'portalUnreadNotifications' => $unreadCount,
            ]);
        });
    }
}
