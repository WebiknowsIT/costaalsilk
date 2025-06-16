<?php

namespace Webkul\Seller\Repositories;

use Webkul\Core\Eloquent\Repository;

class SellerGroupRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Webkul\Seller\Contracts\SellerGroup';
    }
}
