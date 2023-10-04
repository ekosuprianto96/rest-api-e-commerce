<?php

namespace App\Helper;

class Helper {

  public static function tambahBiayaPlatform($total) {
    $biaya_platform = 10;
    $biaya_platform = (float) $biaya_platform / 100;
    $biaya_platform = (float) $total * $biaya_platform;
    $total_biaya = (float) $total - $biaya_platform;

    return $total_biaya;
  }
}