<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

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
        $user = $request->user();

        // Cache roles & permissions per user for 5 min — avoids 2 DB queries on every request
        $authUser = null;
        if ($user) {
            $uid         = $user->id;
            $roles       = cache()->remember("user_roles_{$uid}",       300, fn () => $user->getRoleNames());
            $permissions = cache()->remember("user_permissions_{$uid}", 300, fn () => $user->getAllPermissions()->pluck('name'));

            $authUser = [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'avatar'      => $user->avatar,
                'roles'       => $roles,
                'permissions' => $permissions,
            ];
        }

        return [
            ...parent::share($request),
            'auth' => ['user' => $authUser],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'info'    => fn () => $request->session()->get('info'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
