{{--
    Dashboard Hero Component - Metronic Design System Aligned

    A hero banner component for the student dashboard with profile info,
    continue learning button, and quick stats.

    @props
    - user: User model
    - program: ?Program model
    - continueLesson: ?Lesson model - The lesson to continue
    - continueCourse: ?Course or array - The course containing the continue lesson
    - isNewStudent: bool - Whether this is a new student
    - progressRate: int - Overall completion percentage

    @example
    <x-student.dashboard-hero
        :user="$user"
        :program="$program"
        :continueLesson="$continueLesson"
        :continueCourse="$continueCourse"
        :isNewStudent="$isNewStudent"
        :progressRate="$progress_stats['completion_rate']"
    />
--}}

@props([
    'user',
    'program' => null,
    'continueLesson' => null,
    'continueCourse' => null,
    'isNewStudent' => false,
    'progressRate' => 0,
])

@php
    $userInitial = substr($user->first_name ?? $user->name, 0, 1);
    $userName = $user->first_name ?? $user->name;
    $courseId = is_array($continueCourse) ? ($continueCourse['id'] ?? null) : ($continueCourse->id ?? null);
@endphp

<div class="card mb-5 mb-xl-10 border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="position-relative">
            {{-- Solid Background with Opacity (Metronic standard) --}}
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-primary" style="opacity: 0.9;"></div>

            {{-- Content --}}
            <div class="position-relative p-9">
                <div class="row align-items-center">
                    {{-- Left: Profile & Continue Learning --}}
                    <div class="col-lg-7">
                        {{-- Profile Section --}}
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-75px symbol-circle me-5">
                                <span class="symbol-label bg-white bg-opacity-25">
                                    <span class="text-white fs-1 fw-bold">{{ $userInitial }}</span>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h1 class="text-white fw-bold mb-2">
                                    {{ __('Hello, :name!', ['name' => $userName]) }}
                                </h1>
                                <p class="text-white text-opacity-75 fs-5 mb-0">
                                    @if($program)
                                        {{ $program->name }}
                                    @else
                                        {{ __('Ready to continue your learning journey?') }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Continue Learning Button --}}
                        @if($continueLesson && $courseId)
                            <a href="{{ route('student.courses.lessons.show', ['courseId' => $courseId, 'lessonId' => $continueLesson->id]) }}"
                               class="btn btn-white btn-lg px-6 py-3 fw-bold shadow-sm hover-elevate-up"
                               aria-label="{{ $isNewStudent ? __('Start Learning') : __('Continue Learning') }}">
                                {!! getIcon('rocket', 'fs-2 me-2 text-primary') !!}
                                {{ $isNewStudent ? __('Start Learning') : __('Continue Learning') }}
                                <span class="badge badge-light-primary ms-3 fs-8">
                                    {{ Str::limit($continueLesson->title, 25) }}
                                </span>
                            </a>
                        @else
                            <a href="{{ route('student.program.index') }}"
                               class="btn btn-white btn-lg px-6 py-3 fw-bold shadow-sm hover-elevate-up"
                               aria-label="{{ __('View Program') }}">
                                {!! getIcon('book-open', 'fs-2 me-2 text-primary') !!}
                                {{ __('View Program') }}
                            </a>
                        @endif
                    </div>

                    {{-- Right: Date & Progress --}}
                    <div class="col-lg-5 text-lg-end mt-5 mt-lg-0">
                        <div class="text-white">
                            <div class="fs-2x fw-bold">{{ date('l') }}</div>
                            <div class="fs-4 text-opacity-75">{{ date('F j, Y') }}</div>
                        </div>

                        {{-- Quick Stats in Hero --}}
                        @if($progressRate > 0)
                            <div class="d-inline-flex align-items-center mt-4 px-4 py-2 rounded bg-white bg-opacity-15">
                                <span class="text-white fs-6 me-3">{{ __('Overall Progress') }}</span>
                                <span class="fs-2 fw-bolder text-white">{{ $progressRate }}%</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
