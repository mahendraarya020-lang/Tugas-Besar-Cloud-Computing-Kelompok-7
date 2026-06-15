<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'name', 'email', 'phone', 'company'])]
class Client extends Model
{
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
