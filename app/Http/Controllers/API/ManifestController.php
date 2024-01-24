<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManifestController extends Controller
{
    public function index()
    {
        return response()->json([
            "name" => config('app.name'),
            "short_name" => config('app.name'),
            "theme_color" => "#4DBA87",
            "icons" => [
                [
                    "src" => config('app.logo'),
                    "sizes" => "192x192",
                    "type" => "image/png"
                ],
                [
                    "src" => config('app.logo'),
                    "sizes" => "512x512",
                    "type" => "image/png"
                ],
                [
                    "src" => config('app.logo'),
                    "sizes" => "192x192",
                    "type" => "image/png",
                    "purpose" => "maskable"
                ],
                [
                    "src" => config('app.logo'),
                    "sizes" => "512x512",
                    "type" => "image/png",
                    "purpose" => "maskable"
                ]
            ],
            "start_url" => "/",
            "display" => "standalone",
            "background_color" => "#000000"
        ]);
    }
}
