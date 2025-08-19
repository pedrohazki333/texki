<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DTF extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'print_date', 'meters', 'status'];

    protected static function booted()
    {
        static::creating(function ($print) {
            $date = $print->print_date ?? now()->toDateString();
            $formattedDate = \Carbon\Carbon::parse($date)->format('mdY');

            $count = self::whereDate('print_date', $date)->count() + 1;
            $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);

            $print->id = "dtf-{$formattedDate}-{$sequence}";
        });
    }
}
