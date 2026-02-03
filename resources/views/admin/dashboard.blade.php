@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="text-white mb-4">
            <i class="bi bi-shield-check"></i> Admin Dashboard
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-warning">
                <h4 class="mb-0">All Polls Management</h4>
            </div>
            <div class="card-body">
                @if($polls->isEmpty())
                <p class="text-center text-muted">No polls found.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Creator</th>
                                <th>Active Votes</th>
                                <th>Total Votes</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($polls as $poll)
                            <tr>
                                <td>{{ $poll->id }}</td>
                                <td>
                                    <strong>{{ Str::limit($poll->title, 50) }}</strong>
                                </td>
                                <td>
                                    @if($poll->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $poll->user->name }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $poll->active_votes_count }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $poll->votes_count }}</span>
                                </td>
                                <td>{{ $poll->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('polls.show', $poll->id) }}" class="btn btn-outline-primary" title="View Poll">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.poll.voters', $poll->id) }}" class="btn btn-outline-warning" title="Manage Voters">
                                            <i class="bi bi-people"></i>
                                        </a>
                                        <form action="{{ route('polls.toggle', $poll->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary" title="Toggle Status">
                                                <i class="bi bi-power"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $polls->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
