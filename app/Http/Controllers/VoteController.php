<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Services\VotingService;
use App\Services\IpValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    protected $votingService;
    protected $ipValidationService;

    public function __construct(
        VotingService $votingService,
        IpValidationService $ipValidationService
    ) {
        $this->middleware('auth');
        $this->votingService = $votingService;
        $this->ipValidationService = $ipValidationService;
    }

    /**
     * Submit a vote (AJAX endpoint)
     */
    public function store(Request $request, $pollId)
    {
        // Validate request
        $request->validate([
            'option_id' => 'required|exists:poll_options,id',
        ]);

        // Get and validate IP address using Core PHP service
        $ipAddress = $this->ipValidationService->getClientIp();
        $ipValidation = $this->ipValidationService->validateIp($ipAddress);

        if (!$ipValidation['valid']) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid IP address detected.'
            ], 400);
        }

        // Verify poll exists and is active
        $poll = Poll::findOrFail($pollId);
        if (!$poll->is_active) {
            return response()->json([
                'success' => false,
                'error' => 'This poll is no longer active.'
            ], 403);
        }

        // Process vote using Core PHP service
        $result = $this->votingService->processVote(
            $pollId,
            $request->option_id,
            $ipAddress,
            Auth::id()
        );

        if ($result['success']) {
            return response()->json($result, 200);
        } else {
            return response()->json($result, 400);
        }
    }
}
