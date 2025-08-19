<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    protected $fillable = ['customer_id', 'employee_id', 'status', 'total', 'notes'];

    protected static function booted()
    {
        static::deleting(function (Order $order) {
            // Carrega artes só se ainda não veio
            $order->loadMissing('arts');

            foreach ($order->arts as $art) {
                $path = self::normalizePublicPath($art->image_path);
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            // Remova via Eloquent (para manter consistência),
            // em vez de depender de cascade do banco aqui.
            $order->arts()->delete();
            $order->items()->delete();
        });
    }

    private static function normalizePublicPath(?string $raw): ?string
    {
        if (blank($raw)) return null;

        // remove prefixos errados como "public/", "storage/", "public/storage/"
        $clean = ltrim(str_replace(['public/', 'storage/', 'public\\', 'storage\\'], '', $raw), '/\\');

        // agora deve ficar "orders/arts/arquivo.jpg"
        return $clean;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function arts()
    {
        return $this->hasMany(OrderArt::class);
    }

    public function getFirstArtUrlAttribute(): ?string
    {
        $art = $this->relationLoaded('arts')
            ? $this->arts->first()
            : $this->arts()->oldest('id')->first();

        $path = $art?->image_path;
        if (! $path) return null;

        $path = self::normalizePublicPath($path);

        return $path ? \Storage::disk('public')->url($path) : null;
    }
}
