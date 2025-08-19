<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderArt extends Model
{
    protected $fillable = ['order_id', 'image_path', 'quantity', 'width', 'height', 'notes'];

    protected $casts = [
        'quantity'   => 'integer',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::deleting(function (OrderArt $art) {
            if ($art->image_path) {
                // apaga do disco 'public' se existir
                Storage::disk('public')->delete($art->image_path);
            }
        });
    }

    private static function normalizePublicPath(?string $raw): ?string
    {
        if (blank($raw)) return null;
        return ltrim(str_replace(['public/', 'storage/', 'public\\', 'storage\\'], '', $raw), '/\\');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
