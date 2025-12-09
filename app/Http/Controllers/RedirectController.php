<?php

namespace App\Http\Controllers;

use App\Models\Links;
use App\Models\LinkClicks;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RedirectController extends Controller
{
    public function redirect(Request $request, $slug)
    {
        // Cari link berdasarkan slug
        $link = Links::where('slug', $slug)->firstOrFail();

        // Cek status link
        if (! $link->is_active) {
            abort(410, 'Link sudah tidak aktif.');
        }

        if ($link->expired_at && $link->expired_at->isPast()) {
            abort(410, 'Link sudah kedaluwarsa.');
        }

        // Log klik
        $agent = new Agent();

        LinkClicks::create([
            'link_id'   => $link->id,
            'ip_address' => $request->ip(),
            'browser'   => $agent->browser(),
            'device'    => $agent->device(),
            'platform'  => $agent->platform(),
            'country'   => $this->getCountry($request->ip()),
        ]);

        // Increment simple counter
        $link->increment('clicks_count');

        // Redirect ke original URL
        return redirect()->away($link->original_url);
    }

    // Optional: berdasarkan IP
    private function getCountry($ip)
    {
        try {
            $response = Http::get("http://ip-api.com/json/{$ip}");
            return $response->json('country') ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
