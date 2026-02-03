<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'poll_id',
        'option_text',
        'display_order',
    ];

    /**
     * Get the poll that owns the option.
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get the votes for the option.
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
     * Get the vote count for this option.
     */
    public function getVoteCountAttribute()
    {
        return $this->activeVotes()->count();
    }
}
