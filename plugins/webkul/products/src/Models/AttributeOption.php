<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Product\Database\Factories\AttributeOptionFactory;
use Webkul\Security\Models\User;

class AttributeOption extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'products_attribute_options';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'color',
        'extra_price',
        'sort',
        'attribute_id',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): AttributeOptionFactory
    {
        return AttributeOptionFactory::new();
    }
}
