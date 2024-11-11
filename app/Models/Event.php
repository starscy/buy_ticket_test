<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    protected $guarded = [];

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(EventType::class, 'events_events-types')->withPivot('prices');
    }
}
