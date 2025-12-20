<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseChangeLog;
use App\Models\CourseModule;
use App\Models\ModuleLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ModuleLessonService
{
    public function __construct(
        private ModuleItemService $moduleItemService
    ) {}

    /**
     * Create a new lesson for a module.
     *
     * @param  CourseModule  $module  Module to create lesson for
     * @param  array  $data  Lesson data
     * @param  Request  $request  Request with file uploads
     * @return ModuleLesson Newly created lesson
     */
    public function createLesson(CourseModule $module, array $data, Request $request): ModuleLesson
    {
        // Auto-calculate order_number
        $maxOrder = $module->lessons()->max('order_number') ?? 0;
        $nextOrder = $maxOrder + 1;

        $lessonData = [
            'module_id' => $module->id,
            'title' => $data['title'],
            'content_type' => $data['content_type'],
            'status' => $data['status'] ?? 'draft',
            'order_number' => $nextOrder,
            'estimated_duration' => $data['estimated_duration'] ?? null,
        ];

        // Handle content based on type
        switch ($data['content_type']) {
            case 'text_html':
                $lessonData['content'] = $data['content'];
                break;
            case 'video':
                $lessonData['content_url'] = $data['content_url'];
                break;
            case 'video_upload':
                // Upload video file
                $uploadResult = $this->handleVideoUpload($module, $request->file('content_file'));
                $lessonData['file_path'] = $uploadResult['file_path'];
                $lessonData['content_url'] = $uploadResult['content_url'];
                break;
            case 'pdf':
                // Upload PDF file
                $uploadResult = $this->handlePdfUpload($module, $request->file('content_file'));
                $lessonData['file_path'] = $uploadResult['file_path'];
                $lessonData['content_url'] = $uploadResult['content_url'];
                break;
            case 'external_link':
                $lessonData['content_url'] = $data['content_url'];
                $lessonData['open_new_tab'] = $request->boolean('open_new_tab');
                break;
        }

        $lesson = ModuleLesson::create($lessonData);

        $this->moduleItemService->attachItem($module, $lesson, [
            'order_position' => $nextOrder - 1,
        ]);

        return $lesson;
    }

    /**
     * Update an existing lesson.
     *
     * @param  Course  $course  Course the lesson belongs to
     * @param  CourseModule  $module  Module the lesson belongs to
     * @param  ModuleLesson  $lesson  Lesson to update
     * @param  array  $data  Update data
     * @param  Request  $request  Request with file uploads
     * @param  bool  $contentTypeChanging  Whether content type is changing
     * @return ModuleLesson Updated lesson
     */
    public function updateLesson(Course $course, CourseModule $module, ModuleLesson $lesson, array $data, Request $request, bool $contentTypeChanging): ModuleLesson
    {
        $oldValues = $lesson->toArray();

        // Cleanup old content if changing from PDF or video_upload
        if ($contentTypeChanging && in_array($lesson->content_type, ['pdf', 'video_upload']) && $lesson->file_path) {
            Storage::disk('public')->delete($lesson->file_path);
        }

        $lessonData = [
            'title' => $data['title'],
            'content_type' => $data['content_type'],
            'status' => $data['status'] ?? $lesson->status,
            'estimated_duration' => $data['estimated_duration'] ?? null,
        ];

        // Handle content based on type
        switch ($data['content_type']) {
            case 'text_html':
                $lessonData['content'] = $data['content'];
                $lessonData['content_url'] = null;
                $lessonData['file_path'] = null;
                break;
            case 'video':
                $lessonData['content_url'] = $data['content_url'];
                $lessonData['content'] = null;
                $lessonData['file_path'] = null;
                break;
            case 'video_upload':
                if ($request->hasFile('content_file')) {
                    // Upload new video file
                    $uploadResult = $this->handleVideoUpload($module, $request->file('content_file'));
                    $lessonData['file_path'] = $uploadResult['file_path'];
                    $lessonData['content_url'] = $uploadResult['content_url'];
                }
                $lessonData['content'] = null;
                break;
            case 'pdf':
                if ($request->hasFile('content_file')) {
                    // Upload new PDF file
                    $uploadResult = $this->handlePdfUpload($module, $request->file('content_file'));
                    $lessonData['file_path'] = $uploadResult['file_path'];
                    $lessonData['content_url'] = $uploadResult['content_url'];
                }
                $lessonData['content'] = null;
                break;
            case 'external_link':
                $lessonData['content_url'] = $data['content_url'];
                $lessonData['open_new_tab'] = $request->boolean('open_new_tab');
                $lessonData['content'] = null;
                $lessonData['file_path'] = null;
                break;
        }

        $lesson->update($lessonData);

        // Ensure module item exists (e.g., legacy lessons migrated later)
        $this->moduleItemService->attachItem($module, $lesson);

        // Log changes
        if (class_exists(CourseChangeLog::class)) {
            CourseChangeLog::create([
                'course_id' => $course->id,
                'user_id' => auth()->id(),
                'field_changed' => 'lesson_updated',
                'old_value' => json_encode($oldValues),
                'new_value' => json_encode($lesson->fresh()->toArray()),
            ]);
        }

        return $lesson->fresh();
    }

    /**
     * Delete a lesson (soft delete).
     *
     * @param  Course  $course  Course the lesson belongs to
     * @param  CourseModule  $module  Module the lesson belongs to
     * @param  ModuleLesson  $lesson  Lesson to delete
     * @return ModuleLesson Deleted lesson
     */
    public function deleteLesson(Course $course, CourseModule $module, ModuleLesson $lesson): ModuleLesson
    {
        $progressCount = $this->countLessonProgress($lesson);

        // Log deletion
        if (class_exists(CourseChangeLog::class)) {
            CourseChangeLog::create([
                'course_id' => $course->id,
                'user_id' => auth()->id(),
                'field_changed' => 'lesson_deleted',
                'old_value' => json_encode($lesson->toArray()),
                'new_value' => "Lesson deleted with {$progressCount} student views",
            ]);
        }

        // Delete file if exists (PDF or video_upload)
        if (in_array($lesson->content_type, ['pdf', 'video_upload']) && $lesson->file_path) {
            Storage::disk('public')->delete($lesson->file_path);
        }

        // Soft delete lesson
        $lesson->delete();

        $this->moduleItemService->detachItem($lesson);

        return $lesson;
    }

    /**
     * Reorder lessons within a module.
     *
     * @param  CourseModule  $module  Module the lessons belong to
     * @param  array  $lessonIds  Array of lesson IDs in new order
     * @return array Lesson IDs in new order
     *
     * @throws \Exception If invalid lesson IDs provided
     */
    public function reorderLessons(CourseModule $module, array $lessonIds): array
    {
        // Verify all lessons belong to this module
        $lessons = ModuleLesson::whereIn('id', $lessonIds)
            ->where('module_id', $module->id)
            ->get();

        if ($lessons->count() !== count($lessonIds)) {
            throw new \Exception('Invalid lesson IDs provided');
        }

        // Update order_number for each lesson
        foreach ($lessonIds as $index => $lessonId) {
            ModuleLesson::where('id', $lessonId)->update(['order_number' => $index + 1]);
        }

        return $lessonIds;
    }

    /**
     * Handle PDF file upload for a lesson.
     *
     * @param  CourseModule  $module  Module the lesson belongs to
     * @param  \Illuminate\Http\UploadedFile  $file  Uploaded PDF file
     * @return array File upload result with file_path and content_url
     */
    public function handlePdfUpload(CourseModule $module, $file): array
    {
        $path = $file->store("lessons/{$module->id}", 'public');

        return [
            'file_path' => $path,
            'content_url' => Storage::url($path),
        ];
    }

    /**
     * Handle video file upload for a lesson.
     *
     * @param  CourseModule  $module  Module the lesson belongs to
     * @param  \Illuminate\Http\UploadedFile  $file  Uploaded video file
     * @return array File upload result with file_path and content_url
     */
    public function handleVideoUpload(CourseModule $module, $file): array
    {
        $path = $file->store("lessons/{$module->id}/videos", 'public');

        return [
            'file_path' => $path,
            'content_url' => Storage::url($path),
        ];
    }

    /**
     * Validate and parse video URL (YouTube or Vimeo).
     *
     * @param  string  $url  Video URL to validate
     * @return array Parsed video information
     *
     * @throws \InvalidArgumentException If URL is invalid
     */
    public function handleVideoUrl(string $url): array
    {
        $youtubePattern = '/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/';
        $vimeoPattern = '/^(https?:\/\/)?(www\.)?vimeo\.com\/(\d+)/';

        if (preg_match($youtubePattern, $url, $matches)) {
            return [
                'type' => 'youtube',
                'video_id' => $matches[4] ?? null,
                'url' => $url,
            ];
        }

        if (preg_match($vimeoPattern, $url, $matches)) {
            return [
                'type' => 'vimeo',
                'video_id' => $matches[3] ?? null,
                'url' => $url,
            ];
        }

        throw new \InvalidArgumentException('The video URL must be a valid YouTube or Vimeo URL.');
    }

    /**
     * Toggle lesson status between draft and published.
     *
     * @param  Course  $course  Course the lesson belongs to
     * @param  CourseModule  $module  Module the lesson belongs to
     * @param  ModuleLesson  $lesson  Lesson to toggle
     * @return ModuleLesson Updated lesson
     */
    public function toggleLessonStatus(Course $course, CourseModule $module, ModuleLesson $lesson): ModuleLesson
    {
        $oldStatus = $lesson->status;
        $newStatus = $lesson->status === 'published' ? 'draft' : 'published';
        $lesson->status = $newStatus;
        $lesson->save();

        // Log status change
        CourseChangeLog::create([
            'course_id' => $course->id,
            'user_id' => auth()->id(),
            'field_changed' => 'lesson_status',
            'old_value' => $oldStatus,
            'new_value' => $newStatus,
        ]);

        return $lesson->fresh();
    }

    /**
     * Count lesson progress records.
     *
     * @param  ModuleLesson  $lesson  Lesson to count progress for
     * @return int Progress count
     */
    private function countLessonProgress(ModuleLesson $lesson): int
    {
        // Count student progress records
        // Note: lesson_progress table may not exist yet, so we'll check if it does
        if (DB::getSchemaBuilder()->hasTable('lesson_progress')) {
            return DB::table('lesson_progress')
                ->where('lesson_id', $lesson->id)
                ->count();
        }

        return 0;
    }
}
