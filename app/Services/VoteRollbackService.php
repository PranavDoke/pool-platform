<?php

namespace App\Services;

use App\Models\Vote;
use App\Models\VoteHistory;
use Illuminate\Support\Facades\DB;

/**
 * Core PHP Vote Rollback Service
 * Handles IP release and vote rollback logic
 */
class VoteRollbackService
{
    /**
     * Release an IP address and rollback the vote (Core PHP logic)
     *
     * @param int $pollId
     * @param string $ipAddress
     * @return array
     */
    public function releaseIpVote($pollId, $ipAddress)
    {
        try {
            DB::beginTransaction();

            // Find the active vote for this IP and poll
            $vote = Vote::where('poll_id', $pollId)
                        ->where('ip_address', $ipAddress)
                        ->where('is_active', 1)
                        ->first();

            if (!$vote) {
                throw new \Exception('No active vote found for this IP address on this poll');
            }

            // Store vote details before deactivation
            $voteDetails = [
                'vote_id' => $vote->id,
                'option_id' => $vote->poll_option_id,
                'voted_at' => $vote->voted_at,
            ];

            // Mark vote as inactive (soft delete approach)
            $vote->is_active = 0;
            $vote->save();

            // Log the release action to vote history
            VoteHistory::create([
                'vote_id' => $vote->id,
                'poll_id' => $vote->poll_id,
                'poll_option_id' => $vote->poll_option_id,
                'user_id' => $vote->user_id,
                'ip_address' => $vote->ip_address,
                'action' => 'released',
                'user_agent' => $vote->user_agent,
                'metadata' => [
                    'released_by' => 'admin',
                    'previous_vote' => $voteDetails
                ],
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'IP address released successfully. The IP can now vote again.',
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
     * Get vote history for an IP on a specific poll
     *
     * @param int $pollId
     * @param string $ipAddress
     * @return array
     */
    public function getIpVoteHistory($pollId, $ipAddress)
    {
        $history = VoteHistory::where('poll_id', $pollId)
                              ->where('ip_address', $ipAddress)
                              ->with('pollOption')
                              ->orderBy('created_at', 'desc')
                              ->get();

        return [
            'success' => true,
            'history' => $history,
            'count' => $history->count()
        ];
    }

    /**
     * Get all IPs that voted on a poll (for admin)
     *
     * @param int $pollId
     * @return array
     */
    public function getPollVoterIps($pollId)
    {
        // Use raw SQL for Core PHP approach
        $query = "SELECT 
                    v.ip_address,
                    v.is_active,
                    po.option_text,
                    v.voted_at,
                    v.user_agent,
                    COUNT(vh.id) as history_count
                  FROM votes v
                  LEFT JOIN poll_options po ON po.id = v.poll_option_id
                  LEFT JOIN vote_history vh ON vh.vote_id = v.id
                  WHERE v.poll_id = ?
                  GROUP BY v.ip_address, v.is_active, po.option_text, v.voted_at, v.user_agent
                  ORDER BY v.voted_at DESC";

        $voters = DB::select($query, [$pollId]);

        return [
            'success' => true,
            'voters' => $voters,
            'total' => count($voters)
        ];
    }

    /**
     * Check if IP has previous vote history on this poll
     *
     * @param int $pollId
     * @param string $ipAddress
     * @return bool
     */
    public function hasPreviousVoteHistory($pollId, $ipAddress)
    {
        $count = VoteHistory::where('poll_id', $pollId)
                            ->where('ip_address', $ipAddress)
                            ->count();

        return $count > 0;
    }

    /**
     * Release multiple IPs at once (bulk operation)
     *
     * @param int $pollId
     * @param array $ipAddresses
     * @return array
     */
    public function releaseMultipleIps($pollId, array $ipAddresses)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($ipAddresses as $ip) {
            $result = $this->releaseIpVote($pollId, $ip);
            
            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = [
                    'ip' => $ip,
                    'error' => $result['error']
                ];
            }
        }

        return $results;
    }
}
