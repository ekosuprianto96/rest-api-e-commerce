<?php

use App\Models\Produk;
use App\Models\SettingGateway;
use App\Models\Wishlist;
use App\Models\SettingWebsite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

if (!function_exists('getSettings')) {
  function getSettings($key)
  {
    $settings = SettingWebsite::select($key)->first();
    return $settings->{$key};
  }
}
if (!function_exists('path_produk')) {
  function path_file_produk($key = 'file', $filename = null)
  {
    if(isset($fileName)) return 'assets/produk/'.$key.'/'.$fileName;

    return 'assets/produk/'.$key;
  }
}
if (!function_exists('statusWishlist')) {
  function statusWishlist(Produk $produk)
  {
    $statusWishlist = 0;
    if (Auth::check()) {
      $wishlist = Wishlist::where([
        'kode_produk' => $produk->kode_produk,
        'uuid_user' => Auth::user()->uuid
      ])->first();

      if ($wishlist) {
        if ($produk->kode_produk == $wishlist->kode_produk) {
          $statusWishlist = 1;
        } else {
          $statusWishlist = 0;
        }
      }
      return $wishlist;
    }
  }
}
if (!function_exists('getUserName')) {
  function getUserName()
  {
    if (Auth::check()) {
      return Auth::user()->username;
    }
  }
}
if (!function_exists('isActiveMenu')) {
  function isActiveMenu($route, $output = 'bg-blue-500 text-slate-50')
  {
    if (Route::currentRouteName() == $route) return $output;

    return 'hover:bg-blue-500 hover:text-slate-50';
  }
}
if (!function_exists('getSettingsGateway')) {
  function getSettingsGateway($name, $key = null)
  {
    $settings = getOrUpdateCache('settingGateway', function () use ($name) {
      return SettingGateway::where('name', $name)->first();
    }, 2400);

    if (isset($settings)) {
      if (empty($key)) {
        return $settings;
      }
      return $settings[$key];
    }
    return null;
  }
}
if (!function_exists('getOrUpdateCache')) {
  function getOrUpdateCache($key, $databaseQuery, $minutes = null)
  {
    $cachedData = Cache::tags($key)->get($key);

    if ($cachedData === null) {
      // Data tidak ada di cache, ambil dari database
      $databaseData = $databaseQuery();

      // Simpan data ke cache
      $data = Cache::tags($key)->rememberForever($key, function () use ($databaseData) {
        return $databaseData;
      });

      return $data;
    }

    // Data ada di cache, cek apakah ada perbedaan dengan data di database
    $databaseData = $databaseQuery();

    if ($cachedData != $databaseData) {
      // Jika ada perbedaan, perbarui data di cache
      Cache::tags($key)->put($key, $databaseData, $minutes);
    }

    return $cachedData;
  }
}
if (!function_exists('isCheckVar')) {
  function isCheckVar($data = null, $key = null)
  {
    if(empty($data)) return null;

    if(isset($key)) return $data[$key];

    return $data;
  }
}
if (!function_exists('renderScript')) {
  function renderScript($fileName)
  {
    return view("frontend.layouts.scripts.{$fileName}")->render();
  }
}
