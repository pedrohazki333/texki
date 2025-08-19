<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    protected $fillable = ['customer_id', 'employee_id', 'status', 'total', 'notes'];

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

        return $path
            ? Storage::disk('public')->url($path)   // <- monta URL pública
            : null;
    }
}
