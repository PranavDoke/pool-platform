<?php

namespace App\Services;

use App\Models\Vote;
use App\Models\VoteHistory;
use Illuminate\Support\Facades\DB;

/**
 * Core PHP Voting Service
 * Handles custom voting logic without relying heavily on Laravel helpers
 */
class VotingService
{
    /**
     * Process a vote with Core PHP logic
     *
     * @param int $pollId
     * @param int $optionId
     * @param string $ipAddress
     * @param int|null $userId
     * @return array
     */
    public function processVote($pollId, $optionId, $ipAddress, $userId = null)
    {
        try {
            DB::beginTransaction();

            // Check if IP has already voted (Core PHP validation)
            if ($this->hasIpVoted($pollId, $ipAddress)) {
                throw new \Exception('You have already voted on this poll. Each IP address can only vote once.');
            }

            // Get user agent
            $userAgent = $this->getUserAgent();

            // Insert vote using raw approach
            $vote = new Vote();
            $vote->poll_id = $pollId;
            $vote->poll_option_id = $optionId;
            $vote->ip_address = $ipAddress;
            $vote->user_id = $userId;
            $vote->user_agent = $userAgent;
            $vote->voted_at = date('Y-m-d H:i:s');
            $vote->is_active = 1;
            $vote->save();

            // Log to vote history
            $this->logVoteHistory($vote->id, 'voted', [
                'poll_id' => $pollId,
                'option_id' => $optionId,
                'ip' => $ipAddress
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Vote submitted successfully!',
                'vote_id' => $vote->id
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if IP has already voted on this poll (Core PHP)
     *
     * @param int $pollId
     * @param string $ipAddress
     * @return bool
     */
    private function hasIpVoted($pollId, $ipAddress)
    {
        // Use raw query for Core PHP approach
        $query = "SELECT COUNT(*) as count FROM votes 
                  WHERE poll_id = ? AND ip_address = ? AND is_active = 1";
        
        $result = DB::select($query, [$pollId, $ipAddress]);
        
        return $result[0]->count > 0;
    }

    /**
     * Get user agent from server variables (Core PHP)
     *
     * @return string|null
     */
    private function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    /**
     * Log vote history
     *
     * @param int $voteId
     * @param string $action
     * @param array $metadata
     * @return void
     */
    private function logVoteHistory($voteId, $action, $metadata = [])
    {
        $vote = Vote::find($voteId);

        if (!$vote) {
            return;
        }

        VoteHistory::create([
            'vote_id' => $voteId,
            'poll_id' => $vote->poll_id,
            'poll_option_id' => $vote->poll_option_id,
            'user_id' => $vote->user_id,
            'ip_address' => $vote->ip_address,
            'action' => $action,
            'user_agent' => $vote->user_agent,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get vote statistics for a poll
     *
     * @param int $pollId
     * @return array
     */
    public function getPollResults($pollId)
    {
        // Use raw SQL for Core PHP approach
        $query = "SELECT 
                    po.id,
                    po.option_text,
                    COUNT(v.id) as votes
                  FROM poll_options po
                  LEFT JOIN votes v ON v.poll_option_id = po.id AND v.is_active = 1
                  WHERE po.poll_id = ?
                  GROUP BY po.id, po.option_text
                  ORDER BY po.display_order";

        $results = DB::select($query, [$pollId]);

        $totalVotes = array_sum(array_column($results, 'votes'));

        return [
            'success' => true,
            'results' => $results,
            'total_votes' => $totalVotes,
            'timestamp' => date('c')
        ];
    }
}
