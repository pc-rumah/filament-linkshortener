<?php

namespace App\Http\Controllers;

use App\Models\Links;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrController extends Controller
{
    public function generate($slug)
    {
        $link = Links::where('slug', $slug)->firstOrFail();

        $url = route('link.redirect', $link->slug);

        $qr = QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->generate($url);

        return response($qr)
            ->header('Content-Type', 'image/png');
    }
}
