<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Links;
use App\Models\Plans;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Ceklimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        $plan = $user->plan;

        if (!$plan) {
            // fallback misal otomatis ke plan basic
            $plan = Plans::where('slug', 'basic')->first();
        }

        $maxLinks = $plan->features['max_links'];
        $currentLinks = Links::where('user_id', $user->id)->count();

        if ($currentLinks >= $maxLinks) {
            return redirect()->back()->withErrors([
                'limit' => 'Kamu telah mencapai limit link untuk plan ' . $plan->name
            ]);
        }

        return $next($request);
    }
}
