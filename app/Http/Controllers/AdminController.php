<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\Vote;
use App\Services\VoteRollbackService;
use App\Services\IpValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $rollbackService;
    protected $ipValidationService;

    public function __construct(
        VoteRollbackService $rollbackService,
        IpValidationService $ipValidationService
    ) {
        $this->middleware('auth');
        $this->rollbackService = $rollbackService;
        $this->ipValidationService = $ipValidationService;
    }

    /**
     * Show admin dashboard
     */
    public function index()
    {
        // Check if user is admin
        if (!Auth::user()->is_admin) {
            return redirect()->route('polls.index')
                ->with('error', 'Unauthorized access.');
        }

        $polls = Poll::withCount(['activeVotes', 'votes'])
            ->latest()
            ->paginate(10);

        return view('admin.dashboard', compact('polls'));
    }

    /**
     * View IPs that voted on a specific poll
     */
    public function showPollVoters($pollId)
    {
        if (!Auth::user()->is_admin) {
            return redirect()->route('polls.index')
                ->with('error', 'Unauthorized access.');
        }

        $poll = Poll::with('options')->findOrFail($pollId);
        $votersData = $this->rollbackService->getPollVoterIps($pollId);

        return view('admin.poll-voters', [
            'poll' => $poll,
            'voters' => $votersData['voters'],
            'total' => $votersData['total']
        ]);
    }

    /**
     * Release an IP address (vote rollback)
     */
    public function releaseIp(Request $request, $pollId)
    {
        if (!Auth::user()->is_admin) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized access.'
            ], 403);
        }

        $request->validate([
            'ip_address' => 'required|ip',
        ]);

        $result = $this->rollbackService->releaseIpVote($pollId, $request->ip_address);

        return response()->json($result);
    }

    /**
     * View vote history for an IP
     */
    public function showIpHistory($pollId, Request $request)
    {
        if (!Auth::user()->is_admin) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized access.'
            ], 403);
        }

        $ipAddress = $request->query('ip');
        if (!$ipAddress) {
            return response()->json([
                'success' => false,
                'error' => 'IP address is required.'
            ], 400);
        }

        $historyData = $this->rollbackService->getIpVoteHistory($pollId, $ipAddress);

        return response()->json($historyData);
    }
}
