<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Notifications\AssignmentDueReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendAssignmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:assignment-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications for assignments due within 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for assignments due within 24 hours...');

        // Get assignments due within the next 24 hours
        $assignments = Assignment::where('due_date', '>', now())
            ->where('due_date', '<=', now()->addHours(24))
            ->with('course')
            ->get();

        $count = 0;

        foreach ($assignments as $assignment) {
            // Get students enrolled in the course (via program)
            $students = \App\Models\User::where('program_id', $assignment->course->program_id)
                ->where('user_type', 'student')
                ->get();

            // Filter students who haven't submitted yet
            $studentsWithoutSubmission = $students->filter(function ($student) use ($assignment) {
                return !$assignment->submissions()->where('student_id', $student->id)->exists();
            });

            if ($studentsWithoutSubmission->count() > 0) {
                // Send notification to students
                Notification::send($studentsWithoutSubmission, new AssignmentDueReminderNotification($assignment));
                $count += $studentsWithoutSubmission->count();

                $this->info("Sent {$studentsWithoutSubmission->count()} reminders for: {$assignment->title}");
            }
        }

        $this->info("Total reminders sent: {$count}");

        return Command::SUCCESS;
    }
}
