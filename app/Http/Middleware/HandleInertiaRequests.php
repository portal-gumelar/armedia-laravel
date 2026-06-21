<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\WebSetting;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $settings = WebSetting::all()->pluck('value', 'key')->toArray();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => \Illuminate\Support\Facades\Auth::guard('member')->user() ?? $request->user(),
            ],
            'webSettings' => $settings,
        ];
    }
}
