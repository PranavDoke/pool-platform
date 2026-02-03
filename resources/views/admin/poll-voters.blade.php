@extends('layouts.app')

@section('title', 'Manage Poll Voters - ' . $poll->title)

@section('content')
<div class="row">
    <div class="col-12">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light mb-3">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
        
        <div class="card">
            <div class="card-header bg-warning">
                <h4 class="mb-0">
                    <i class="bi bi-people"></i> Voter Management: {{ $poll->title }}
                </h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Total Voters:</strong> {{ $total }}
                    | You can release IP addresses to allow users to vote again.
                </div>

                @if(empty($voters))
                <p class="text-center text-muted">No votes recorded yet for this poll.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Status</th>
                                <th>Vote Choice</th>
                                <th>Voted At</th>
                                <th>History Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($voters as $voter)
                            <tr id="voter-row-{{ md5($voter->ip_address) }}">
                                <td>
                                    <code>{{ $voter->ip_address }}</code>
                                </td>
                                <td>
                                    @if($voter->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Released</span>
                                    @endif
                                </td>
                                <td>{{ $voter->option_text ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($voter->voted_at)->format('M d, Y H:i:s') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $voter->history_count }}</span> entries
                                </td>
                                <td>
                                    @if($voter->is_active)
                                    <button class="btn btn-sm btn-danger release-ip-btn" 
                                            data-ip="{{ $voter->ip_address }}"
                                            data-poll-id="{{ $poll->id }}">
                                        <i class="bi bi-unlock"></i> Release IP
                                    </button>
                                    @else
                                    <span class="text-muted">Already Released</span>
                                    @endif
                                    
                                    <button class="btn btn-sm btn-outline-info view-history-btn"
                                            data-ip="{{ $voter->ip_address }}"
                                            data-poll-id="{{ $poll->id }}">
                                        <i class="bi bi-clock-history"></i> History
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vote History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="historyContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Release IP functionality
    $('.release-ip-btn').click(function() {
        const btn = $(this);
        const ipAddress = btn.data('ip');
        const pollId = btn.data('poll-id');
        
        if (!confirm(`Are you sure you want to release IP ${ipAddress}?\n\nThis will allow them to vote again.`)) {
            return;
        }

        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Releasing...');

        $.ajax({
            url: `/api/admin/polls/${pollId}/release-ip`,
            method: 'POST',
            data: { ip_address: ipAddress },
            success: function(response) {
                if (response.success) {
                    alert('IP released successfully! The user can now vote again.');
                    location.reload();
                } else {
                    alert('Error: ' + response.error);
                    btn.prop('disabled', false).html('<i class="bi bi-unlock"></i> Release IP');
                }
            },
            error: function(xhr) {
                alert('Error releasing IP: ' + (xhr.responseJSON?.error || 'Unknown error'));
                btn.prop('disabled', false).html('<i class="bi bi-unlock"></i> Release IP');
            }
        });
    });

    // View history functionality
    $('.view-history-btn').click(function() {
        const ipAddress = $(this).data('ip');
        const pollId = $(this).data('poll-id');

        $('#historyModal').modal('show');
        $('#historyContent').html('<div class="text-center"><div class="spinner-border"></div></div>');

        $.ajax({
            url: `/api/admin/polls/${pollId}/ip-history?ip=${ipAddress}`,
            method: 'GET',
            success: function(response) {
                if (response.success && response.history.length > 0) {
                    let historyHtml = '<div class="timeline">';
                    response.history.forEach(function(entry) {
                        const date = new Date(entry.created_at);
                        const actionBadge = entry.action === 'voted' ? 'bg-success' : 
                                          entry.action === 'released' ? 'bg-danger' : 'bg-info';
                        
                        historyHtml += `
                            <div class="mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between">
                                    <strong><span class="badge ${actionBadge}">${entry.action.toUpperCase()}</span></strong>
                                    <small class="text-muted">${date.toLocaleString()}</small>
                                </div>
                                <div class="mt-2">
                                    <strong>Option:</strong> ${entry.poll_option?.option_text || 'N/A'}<br>
                                    <strong>IP:</strong> <code>${entry.ip_address}</code>
                                </div>
                            </div>
                        `;
                    });
                    historyHtml += '</div>';
                    $('#historyContent').html(historyHtml);
                } else {
                    $('#historyContent').html('<p class="text-muted text-center">No history found.</p>');
                }
            },
            error: function() {
                $('#historyContent').html('<p class="text-danger text-center">Error loading history.</p>');
            }
        });
    });
});
</script>
@endsection
