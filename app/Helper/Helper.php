<?php

use App\Models\Produk;
use App\Models\Wishlist;
use App\Models\SettingWebsite;
use Illuminate\Support\Facades\Auth;

if (!function_exists('getSettings')) {
  function getSettings($key)
  {
    $settings = SettingWebsite::select($key)->first();
    return $settings->{$key};
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
