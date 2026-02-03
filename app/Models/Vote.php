<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'poll_id',
        'poll_option_id',
        'user_id',
        'ip_address',
        'user_agent',
        'voted_at',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'voted_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the poll that owns the vote.
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get the option that was voted for.
     */
    public function pollOption()
    {
        return $this->belongsTo(PollOption::class);
    }

    /**
     * Get the user that cast the vote.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vote history entries.
     */
    public function history()
    {
        return $this->hasMany(VoteHistory::class);
    }

    /**
     * Scope a query to only include active votes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
