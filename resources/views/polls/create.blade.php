@extends('layouts.app')

@section('title', 'Create Poll')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Poll</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('polls.store') }}" id="createPollForm">
                    @csrf

                    <div class="mb-4">
                        <label for="title" class="form-label fw-bold">Poll Question *</label>
                        <input type="text" 
                               class="form-control form-control-lg @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               required
                               placeholder="e.g., What is your favorite programming language?">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Description (Optional)</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Add additional context or details about this poll...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Poll Options *</label>
                        <small class="text-muted">(Minimum 2 options required)</small>
                        
                        <div id="optionsContainer">
                            <div class="input-group mb-2">
                                <span class="input-group-text">1</span>
                                <input type="text" class="form-control" name="options[]" placeholder="Option 1" required>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">2</span>
                                <input type="text" class="form-control" name="options[]" placeholder="Option 2" required>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm" id="addOptionBtn">
                            <i class="bi bi-plus"></i> Add Another Option
                        </button>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Create Poll
                        </button>
                        <a href="{{ route('polls.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let optionCount = 2;

    $('#addOptionBtn').click(function() {
        optionCount++;
        const optionHtml = `
            <div class="input-group mb-2 option-item">
                <span class="input-group-text">${optionCount}</span>
                <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount}">
                <button type="button" class="btn btn-outline-danger remove-option">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        $('#optionsContainer').append(optionHtml);
    });

    $(document).on('click', '.remove-option', function() {
        $(this).closest('.option-item').remove();
        updateOptionNumbers();
    });

    function updateOptionNumbers() {
        $('#optionsContainer .input-group').each(function(index) {
            $(this).find('.input-group-text').text(index + 1);
            $(this).find('input').attr('placeholder', 'Option ' + (index + 1));
        });
        optionCount = $('#optionsContainer .input-group').length;
    }
});
</script>
@endsection
