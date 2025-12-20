@props(['lesson', 'isCompleted' => false])

<div class="lesson-content-wrapper" id="lesson-{{ $lesson->id }}">
    <h2 class="fw-bold text-gray-900 mb-5">{{ $lesson->title }}</h2>

    {{-- Text/HTML Content --}}
    @if($lesson->content_type === 'text_html')
        <div class="lesson-text-content fs-6 text-gray-700 mb-7" id="lesson-text-content">
            {!! $lesson->content ?? '' !!}
        </div>
    @endif

    {{-- Video Content --}}
    @if($lesson->content_type === 'video')
        <div class="ratio ratio-16x9 mb-7">
            @if($lesson->video_embed_url)
                <iframe src="{{ $lesson->video_embed_url }}"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                </iframe>
            @elseif($lesson->content_url)
                {{-- Fallback: try to parse URL --}}
                @php
                    $videoId = null;
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\?\/]+)/', $lesson->content_url, $matches)) {
                        $videoId = $matches[1];
                    } elseif (preg_match('/vimeo\.com\/(\d+)/', $lesson->content_url, $matches)) {
                        $videoId = $matches[1];
                    }
                @endphp
                @if($videoId)
                    @if(str_contains($lesson->content_url, 'vimeo'))
                        <iframe src="https://player.vimeo.com/video/{{ $videoId }}"
                                frameborder="0"
                                allow="autoplay; fullscreen; picture-in-picture"
                                allowfullscreen>
                        </iframe>
                    @else
                        <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                        </iframe>
                    @endif
                @endif
            @endif
        </div>
    @endif

    {{-- PDF Content --}}
    @if($lesson->content_type === 'pdf')
        <div class="pdf-viewer-wrapper mb-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-gray-800 mb-0">{{ __('PDF Document') }}</h5>
                @if($lesson->file_path)
                    <a href="{{ Storage::url($lesson->file_path) }}"
                       class="btn btn-sm btn-light-primary"
                       download>
                        {!! getIcon('cloud-download', 'fs-4 me-1') !!}
                        {{ __('Download PDF') }}
                    </a>
                @endif
            </div>
            @if($lesson->file_path)
                <iframe src="{{ Storage::url($lesson->file_path) }}"
                        class="w-100"
                        style="min-height: 600px;"
                        frameborder="0">
                </iframe>
            @elseif($lesson->content_url)
                <iframe src="{{ $lesson->content_url }}"
                        class="w-100"
                        style="min-height: 600px;"
                        frameborder="0">
                </iframe>
            @endif
        </div>
    @endif

    {{-- Video Upload Content --}}
    @if($lesson->content_type === 'video_upload')
        <div class="video-upload-wrapper mb-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-gray-800 mb-0">{{ __('Video Lesson') }}</h5>
                @if($lesson->file_path)
                    <a href="{{ Storage::url($lesson->file_path) }}"
                       class="btn btn-sm btn-light-primary"
                       download="{{ Str::slug($lesson->title) }}.{{ pathinfo($lesson->file_path, PATHINFO_EXTENSION) }}">
                        {!! getIcon('cloud-download', 'fs-4 me-1') !!}
                        {{ __('Download Video') }}
                    </a>
                @endif
            </div>
            @if($lesson->file_path)
                <div class="ratio ratio-16x9 rounded overflow-hidden bg-dark">
                    <video controls class="w-100" preload="metadata">
                        <source src="{{ Storage::url($lesson->file_path) }}" type="video/mp4">
                        {{ __('Your browser does not support the video tag.') }}
                    </video>
                </div>
            @endif
        </div>
    @endif

    {{-- External Link Content --}}
    @if($lesson->content_type === 'external_link')
        <div class="alert alert-primary d-flex align-items-center mb-7">
            {!! getIcon('information-5', 'fs-2hx text-primary me-4') !!}
            <div class="d-flex flex-column flex-grow-1">
                <h4 class="mb-1 text-gray-900">{{ __('External Resource') }}</h4>
                <span class="text-gray-700 fs-6">{{ __('This lesson contains an external resource. Click the button below to open it in a new window.') }}</span>
            </div>
        </div>
        @if($lesson->content_url)
            <a href="{{ $lesson->content_url }}"
               class="btn btn-primary btn-lg"
               target="_blank"
               rel="noopener noreferrer">
                {!! getIcon('arrow-right-square', 'fs-3 me-2 text-white') !!}
                {{ __('Open External Resource') }}
            </a>
        @endif
    @endif

    {{-- Completion Section --}}
    <x-student.content-completion
        type="lesson"
        :itemId="$lesson->id"
        :isCompleted="$isCompleted"
    />
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('mark-complete-btn');
    if (!btn || btn.dataset.completed === 'true') return;

    btn.addEventListener('click', function() {
        if (btn.disabled) return;

        const lessonId = btn.dataset.lessonId;
        btn.disabled = true;
        btn.classList.add('btn-marking-complete');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __('Processing...') }}';

        fetch(`/student/lessons/${lessonId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show animated success card
                showCompletionSuccess(lessonId, data.data);

                // Show success notification
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.message || '{{ __('Lesson marked as complete!') }}');
                }

                // Update progress indicators without page reload
                updateProgressIndicators(data.data);

                // Update sidebar item if present
                updateSidebarItem(lessonId);
            } else {
                btn.disabled = false;
                btn.classList.remove('btn-marking-complete');
                btn.innerHTML = '{!! getIcon('check', 'fs-3 me-2') !!}{{ __('Mark as Complete') }}';
                if (typeof toastr !== 'undefined') {
                    toastr.error(data.message || '{{ __('Failed to mark lesson as complete. Please try again.') }}');
                }
            }
        })
        .catch(error => {
            console.error('Error marking lesson complete:', error);
            btn.disabled = false;
            btn.classList.remove('btn-marking-complete');
            btn.innerHTML = '{!! getIcon('check', 'fs-3 me-2') !!}{{ __('Mark as Complete') }}';
            if (typeof toastr !== 'undefined') {
                toastr.error('{{ __('Failed to mark lesson as complete. Please try again.') }}');
            }
        });
    });
});

/**
 * Show animated completion success card
 */
function showCompletionSuccess(lessonId, data) {
    const section = document.getElementById('completion-section-lesson-' + lessonId);
    if (!section) return;

    const courseProgress = data?.course_progress || 0;

    const successHTML = `
        <div class="card card-flush overflow-hidden completion-success-card">
            <div class="card-body py-10 px-10 text-center completion-success-gradient">
                <div class="completion-checkmark-wrapper mb-5">
                    <svg class="completion-checkmark" viewBox="0 0 100 100">
                        <circle class="checkmark-circle" cx="50" cy="50" r="45"
                                fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="4"/>
                        <circle class="checkmark-circle-fill" cx="50" cy="50" r="45"
                                fill="none" stroke="#fff" stroke-width="4"/>
                        <path class="checkmark-check" d="M30 50 L45 65 L70 35"
                              fill="none" stroke="#fff" stroke-width="5"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="text-white fw-bolder fs-1 mb-2">{{ __('Lesson Complete!') }}</h3>
                <p class="text-white text-opacity-85 fs-5 mb-6">
                    {{ __('Great job! You have completed this lesson. Keep up the momentum!') }}
                </p>
                <div class="d-inline-flex align-items-center completion-progress-pill rounded-pill px-5 py-3">
                    <span class="text-white fs-6">{{ __('Course Progress:') }}</span>
                    <span class="text-white fw-bold fs-4 ms-2" data-progress-text="course">${courseProgress}%</span>
                </div>
            </div>
        </div>
    `;

    // Fade out current content, then replace with success
    section.style.transition = 'all 0.3s ease';
    section.style.opacity = '0';
    section.style.transform = 'translateY(10px)';

    setTimeout(() => {
        section.innerHTML = successHTML;
        section.style.opacity = '1';
        section.style.transform = 'translateY(0)';
    }, 300);
}

/**
 * Update all progress indicators on the page without reloading
 */
function updateProgressIndicators(data) {
    if (!data) return;

    // Update course progress bar
    const courseProgressBars = document.querySelectorAll('[data-progress-type="course"]');
    courseProgressBars.forEach(bar => {
        if (data.course_progress !== undefined) {
            bar.style.width = `${data.course_progress}%`;
            bar.setAttribute('aria-valuenow', data.course_progress);
        }
    });

    // Update course progress text
    const courseProgressTexts = document.querySelectorAll('[data-progress-text="course"]');
    courseProgressTexts.forEach(el => {
        if (data.course_progress !== undefined) {
            el.textContent = `${data.course_progress}%`;
        }
    });

    // Update module progress if provided
    if (data.module_progress !== undefined && data.module_id) {
        const moduleProgressBars = document.querySelectorAll(`[data-progress-type="module"][data-module-id="${data.module_id}"]`);
        moduleProgressBars.forEach(bar => {
            bar.style.width = `${data.module_progress}%`;
            bar.setAttribute('aria-valuenow', data.module_progress);
        });

        const moduleProgressTexts = document.querySelectorAll(`[data-progress-text="module"][data-module-id="${data.module_id}"]`);
        moduleProgressTexts.forEach(el => {
            el.textContent = `${data.module_progress}%`;
        });
    }

    // Update completed items count
    const completedCountEls = document.querySelectorAll('[data-completed-count]');
    completedCountEls.forEach(el => {
        if (data.completed_items !== undefined) {
            el.textContent = data.completed_items;
        }
    });
}

/**
 * Update sidebar item to show completion status
 */
function updateSidebarItem(lessonId) {
    // Find the sidebar item for this lesson
    const sidebarItem = document.querySelector(`[data-sidebar-lesson="${lessonId}"]`);
    if (sidebarItem) {
        sidebarItem.classList.add('completed');

        // Update icon if present
        const icon = sidebarItem.querySelector('.lesson-status-icon');
        if (icon) {
            icon.innerHTML = '{!! getIcon('check', 'fs-6 text-success') !!}';
        }

        // Add completed badge if not present
        if (!sidebarItem.querySelector('.badge-light-success')) {
            const badge = document.createElement('span');
            badge.className = 'badge badge-light-success fs-9 ms-2';
            badge.textContent = '{{ __('Completed') }}';
            sidebarItem.querySelector('.lesson-title')?.appendChild(badge);
        }
    }

    // Also update the module content list item
    const contentItem = document.querySelector(`[data-item-id] a[href*="lessons/${lessonId}"]`)?.closest('[data-item-id]');
    if (contentItem) {
        const actionArea = contentItem.querySelector('.content-actions');
        if (actionArea) {
            actionArea.innerHTML = `
                <span class="symbol symbol-25px">
                    <span class="symbol-label bg-light-success">
                        {!! getIcon('check', 'fs-6 text-success') !!}
                    </span>
                </span>
            `;
        }
    }
}
</script>
@endpush
