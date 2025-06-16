<?php

namespace Webkul\Seller\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Seller\Contracts\SellerGroup as SellerGroupContract;
use Webkul\Seller\Database\Factories\SellerGroupFactory;

class SellerGroup extends Model implements SellerGroupContract
{
    use HasFactory;

    /**
     * Deinfine model table name.
     *
     * @var string
     */
    protected $table = 'seller_groups';

    /**
     * Fillable property for the model.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'is_user_defined',
    ];

    /**
     * Get the sellers for this group.
     */
    public function sellers(): HasMany
    {
        return $this->hasMany(SellerProxy::modelClass());
    }

    /**
     * Create a new factory instance for the model
     */
    protected static function newFactory(): Factory
    {
        return SellerGroupFactory::new();
    }
}
