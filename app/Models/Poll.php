<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'is_active',
        'start_date',
        'end_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    /**
     * Get the user that created the poll.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the options for the poll.
     */
    public function options()
    {
        return $this->hasMany(PollOption::class)->orderBy('display_order');
    }

    /**
     * Get the votes for the poll.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Get active votes only.
     */
    public function activeVotes()
    {
        return $this->hasMany(Vote::class)->where('is_active', true);
    }

    /**
     * Get the vote history for the poll.
     */
    public function voteHistory()
    {
        return $this->hasMany(VoteHistory::class);
    }

    /**
     * Scope a query to only include active polls.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
