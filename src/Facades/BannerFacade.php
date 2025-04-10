<?php 

namespace Ehtterami\IpTruffle\Facades;

use Ehtterami\IpTruffle\Services\BannerService;

class BannerFacade
{
    private static ?BannerService $instance = null;

    public static function render(): void
    {
        if(self::$instance === null) {
            self::$instance = new BannerService();
        }

        echo self::$instance->render();
    }
}