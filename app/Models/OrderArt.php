<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderArt extends Model
{
    protected $fillable = ['order_id', 'image_path', 'quantity', 'width', 'height', 'notes'];

    protected $casts = [
        'quantity'   => 'integer',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
