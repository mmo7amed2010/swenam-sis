{{--
 * Recent Announcements Widget
 * 
 * Displays recent system and course announcements for the authenticated user
 * Enhanced with better design, animations, and user experience
 * 
 * @param int $limit - Number of announcements to display (default: 5)
--}}

@props([
    'limit' => 5,
])

@php
    $user = auth()->user();
    
    // Get system announcements filtered by target audience
    $systemAnnouncementsQuery = \App\Models\Announcement::systemWide()
        ->published()
        ->where(function($query) use ($user) {
            $query->where('target_audience', 'all')
                  ->orWhere('target_audience', $user->user_type . 's') // 'students', 'instructors', 'admins'
                  ->orWhere(function($q) use ($user) {
                      // Program-specific announcements
                      $q->where('target_audience', 'program');
                      
                      if ($user->user_type === 'student' && $user->program_id) {
                          // Students in a program see all announcements for that program
                          $q->where('program_id', $user->program_id);
                      }
                  });
        });
    
    $systemAnnouncements = $systemAnnouncementsQuery->latest()
        ->limit($limit)
        ->get();
    
    // Get course announcements for students
    $courseAnnouncements = collect();
    if ($user->user_type === 'student' && $user->program_id) {
        $courseIds = $user->programCourses()->pluck('courses.id');
        $courseAnnouncements = \App\Models\Announcement::whereIn('course_id', $courseIds)
            ->published()
            ->with('course')
            ->latest()
            ->limit($limit)
            ->get();
    }
    
    // Merge and sort by date
    $announcements = $systemAnnouncements->merge($courseAnnouncements)
        ->sortByDesc('created_at')
        ->take($limit);
        
    $priorityColors = [
        'high' => 'danger',
        'medium' => 'warning',
        'low' => 'info',
    ];
    
    $priorityIcons = [
        'high' => 'notification-bing',
        'medium' => 'notification',
        'low' => 'information-5',
    ];
@endphp

