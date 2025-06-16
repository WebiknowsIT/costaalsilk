<?php

namespace Webkul\Seller\Facades;

use Illuminate\Support\Facades\Facade;
use Webkul\Seller\Captcha as BaseCaptcha;

class Captcha extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BaseCaptcha::class;
    }
}
