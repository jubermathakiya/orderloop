<?php

namespace App\Models;

use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = ['title', 'author', 'status'];

    protected $casts = [
        'status' => BookStatus::class, // Cast status to Enum
    ];

    public function borrow(): HasMany
    {
        return $this->hasMany(Borrow::class, 'book_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === BookStatus::Available;
    }

    public function markAsBorrowed(): void
    {
        $this->update(['status' => BookStatus::Borrowed]);
    }

    public function markAsAvailable(): void
    {
        $this->update(['status' => BookStatus::Available]);
    }
}
