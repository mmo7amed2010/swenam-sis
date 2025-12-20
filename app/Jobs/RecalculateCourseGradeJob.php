<?php

namespace App\Jobs;

use App\Services\CourseGradeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalculateCourseGradeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $studentId,
        public int $courseId
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(CourseGradeService $service): void
    {
        $service->updateGrade($this->studentId, $this->courseId);
    }
}