<!--begin::Announcements Widget-->
<div class="card card-flush h-xl-100 shadow-sm">
    <!--begin::Header-->
    <div class="card-header pt-7 pb-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-800 fs-3">
                {!! getIcon('megaphone', 'fs-3 me-2 text-primary') !!}
                Recent Announcements
            </span>
            <span class="text-gray-400 mt-1 fw-semibold fs-7">Stay updated with the latest news</span>
        </h3>
        <div class="card-toolbar">
            @if($user->user_type === 'admin')
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-light-primary">
                    {!! getIcon('eye', 'fs-6 me-1') !!}
                    View All
                </a>
            @elseif($user->user_type === 'instructor')
                <a href="{{ route('instructor.courses.index') }}" class="btn btn-sm btn-light-primary">
                    {!! getIcon('book', 'fs-6 me-1') !!}
                    My Courses
                </a>
            @endif
        </div>
    </div>
    <!--end::Header-->

    <!--begin::Body-->
    <div class="card-body pt-5">
        @if($announcements->count() > 0)
            <div class="timeline timeline-border-dashed">
                @foreach($announcements as $index => $announcement)
                    @php
                        $priorityColor = $priorityColors[$announcement->priority] ?? 'primary';
                        $priorityIcon = $priorityIcons[$announcement->priority] ?? 'notification';
                        $isNew = $announcement->created_at->isToday();
                    @endphp
                    
                    <!--begin::Timeline item-->
                    <div class="timeline-item">
                        <!--begin::Timeline line-->
                        <div class="timeline-line"></div>
                        <!--end::Timeline line-->

                        <!--begin::Timeline icon-->
                        <div class="timeline-icon">
                            <span class="svg-icon svg-icon-2 svg-icon-{{ $priorityColor }}">
                                {!! getIcon($priorityIcon, 'fs-2 text-' . $priorityColor) !!}
                            </span>
                        </div>
                        <!--end::Timeline icon-->

                        <!--begin::Timeline content-->
                        <div class="timeline-content mb-10 mt-n1">
                            <!--begin::Timeline heading-->
                            <div class="pe-3 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    @php
                                        $showRoute = match($user->user_type) {
                                            'admin' => route('admin.announcements.show', $announcement),
                                            'instructor' => '#', // Instructor announcements are course-specific
                                            default => route('announcements.show', $announcement)
                                        };
                                    @endphp
                                    <a href="{{ $showRoute }}" 
                                       class="text-gray-800 text-hover-primary fw-bold fs-5 me-2">
                                        {{ $announcement->title }}
                                    </a>
                                    @if($isNew)
                                        <span class="badge badge-light-success badge-sm">New</span>
                                    @endif
                                </div>

                                <!--begin::Description-->
                                <div class="text-gray-600 fw-normal fs-6 mb-3">
                                    {{ Str::limit(strip_tags($announcement->content), 120) }}
                                </div>
                                <!--end::Description-->

                                <!--begin::Meta-->
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    @if($announcement->type === 'system')
                                        <span class="badge badge-light-primary">
                                            {!! getIcon('shield-tick', 'fs-7 me-1') !!}
                                            System
                                        </span>
                                    @else
                                        <span class="badge badge-light-info">
                                            {!! getIcon('book', 'fs-7 me-1') !!}
                                            {{ $announcement->course->course_code ?? 'Course' }}
                                        </span>
                                    @endif
                                    
                                    <span class="badge badge-light-{{ $priorityColor }}">
                                        {{ ucfirst($announcement->priority) }} Priority
                                    </span>
                                    
                                    <span class="text-gray-400 fs-7">
                                        {!! getIcon('time', 'fs-7 me-1') !!}
                                        {{ $announcement->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <!--end::Meta-->
                            </div>
                            <!--end::Timeline heading-->
                        </div>
                        <!--end::Timeline content-->
                    </div>
                    <!--end::Timeline item-->
                @endforeach
            </div>

            @if($announcements->count() >= $limit)
                <div class="text-center pt-5 border-top">
                    @if($user->user_type === 'admin')
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-light-primary">
                            {!! getIcon('arrow-right', 'fs-6 ms-1') !!}
                            View All Announcements
                        </a>
                    @elseif($user->user_type === 'instructor')
                        <a href="{{ route('instructor.courses.index') }}" class="btn btn-sm btn-light-primary">
                            {!! getIcon('book', 'fs-6 ms-1') !!}
                            Manage Course Announcements
                        </a>
                    @else
                        <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-light-primary">
                            {!! getIcon('arrow-right', 'fs-6 ms-1') !!}
                            View All Announcements
                        </a>
                    @endif
                </div>
            @endif
        @else
            <!--begin::Empty state-->
            <div class="text-center py-15">
                <div class="mb-5">
                    <div class="symbol symbol-100px mb-5">
                        <span class="symbol-label bg-light-primary">
                            {!! getIcon('notification', 'fs-3x text-primary') !!}
                        </span>
                    </div>
                </div>
                <div class="text-gray-800 fw-bold fs-4 mb-2">No Announcements Yet</div>
                <div class="text-gray-400 fs-6 mb-5">Check back later for important updates and news</div>
                @if($user->user_type === 'admin')
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-light-primary">
                        {!! getIcon('eye', 'fs-6 me-1') !!}
                        Go to Announcements
                    </a>
                @elseif($user->user_type === 'instructor')
                    <a href="{{ route('instructor.courses.index') }}" class="btn btn-sm btn-light-primary">
                        {!! getIcon('book', 'fs-6 me-1') !!}
                        My Courses
                    </a>
                @else
                    <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-light-primary">
                        {!! getIcon('eye', 'fs-6 me-1') !!}
                        Go to Announcements
                    </a>
                @endif
            </div>
            <!--end::Empty state-->
        @endif
    </div>
    <!--end::Body-->
</div>
<!--end::Announcements Widget-->

<style>
    /* Smooth hover effects */
    .timeline-item:hover .timeline-content {
        transform: translateX(5px);
        transition: transform 0.2s ease;
    }
    
    /* Timeline styling */
    .timeline-border-dashed .timeline-line {
        border-left-style: dashed;
    }
</style>
