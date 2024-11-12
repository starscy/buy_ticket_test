<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Модель тип события
 */
class EventType extends Model
{
    protected $table = 'events-types';
    protected $guarded = [];

    /**
     * Связь с событиями
     *
     * @return BelongsToMany
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'events_events-types')->withPivot('prices');
    }
}
