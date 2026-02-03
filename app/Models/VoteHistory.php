<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vote_history';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vote_id',
        'poll_id',
        'poll_option_id',
        'user_id',
        'ip_address',
        'action',
        'user_agent',
        'metadata',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the vote that owns the history entry.
     */
    public function vote()
    {
        return $this->belongsTo(Vote::class);
    }

    /**
     * Get the poll that owns the history entry.
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
     * Get the user that performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
