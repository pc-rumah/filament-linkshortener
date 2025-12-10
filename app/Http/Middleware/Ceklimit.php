<?php

namespace App\Http\Middleware;

use App\Models\Links;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Ceklimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $plan = $user->plan;

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
