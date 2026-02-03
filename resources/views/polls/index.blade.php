@extends('layouts.app')

@section('title', 'All Polls')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-white">
                <i class="bi bi-list-ul"></i> Active Polls
            </h1>
            <a href="{{ route('polls.create') }}" class="btn btn-light btn-lg">
                <i class="bi bi-plus-circle"></i> Create New Poll
            </a>
        </div>
    </div>
</div>

@if($polls->isEmpty())
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 64px; color: #ccc;"></i>
                <h3 class="mt-3">No Active Polls</h3>
                <p class="text-muted">Create your first poll to get started!</p>
                <a href="{{ route('polls.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create Poll
                </a>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    @foreach($polls as $poll)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 poll-card" data-poll-id="{{ $poll->id }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title mb-0">{{ $poll->title }}</h5>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle"></i> Active
                    </span>
                </div>

                @if($poll->description)
                <p class="card-text text-muted">{{ Str::limit($poll->description, 100) }}</p>
                @endif

                <div class="mb-3">
                    <small class="text-muted">
                        <i class="bi bi-people"></i> {{ $poll->active_votes_count }} votes
                        &bull; {{ $poll->options->count() }} options
                    </small>
                </div>

                <a href="{{ route('polls.show', $poll->id) }}" class="btn btn-primary w-100">
                    <i class="bi bi-eye"></i> View & Vote
                </a>

                @if($poll->user_id === Auth::id() || Auth::user()->is_admin)
                <div class="mt-2">
                    <form action="{{ route('polls.toggle', $poll->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="bi bi-power"></i> Deactivate
                        </button>
                    </form>
                </div>
                @endif
            </div>
            <div class="card-footer text-muted">
                <small>
                    <i class="bi bi-person"></i> Created by {{ $poll->user->name }}
                    <br>
                    <i class="bi bi-clock"></i> {{ $poll->created_at->diffForHumans() }}
                </small>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-refresh poll cards every 5 seconds using AJAX
    setInterval(function() {
        $('.poll-card').each(function() {
            const pollId = $(this).data('poll-id');
            const card = $(this);
            
            $.ajax({
                url: `/api/polls/${pollId}/results`,
                method: 'GET',
                success: function(data) {
                    if (data.success) {
                        card.find('.text-muted small').first().html(
                            `<i class="bi bi-people"></i> ${data.total_votes} votes &bull; ${data.results.length} options`
                        );
                    }
                }
            });
        });
    }, 5000);
});
</script>
@endsection
