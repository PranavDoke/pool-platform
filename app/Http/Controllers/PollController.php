<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Services\VotingService;
use App\Services\IpValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PollController extends Controller
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
     * Display a listing of active polls
     */
    public function index()
    {
        $polls = Poll::active()
            ->with('options')
            ->withCount('activeVotes')
            ->latest()
            ->get();

        return view('polls.index', compact('polls'));
    }

    /**
     * Show the form for creating a new poll
     */
    public function create()
    {
        return view('polls.create');
    }

    /**
     * Store a newly created poll
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create poll
        $poll = Poll::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => true,
        ]);

        // Create poll options
        foreach ($request->options as $index => $optionText) {
            if (!empty($optionText)) {
                PollOption::create([
                    'poll_id' => $poll->id,
                    'option_text' => $optionText,
                    'display_order' => $index,
                ]);
            }
        }

        return redirect()->route('polls.show', $poll->id)
            ->with('success', 'Poll created successfully!');
    }

    /**
     * Display the specified poll
     */
    public function show($id)
    {
        $poll = Poll::with(['options.activeVotes', 'user'])
            ->findOrFail($id);

        // Check if current IP has voted
        $ipAddress = $this->ipValidationService->getClientIp();
        $hasVoted = \App\Models\Vote::where('poll_id', $id)
            ->where('ip_address', $ipAddress)
            ->where('is_active', true)
            ->exists();

        // Get vote counts for each option
        $results = $this->votingService->getPollResults($id);

        return view('polls.show', compact('poll', 'hasVoted', 'results'));
    }

    /**
     * Get poll results (AJAX endpoint)
     */
    public function getResults($id)
    {
        $results = $this->votingService->getPollResults($id);
        return response()->json($results);
    }

    /**
     * Toggle poll active status
     */
    public function toggleStatus($id)
    {
        $poll = Poll::findOrFail($id);

        // Only creator or admin can toggle
        if ($poll->user_id !== Auth::id() && !Auth::user()->is_admin) {
            return back()->with('error', 'Unauthorized action.');
        }

        $poll->is_active = !$poll->is_active;
        $poll->save();

        $status = $poll->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Poll has been {$status}.");
    }
}
