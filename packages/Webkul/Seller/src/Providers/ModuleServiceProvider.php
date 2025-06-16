<?php

namespace Webkul\Seller\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        \Webkul\Customer\Models\CompareItem::class,
        \Webkul\Customer\Models\Customer::class,
        \Webkul\Customer\Models\CustomerAddress::class,
        \Webkul\Seller\Models\SellerGroup::class,
        \Webkul\Customer\Models\CustomerNote::class,
        \Webkul\Customer\Models\Wishlist::class,
    ];
}
