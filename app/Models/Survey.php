<!-- Add this CSS to make the preview properly aligned -->
@push('styles')
<style>
/* Survey preview styles for better alignment */
#customization-preview .form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
}

#customization-preview .form-check {
    margin-bottom: 0.5rem;
}

#customization-preview .option-choice-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0.5rem 0;
}

#customization-preview .card.p-3 {
    transition: all 0.3s ease;
}

#customization-preview .progress-preview {
    background: #f8f9fa;
    border-radius: 4px;
    margin: 1rem 0;
}

/* Survey question preview styling */
.survey-preview-container .card.mb-3 {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
}

.survey-preview-container .card.mb-3:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.survey-preview-container .form-label {
    font-size: 1rem;
    margin-bottom: 0.75rem;
}

.survey-preview-container textarea.form-control,
.survey-preview-container input.form-control {
    min-height: 38px;
}

.survey-preview-container .form-check-input {
    margin-top: 0.25rem;
}
</style>
@endpush
