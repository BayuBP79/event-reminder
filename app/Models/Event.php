<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'user_id', 'title', 'description', 'event_date'];
    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function scopeSearch($query, $value){
        $query->where('title', 'like', "%{$value}%")->orWhere('description', 'like', "%{$value}%");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public static function generateEventId()
    {
        $prefix = 'EVT';
        $date = now()->format('Ymd');
        $latestEvent = self::whereDate('created_at', now()->toDateString())
                           ->orderBy('created_at', 'desc')
                           ->first();

        $sequence = $latestEvent ? (int) substr($latestEvent->id, -4) + 1 : 1;
        $sequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$sequence}";
    }
}
