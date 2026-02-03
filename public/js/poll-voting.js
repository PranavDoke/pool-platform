/**
 * Real-Time Poll Voting and Results System
 * Features:
 * - AJAX vote submission (no page reload)
 * - Real-time results updates every 1 second
 * - IP-based vote restriction with instant feedback
 */

(function($) {
    'use strict';

    // Poll Updater Class - Handles real-time result updates
    class PollUpdater {
        constructor(pollId, updateInterval = 1000) {
            this.pollId = pollId;
            this.updateInterval = updateInterval;
            this.isPolling = false;
            this.pollTimer = null;
            this.lastUpdateTime = null;
        }

        start() {
            if (this.isPolling) return;
            this.isPolling = true;
            this.poll();
        }

        stop() {
            this.isPolling = false;
            if (this.pollTimer) {
                clearTimeout(this.pollTimer);
            }
        }

        poll() {
            if (!this.isPolling) return;

            $.ajax({
                url: `/api/polls/${this.pollId}/results`,
                method: 'GET',
                dataType: 'json',
                success: (data) => {
                    this.updateResults(data);
                    this.scheduleNext();
                },
                error: (xhr, status, error) => {
                    console.error('Polling error:', error);
                    this.scheduleNext();
                }
            });
        }

        scheduleNext() {
            if (!this.isPolling) return;
            this.pollTimer = setTimeout(() => this.poll(), this.updateInterval);
        }

        updateResults(data) {
            if (!data.success || !data.results) return;

            const totalVotes = data.total_votes || 0;

            // Update each option's results
            data.results.forEach((option) => {
                const percentage = totalVotes > 0 
                    ? ((option.votes / totalVotes) * 100).toFixed(1) 
                    : 0;

                const optionElement = $(`#option-${option.id}`);
                
                if (optionElement.length) {
                    // Update progress bar
                    const progressBar = optionElement.find('.progress-bar');
                    progressBar.css('width', `${percentage}%`);
                    progressBar.attr('aria-valuenow', percentage);

                    // Update vote count
                    optionElement.find('.vote-count').text(option.votes);

                    // Update percentage
                    optionElement.find('.percentage').text(percentage);
                }
            });

            // Update total votes
            $('#total-votes').text(totalVotes);

            // Update timestamp
            this.lastUpdateTime = new Date();
            $('#last-update').text(this.lastUpdateTime.toLocaleTimeString());
        }
    }

    // Vote Handler Class - Handles vote submission
    class VoteHandler {
        constructor(pollId) {
            this.pollId = pollId;
            this.hasVoted = false;
        }

        submitVote(optionId, optionText) {
            if (this.hasVoted) {
                this.showError('You have already voted on this poll!');
                return;
            }

            // Disable all vote buttons
            $('.vote-btn').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Submitting...');

            $.ajax({
                url: `/api/polls/${this.pollId}/vote`,
                method: 'POST',
                dataType: 'json',
                data: {
                    option_id: optionId
                },
                success: (response) => {
                    if (response.success) {
                        this.hasVoted = true;
                        this.showSuccess(response.message || 'Vote submitted successfully!');
                        this.disableVoting();
                        
                        // Hide voting section after a delay
                        setTimeout(() => {
                            $('#votingSection').fadeOut();
                        }, 2000);
                    } else {
                        this.showError(response.error || 'Failed to submit vote');
                        this.enableVoting();
                    }
                },
                error: (xhr, status, error) => {
                    const errorMessage = xhr.responseJSON?.error || 
                                       xhr.responseJSON?.message || 
                                       'Network error. Please try again.';
                    this.showError(errorMessage);
                    this.enableVoting();
                }
            });
        }

        disableVoting() {
            $('.vote-btn').prop('disabled', true)
                         .removeClass('btn-outline-primary')
                         .addClass('btn-secondary')
                         .html('<i class="bi bi-check-circle"></i> Voted');
        }

        enableVoting() {
            $('.vote-btn').prop('disabled', false)
                         .html(function() {
                             return '<i class="bi bi-hand-index"></i> ' + $(this).data('option-text');
                         });
        }

        showSuccess(message) {
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('#voteAlerts').html(alertHtml);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                $('.alert').fadeOut();
            }, 5000);
        }

        showError(message) {
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('#voteAlerts').html(alertHtml);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                $('.alert').fadeOut();
            }, 5000);
        }
    }

    // Initialize on page load
    $(document).ready(function() {
        const pollContainer = $('.poll-container');
        
        if (pollContainer.length === 0) {
            return; // Not on a poll page
        }

        const pollId = pollContainer.data('poll-id');

        if (!pollId) {
            console.error('Poll ID not found');
            return;
        }

        // Start real-time updates (1 second interval)
        const updater = new PollUpdater(pollId, 1000);
        updater.start();

        // Initialize vote handler
        const voteHandler = new VoteHandler(pollId);

        // Attach vote button click handlers
        $('.vote-btn').on('click', function(e) {
            e.preventDefault();
            const optionId = $(this).data('option-id');
            const optionText = $(this).data('option-text');
            
            // Confirm vote
            if (confirm(`Are you sure you want to vote for:\n"${optionText}"?\n\nYou can only vote once per poll.`)) {
                voteHandler.submitVote(optionId, optionText);
            }
        });

        // Stop polling when user leaves the page
        $(window).on('beforeunload', function() {
            updater.stop();
        });

        console.log('Poll voting system initialized for poll ID:', pollId);
    });

})(jQuery);
