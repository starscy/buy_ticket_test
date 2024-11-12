<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Модель событие
 */
class Event extends Model
{
    protected $guarded = [];

    /**
     * Связь с типами событий
     * @return BelongsToMany
     */
    public function types(): BelongsToMany
    {
        return $this->belongsToMany(EventType::class, 'events_events-types')->withPivot('prices');
    }
}
