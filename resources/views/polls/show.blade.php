@extends('layouts.app')

@section('title', $poll->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card poll-container" data-poll-id="{{ $poll->id }}">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">{{ $poll->title }}</h3>
                @if($poll->description)
                <p class="mb-0 mt-2 opacity-75">{{ $poll->description }}</p>
                @endif
            </div>

            <div class="card-body p-4">
                <!-- Alert Messages Container -->
                <div id="voteAlerts"></div>

                @if(!$hasVoted)
                <!-- Voting Form (if user hasn't voted) -->
                <div id="votingSection">
                    <h5 class="mb-3">Choose your answer:</h5>
                    @foreach($poll->options as $option)
                    <div class="poll-option mb-3">
                        <button class="vote-btn btn btn-outline-primary" 
                                data-option-id="{{ $option->id }}"
                                data-option-text="{{ $option->option_text }}">
                            <i class="bi bi-hand-index"></i> {{ $option->option_text }}
                        </button>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> You have already voted on this poll.
                </div>
                @endif

                <hr class="my-4">

                <!-- Results Section -->
                <div id="resultsSection">
                    <h5 class="mb-3">
                        <i class="bi bi-bar-chart-fill"></i> Live Results
                        <span class="badge bg-secondary" id="total-votes">{{ $results['total_votes'] }}</span> votes
                    </h5>

                    <div id="pollResults">
                        @foreach($results['results'] as $result)
                        <div class="mb-4" id="option-{{ $result->id }}">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>{{ $result->option_text }}</strong>
                                <span>
                                    <span class="vote-count">{{ $result->votes }}</span> votes
                                    (<span class="percentage">{{ $results['total_votes'] > 0 ? number_format(($result->votes / $results['total_votes']) * 100, 1) : 0 }}</span>%)
                                </span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" 
                                     role="progressbar" 
                                     style="width: {{ $results['total_votes'] > 0 ? ($result->votes / $results['total_votes']) * 100 : 0 }}%;" 
                                     aria-valuenow="{{ $results['total_votes'] > 0 ? ($result->votes / $results['total_votes']) * 100 : 0 }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="text-muted text-center mt-3">
                        <small>
                            <i class="bi bi-arrow-clockwise"></i> Updates automatically every second
                            <br>
                            Last updated: <span id="last-update">Just now</span>
                        </small>
                    </div>
                </div>
            </div>

            <div class="card-footer text-muted">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-person"></i> Created by {{ $poll->user->name }}
                        <br>
                        <i class="bi bi-clock"></i> {{ $poll->created_at->diffForHumans() }}
                    </div>
                    <div>
                        <a href="{{ route('polls.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Back to Polls
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(Auth::user()->is_admin)
        <div class="card mt-3">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="bi bi-shield-check"></i> Admin Controls</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.poll.voters', $poll->id) }}" class="btn btn-warning">
                    <i class="bi bi-people"></i> View Voter IPs & Manage
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/poll-voting.js') }}"></script>
@endsection
