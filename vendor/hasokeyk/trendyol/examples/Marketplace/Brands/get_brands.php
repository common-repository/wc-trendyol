<?php

    //Source : https://developers.trendyol.com/docs/marketplace/urun-entegrasyonu/trendyol-marka-listesi

    use Hasokeyk\Trendyol\Trendyol;

    require "vendor/autoload.php";

    $supplierId = 'XXXXXX';
    $username   = 'XXXXXXXXXXXXXXXXXXXX';
    $password   = 'XXXXXXXXXXXXXXXXXXXX';

    $trendyol = new Trendyol($supplierId, $username, $password);

    $trendyol_marketplace_brands = $trendyol->marketplace->TrendyolMarketplaceBrands();

    $brands = $trendyol_marketplace_brands->get_brands();
    print_r($brands);
