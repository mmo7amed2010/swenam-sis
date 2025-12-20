<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\ModuleItem;
use App\Models\ModuleLesson;
use App\Models\Program;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PerformanceTestSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Configuration for data volumes.
     */
    protected array $config = [
        'admins' => 10,
        'instructors' => 50,
        'students' => 10000,
        'programs' => 20,
        'courses_per_program' => 10,
        'modules_per_course' => 5,
        'lessons_per_module' => 5,
        'quizzes_per_course' => 5,
        'questions_per_quiz' => 10,
        'assignments_per_course' => 5,
        'students_per_program' => 500,
        'login_logs' => 100000,
        'course_audit_logs' => 10000,
        'announcements_per_course' => 3,
    ];

    /**
     * Batch size for chunked inserts.
     */
    protected int $batchSize = 500;

    /**
     * Cached password hash.
     */
    protected string $passwordHash;

    /**
     * Stored IDs for relationships.
     */
    protected array $adminIds = [];

    protected array $instructorIds = [];

    protected array $studentIds = [];

    protected array $studentRecordIds = [];

    protected array $programIds = [];

    protected array $courseIds = [];

    protected array $moduleIds = [];

    protected array $lessonIds = [];

    protected array $quizIds = [];

    protected array $assignmentIds = [];

    protected array $moduleItemIds = [];

    protected array $submissionIds = [];

    /**
     * Mapping of program_id => student user_ids for proper data association.
     */
    protected array $programStudentIds = [];

    /**
     * Mapping of course_id => program_id for proper data association.
     */
    protected array $courseProgramIds = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Increase memory limit for large data seeding
        ini_set('memory_limit', '1G');

        $this->passwordHash = Hash::make('password');

        $this->command->info('');
        $this->command->info('==========================================');
        $this->command->info('  Performance Test Data Seeder');
        $this->command->info('==========================================');
        $this->command->info('');

        $startTime = microtime(true);

        // Phase 1: Users and Programs
        $this->seedAdmins();
        $this->seedInstructors();
        $this->seedPrograms();
        $this->seedStudents();
        $this->seedStudentRecords();
        gc_collect_cycles();

        // Phase 2: Courses and Structure
        $this->seedCourses();
        $this->seedCourseInstructors();
        $this->seedStudentPrograms();
        $this->seedCourseModules();
        gc_collect_cycles();

        // Phase 3: Content
        $this->seedModuleLessons();
        $this->seedQuizzes();
        $this->seedQuizQuestions();
        $this->seedAssignments();
        $this->seedModuleItems();
        gc_collect_cycles();

        // Phase 4: Student Activity
        $this->seedSubmissions();
        $this->seedGrades();
        gc_collect_cycles();

        $this->seedQuizAttempts();
        gc_collect_cycles();

        $this->seedModuleProgress();
        $this->seedModuleItemProgress();
        $this->seedCourseGrades();
        gc_collect_cycles();

        // Phase 5: Logs and Announcements
        $this->seedAnnouncements();
        $this->seedLoginLogs();
        $this->seedCourseAuditLogs();

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        $this->command->info('');
        $this->command->info('==========================================');
        $this->command->info("  Seeding completed in {$duration} seconds");
        $this->command->info('==========================================');
        $this->command->info('');
    }

    /**
     * Seed admin users.
     */
    protected function seedAdmins(): void
    {
        $this->command->info('Seeding admin users...');

        $admins = [];
        $now = now();

        for ($i = 0; $i < $this->config['admins']; $i++) {

            $admins[] = [
                'name' => fake()->name(),
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => "admin{$i}@example.com",
                'email_verified_at' => $now,
                'password' => $this->passwordHash,
                'user_type' => 'admin',
                'remember_token' => Str::random(10),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('users')->insert($admins);
        $this->adminIds = User::where('user_type', 'admin')->pluck('id')->toArray();

        $this->command->info("  Created {$this->config['admins']} admin users");
    }

    /**
     * Seed instructor users.
     */
    protected function seedInstructors(): void
    {
        $this->command->info('Seeding instructor users...');

        $instructors = [];
        $now = now();

        for ($i = 0; $i < $this->config['instructors']; $i++) {

            $instructors[] = [
                'name' => fake()->name(),
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => "instructor{$i}@example.com",
                'email_verified_at' => $now,
                'password' => $this->passwordHash,
                'user_type' => 'instructor',
                'remember_token' => Str::random(10),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('users')->insert($instructors);
        $this->instructorIds = User::where('user_type', 'instructor')->pluck('id')->toArray();

        $this->command->info("  Created {$this->config['instructors']} instructor users");
    }

    /**
     * Seed programs.
     */
    protected function seedPrograms(): void
    {
        $this->command->info('Seeding programs...');

        $programs = [];
        $now = now();
        $programNames = [
            'Computer Science', 'Data Science', 'Cybersecurity', 'Software Engineering',
            'Information Technology', 'Artificial Intelligence', 'Machine Learning',
            'Web Development', 'Mobile Development', 'Cloud Computing',
            'Business Administration', 'Project Management', 'Digital Marketing',
            'Finance', 'Accounting', 'Human Resources', 'Operations Management',
            'Healthcare Administration', 'Nursing', 'Public Health',
        ];

        foreach ($programNames as $index => $name) {
            if ($index >= $this->config['programs']) {
                break;
            }

            $programs[] = [
                'name' => $name,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('programs')->insert($programs);
        $this->programIds = Program::pluck('id')->toArray();

        $this->command->info('  Created '.count($this->programIds).' programs');
    }

    /**
     * Seed student users.
     */
    protected function seedStudents(): void
    {
        $this->command->info('Seeding student users...');

        $bar = $this->command->getOutput()->createProgressBar($this->config['students']);
        $bar->start();

        $now = now();
        $students = [];

        for ($i = 0; $i < $this->config['students']; $i++) {

            $programId = $this->programIds[array_rand($this->programIds)];

            $students[] = [
                'name' => fake()->name(),
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => "student{$i}@example.com",
                'email_verified_at' => $now,
                'password' => $this->passwordHash,
                'user_type' => 'student',
                'program_id' => $programId,
                'remember_token' => Str::random(10),
                'last_login_at' => fake()->optional(0.7)->dateTimeBetween('-30 days', 'now'),
                'last_login_ip' => fake()->optional(0.7)->ipv4(),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($students) >= $this->batchSize) {
                DB::table('users')->insert($students);
                $bar->advance(count($students));
                $students = [];
            }
        }

        if (! empty($students)) {
            DB::table('users')->insert($students);
            $bar->advance(count($students));
        }

        $bar->finish();
        $this->command->info('');

        $this->studentIds = User::where('user_type', 'student')->pluck('id')->toArray();

        // Build program-to-students mapping for proper data association
        $studentsByProgram = User::where('user_type', 'student')
            ->select('id', 'program_id')
            ->get()
            ->groupBy('program_id');

        foreach ($studentsByProgram as $programId => $students) {
            $this->programStudentIds[$programId] = $students->pluck('id')->toArray();
        }

        $this->command->info("  Created {$this->config['students']} student users");
    }

    /**
     * Seed student records (extended student info in students table).
     */
    protected function seedStudentRecords(): void
    {
        $this->command->info('Seeding student records...');

        $bar = $this->command->getOutput()->createProgressBar(count($this->studentIds));
        $bar->start();

        $students = [];
        $now = now();
        $statuses = ['active', 'active', 'active', 'suspended', 'withdrawn', 'graduated'];

        foreach ($this->studentIds as $index => $userId) {
            $students[] = [
                'user_id' => $userId,
                'student_number' => 'STU-'.date('Y').'-'.str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => "student{$index}@example.com",
                'phone' => fake()->optional(0.7)->phoneNumber(),
                'date_of_birth' => fake()->dateTimeBetween('-35 years', '-18 years'),
                'address' => json_encode([
                    'street' => fake()->streetAddress(),
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'postal_code' => fake()->postcode(),
                    'country' => fake()->country(),
                ]),
                'enrollment_status' => $statuses[array_rand($statuses)],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($students) >= $this->batchSize) {
                DB::table('students')->insert($students);
                $bar->advance(count($students));
                $students = [];
            }
        }

        if (! empty($students)) {
            DB::table('students')->insert($students);
            $bar->advance(count($students));
        }

        $bar->finish();
        $this->command->info('');

        $this->studentRecordIds = DB::table('students')->pluck('id')->toArray();
        $this->command->info('  Created '.count($this->studentRecordIds).' student records');
    }

    /**
     * Seed courses.
     */
    protected function seedCourses(): void
    {
        $totalCourses = $this->config['programs'] * $this->config['courses_per_program'];
        $this->command->info("Seeding courses ({$totalCourses} total)...");

        $courses = [];
        $now = now();
        $courseIndex = 0;

        $departments = ['Computer Science', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Business', 'Arts'];
        $statuses = ['draft', 'published', 'active', 'active', 'active'];

        foreach ($this->programIds as $programId) {
            for ($i = 0; $i < $this->config['courses_per_program']; $i++) {
                $status = $statuses[array_rand($statuses)];
                $adminId = $this->adminIds[array_rand($this->adminIds)];

                $courses[] = [
                    'course_code' => strtoupper(fake()->bothify('??###')),
                    'name' => fake()->sentence(3),
                    'description' => fake()->paragraph(),
                    'version' => 1,
                    'credits' => fake()->randomFloat(1, 1, 5),
                    'department' => $departments[array_rand($departments)],
                    'program_id' => $programId,
                    'status' => $status,
                    'published_at' => in_array($status, ['published', 'active']) ? $now : null,
                    'created_by_admin_id' => $adminId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $courseIndex++;
            }
        }

        // Insert in batches
        foreach (array_chunk($courses, $this->batchSize) as $batch) {
            DB::table('courses')->insert($batch);
        }

        $this->courseIds = Course::pluck('id')->toArray();

        // Build course-to-program mapping for proper data association
        $coursePrograms = Course::select('id', 'program_id')->get();
        foreach ($coursePrograms as $course) {
            $this->courseProgramIds[$course->id] = $course->program_id;
        }

        $this->command->info('  Created '.count($this->courseIds).' courses');
    }

    /**
     * Seed course instructors.
     */
    protected function seedCourseInstructors(): void
    {
        $this->command->info('Seeding course instructors...');

        $courseInstructors = [];
        $now = now();

        foreach ($this->courseIds as $courseId) {
            $instructorId = $this->instructorIds[array_rand($this->instructorIds)];
            $adminId = $this->adminIds[array_rand($this->adminIds)];

            $courseInstructors[] = [
                'course_id' => $courseId,
                'user_id' => $instructorId,
                'assigned_by_admin_id' => $adminId,
                'assigned_at' => $now,
                'removed_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($courseInstructors, $this->batchSize) as $batch) {
            DB::table('course_instructors')->insert($batch);
        }

        $this->command->info('  Created '.count($courseInstructors).' course instructor assignments');
    }

    /**
     * Seed student program enrollments.
     */
    protected function seedStudentPrograms(): void
    {
        $this->command->info('Seeding student program enrollments...');

        $enrollments = [];
        $now = now();
        $statuses = ['enrolled', 'enrolled', 'enrolled', 'completed', 'withdrawn'];

        // Distribute students across programs (using studentRecordIds from students table)
        $studentsPerProgram = ceil(count($this->studentRecordIds) / count($this->programIds));

        $studentIndex = 0;
        foreach ($this->programIds as $programId) {
            $count = 0;
            while ($count < $studentsPerProgram && $studentIndex < count($this->studentRecordIds)) {
                $enrollmentDate = fake()->dateTimeBetween('-2 years', 'now');
                $expectedGraduation = (clone $enrollmentDate)->modify('+2 years');

                $enrollments[] = [
                    'student_id' => $this->studentRecordIds[$studentIndex],
                    'program_id' => $programId,
                    'enrollment_date' => $enrollmentDate,
                    'expected_graduation' => $expectedGraduation,
                    'status' => $statuses[array_rand($statuses)],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $studentIndex++;
                $count++;

                if (count($enrollments) >= $this->batchSize) {
                    DB::table('student_programs')->insert($enrollments);
                    $enrollments = [];
                }
            }
        }

        if (! empty($enrollments)) {
            DB::table('student_programs')->insert($enrollments);
        }

        $this->command->info('  Created student program enrollments');
    }

    /**
     * Seed course modules.
     */
    protected function seedCourseModules(): void
    {
        $totalModules = count($this->courseIds) * $this->config['modules_per_course'];
        $this->command->info("Seeding course modules ({$totalModules} total)...");

        $modules = [];
        $now = now();

        foreach ($this->courseIds as $courseId) {
            for ($i = 1; $i <= $this->config['modules_per_course']; $i++) {
                $modules[] = [
                    'course_id' => $courseId,
                    'title' => "Module {$i}: ".fake()->sentence(3),
                    'description' => fake()->paragraph(),
                    'order_index' => $i,
                    'status' => fake()->randomElement(['published', 'published', 'published', 'draft']),
                    'release_date' => fake()->optional(0.3)->dateTimeBetween('-7 days', '+30 days'),
                    'estimated_hours' => fake()->randomFloat(1, 2, 20),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($modules) >= $this->batchSize) {
                    DB::table('course_modules')->insert($modules);
                    $modules = [];
                }
            }
        }

        if (! empty($modules)) {
            DB::table('course_modules')->insert($modules);
        }

        $this->moduleIds = CourseModule::pluck('id')->toArray();
        $this->command->info('  Created '.count($this->moduleIds).' course modules');
    }

    /**
     * Seed module lessons.
     */
    protected function seedModuleLessons(): void
    {
        $totalLessons = count($this->moduleIds) * $this->config['lessons_per_module'];
        $this->command->info("Seeding module lessons ({$totalLessons} total)...");

        $bar = $this->command->getOutput()->createProgressBar(count($this->moduleIds));
        $bar->start();

        $lessons = [];
        $now = now();
        $contentTypes = ['video', 'text_html', 'pdf', 'external_link', 'video_upload'];

        foreach ($this->moduleIds as $moduleId) {
            for ($i = 1; $i <= $this->config['lessons_per_module']; $i++) {
                $contentType = $contentTypes[array_rand($contentTypes)];

                $lessons[] = [
                    'module_id' => $moduleId,
                    'title' => "Lesson {$i}: ".fake()->sentence(4),
                    'content_type' => $contentType,
                    'content' => $contentType === 'text_html' ? fake()->paragraphs(5, true) : null,
                    'content_url' => $contentType === 'video' ? 'https://www.youtube.com/watch?v='.Str::random(11) : ($contentType === 'external_link' ? fake()->url() : null),
                    'file_path' => $contentType === 'pdf' ? 'lessons/'.Str::uuid().'.pdf' : null,
                    'order_number' => $i,
                    'status' => fake()->randomElement(['published', 'published', 'published', 'draft']),
                    'estimated_duration' => fake()->numberBetween(5, 60),
                    'open_new_tab' => $contentType === 'external_link',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($lessons) >= $this->batchSize) {
                    DB::table('module_lessons')->insert($lessons);
                    $lessons = [];
                }
            }
            $bar->advance();
        }

        if (! empty($lessons)) {
            DB::table('module_lessons')->insert($lessons);
        }

        $bar->finish();
        $this->command->info('');

        $this->lessonIds = ModuleLesson::pluck('id')->toArray();
        $this->command->info('  Created '.count($this->lessonIds).' module lessons');
    }

    /**
     * Seed quizzes.
     */
    protected function seedQuizzes(): void
    {
        $totalQuizzes = count($this->courseIds) * $this->config['quizzes_per_course'];
        $this->command->info("Seeding quizzes ({$totalQuizzes} total)...");

        $quizzes = [];
        $now = now();

        // Get course to module mapping
        $courseModules = CourseModule::select('id', 'course_id')->get()->groupBy('course_id');

        foreach ($this->courseIds as $courseId) {
            $modules = $courseModules->get($courseId, collect());
            $instructorId = $this->instructorIds[array_rand($this->instructorIds)];

            for ($i = 0; $i < $this->config['quizzes_per_course']; $i++) {
                $moduleId = $modules->isNotEmpty() ? $modules->random()->id : null;
                $isExam = $i === 0; // First quiz is an exam

                $quizzes[] = [
                    'course_id' => $courseId,
                    'module_id' => $moduleId,
                    'created_by' => $instructorId,
                    'title' => $isExam ? 'Final Exam' : 'Quiz '.($i + 1).': '.fake()->words(3, true),
                    'description' => fake()->paragraph(),
                    'total_points' => $isExam ? 100 : fake()->randomElement([10, 20, 25, 50]),
                    'due_date' => fake()->optional(0.7)->dateTimeBetween('now', '+60 days'),
                    'time_limit' => fake()->randomElement([15, 30, 45, 60, 90, 120, null]),
                    'max_attempts' => $isExam ? 1 : fake()->randomElement([1, 2, 3, -1]),
                    'shuffle_questions' => fake()->boolean(70),
                    'shuffle_answers' => fake()->boolean(50),
                    'show_correct_answers' => fake()->randomElement(['never', 'after_all_attempts']),
                    'passing_score' => fake()->randomElement([50, 60, 70, 80]),
                    'published' => fake()->boolean(80),
                    'assessment_type' => $isExam ? 'exam' : 'quiz',
                    'scope' => 'module',
                    'is_retake_exam' => false,
                    'primary_exam_id' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($quizzes) >= $this->batchSize) {
                    DB::table('quizzes')->insert($quizzes);
                    $quizzes = [];
                }
            }
        }

        if (! empty($quizzes)) {
            DB::table('quizzes')->insert($quizzes);
        }

        $this->quizIds = Quiz::pluck('id')->toArray();
        $this->command->info('  Created '.count($this->quizIds).' quizzes');
    }

    /**
     * Seed quiz questions.
     */
    protected function seedQuizQuestions(): void
    {
        $totalQuestions = count($this->quizIds) * $this->config['questions_per_quiz'];
        $this->command->info("Seeding quiz questions ({$totalQuestions} total)...");

        $bar = $this->command->getOutput()->createProgressBar(count($this->quizIds));
        $bar->start();

        $questions = [];
        $now = now();

        foreach ($this->quizIds as $quizId) {
            for ($i = 1; $i <= $this->config['questions_per_quiz']; $i++) {
                $questionType = fake()->randomElement(['mcq', 'mcq', 'mcq', 'true_false']);

                $questions[] = [
                    'quiz_id' => $quizId,
                    'question_type' => $questionType,
                    'question_text' => fake()->sentence().'?',
                    'points' => fake()->randomElement([1, 2, 5, 10]),
                    'order_number' => $i,
                    'answers_json' => json_encode($this->generateAnswers($questionType)),
                    'settings_json' => json_encode(['randomize_answers' => fake()->boolean(50)]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($questions) >= $this->batchSize) {
                    DB::table('quiz_questions')->insert($questions);
                    $questions = [];
                }
            }
            $bar->advance();
        }

        if (! empty($questions)) {
            DB::table('quiz_questions')->insert($questions);
        }

        $bar->finish();
        $this->command->info('');
        $this->command->info('  Created quiz questions');
    }

    /**
     * Generate answers for quiz questions.
     */
    protected function generateAnswers(string $type): array
    {
        if ($type === 'true_false') {
            $correctAnswer = fake()->boolean();

            return [
                ['text' => 'True', 'is_correct' => $correctAnswer],
                ['text' => 'False', 'is_correct' => ! $correctAnswer],
            ];
        }

        $correctIndex = fake()->numberBetween(0, 3);
        $answers = [];

        for ($i = 0; $i < 4; $i++) {
            $answers[] = [
                'text' => fake()->sentence(fake()->numberBetween(2, 8)),
                'is_correct' => $i === $correctIndex,
            ];
        }

        return $answers;
    }

    /**
     * Seed assignments.
     */
    protected function seedAssignments(): void
    {
        $totalAssignments = count($this->courseIds) * $this->config['assignments_per_course'];
        $this->command->info("Seeding assignments ({$totalAssignments} total)...");

        $assignments = [];
        $now = now();
        // Valid enum values for assignment_type: file_upload, text_submission, quiz, external_link
        $assignmentTypes = ['file_upload', 'text_submission', 'file_upload', 'file_upload'];
        // Valid enum values for submission_type: file_upload, text_entry, url_submission, multiple
        $submissionTypes = ['file_upload', 'text_entry', 'file_upload', 'file_upload'];

        foreach ($this->courseIds as $courseId) {
            $instructorId = $this->instructorIds[array_rand($this->instructorIds)];

            for ($i = 0; $i < $this->config['assignments_per_course']; $i++) {
                $assignmentType = $assignmentTypes[array_rand($assignmentTypes)];
                $submissionType = $submissionTypes[array_rand($submissionTypes)];
                $maxPoints = fake()->randomElement([10, 20, 25, 50, 100]);
                $isFileType = $submissionType === 'file_upload';

                $assignments[] = [
                    'course_id' => $courseId,
                    'assignmentable_type' => null,
                    'assignmentable_id' => null,
                    'title' => 'Assignment '.($i + 1).': '.fake()->words(3, true),
                    'description' => fake()->paragraphs(2, true),
                    'instructions' => fake()->paragraphs(3, true),
                    'assignment_type' => $assignmentType,
                    'submission_type' => $submissionType,
                    'max_file_size_mb' => $isFileType ? fake()->randomElement([5, 10, 25, 50]) : null,
                    'max_points' => $maxPoints,
                    'total_points' => $maxPoints,
                    'weight' => fake()->randomFloat(2, 0.05, 0.30),
                    'passing_score' => fake()->randomElement([50, 60, 70, 80]),
                    'rubric' => json_encode([]),
                    'is_published' => fake()->boolean(85),
                    'attempts_allowed' => fake()->randomElement([1, 2, 3]),
                    'allow_resubmission' => fake()->boolean(60),
                    'created_by_user_id' => $instructorId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($assignments) >= $this->batchSize) {
                    DB::table('assignments')->insert($assignments);
                    $assignments = [];
                }
            }
        }

        if (! empty($assignments)) {
            DB::table('assignments')->insert($assignments);
        }

        $this->assignmentIds = Assignment::pluck('id')->toArray();
        $this->command->info('  Created '.count($this->assignmentIds).' assignments');
    }

    /**
     * Seed module items.
     */
    protected function seedModuleItems(): void
    {
        $this->command->info('Seeding module items...');

        $moduleItems = [];
        $now = now();

        // Get lessons grouped by module
        $lessonsByModule = ModuleLesson::select('id', 'module_id')->get()->groupBy('module_id');

        // Get quizzes grouped by module (only those with module_id)
        $quizzesByModule = Quiz::whereNotNull('module_id')
            ->select('id', 'module_id')
            ->get()
            ->groupBy('module_id');

        // Get assignments grouped by course, then we'll distribute to modules
        $assignmentsByCourse = Assignment::select('id', 'course_id')->get()->groupBy('course_id');

        // Get module to course mapping
        $moduleCourses = CourseModule::select('id', 'course_id')->get()->keyBy('id');

        // Track used assignments and quizzes globally to avoid duplicates (unique constraint)
        $usedAssignments = [];
        $usedQuizzes = [];

        foreach ($this->moduleIds as $moduleId) {
            $position = 1;
            $courseId = $moduleCourses->get($moduleId)?->course_id;

            // Add lessons as module items
            $lessons = $lessonsByModule->get($moduleId, collect());
            foreach ($lessons as $lesson) {
                $moduleItems[] = [
                    'module_id' => $moduleId,
                    'itemable_type' => ModuleLesson::class,
                    'itemable_id' => $lesson->id,
                    'order_position' => $position++,
                    'is_required' => fake()->boolean(80),
                    'release_date' => fake()->optional(0.3)->dateTimeBetween('-7 days', '+30 days'),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Add quizzes as module items (only unused ones)
            $quizzes = $quizzesByModule->get($moduleId, collect());
            foreach ($quizzes as $quiz) {
                if (isset($usedQuizzes[$quiz->id])) {
                    continue;
                }
                $usedQuizzes[$quiz->id] = true;

                $moduleItems[] = [
                    'module_id' => $moduleId,
                    'itemable_type' => Quiz::class,
                    'itemable_id' => $quiz->id,
                    'order_position' => $position++,
                    'is_required' => fake()->boolean(90),
                    'release_date' => fake()->optional(0.3)->dateTimeBetween('-7 days', '+30 days'),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Add one unused assignment per module from the course's assignments
            if ($courseId) {
                $courseAssignments = $assignmentsByCourse->get($courseId, collect());
                // Find first unused assignment for this course
                $assignment = $courseAssignments->first(fn ($a) => ! isset($usedAssignments[$a->id]));

                if ($assignment) {
                    $usedAssignments[$assignment->id] = true;
                    $moduleItems[] = [
                        'module_id' => $moduleId,
                        'itemable_type' => Assignment::class,
                        'itemable_id' => $assignment->id,
                        'order_position' => $position++,
                        'is_required' => fake()->boolean(90),
                        'release_date' => fake()->optional(0.3)->dateTimeBetween('-7 days', '+30 days'),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (count($moduleItems) >= $this->batchSize) {
                DB::table('module_items')->insert($moduleItems);
                $moduleItems = [];
            }
        }

        if (! empty($moduleItems)) {
            DB::table('module_items')->insert($moduleItems);
        }

        $this->moduleItemIds = ModuleItem::pluck('id')->toArray();
        $this->command->info('  Created '.count($this->moduleItemIds).' module items (lessons, quizzes, assignments)');
    }

    /**
     * Seed submissions.
     */
    protected function seedSubmissions(): void
    {
        $submissionsPerAssignment = 50; // ~50 students per assignment
        $this->command->info('Seeding submissions...');

        $bar = $this->command->getOutput()->createProgressBar(count($this->assignmentIds));
        $bar->start();

        $submissions = [];
        $now = now();
        $statuses = ['submitted', 'graded', 'graded', 'graded'];

        // Get assignment to course mapping
        $assignmentCourses = Assignment::select('id', 'course_id')->get()->keyBy('id');

        foreach ($this->assignmentIds as $assignmentId) {
            // Get the course and program for this assignment
            $courseId = $assignmentCourses->get($assignmentId)?->course_id;
            $programId = $courseId ? ($this->courseProgramIds[$courseId] ?? null) : null;

            // Only use students from the course's program
            $programStudents = $programId && isset($this->programStudentIds[$programId])
                ? $this->programStudentIds[$programId]
                : $this->studentIds;

            if (empty($programStudents)) {
                $bar->advance();

                continue;
            }

            // Random sample of students from the program for this assignment
            $sampleSize = min($submissionsPerAssignment, count($programStudents));
            $selectedStudents = array_rand(array_flip($programStudents), $sampleSize);
            if (! is_array($selectedStudents)) {
                $selectedStudents = [$selectedStudents];
            }

            foreach ($selectedStudents as $studentId) {
                $isLate = fake()->boolean(15);
                $submittedAt = fake()->dateTimeBetween('-60 days', 'now');

                $submissions[] = [
                    'assignment_id' => $assignmentId,
                    'user_id' => $studentId,
                    'submission_type' => fake()->randomElement(['file', 'text']),
                    'text_content' => fake()->optional(0.4)->paragraphs(3, true),
                    'file_path' => fake()->optional(0.6)->filePath(),
                    'file_name' => fake()->optional(0.6)->word().'.pdf',
                    'file_size' => fake()->optional(0.6)->numberBetween(10000, 5000000),
                    'attempt_number' => 1,
                    'submitted_at' => $submittedAt,
                    'is_late' => $isLate,
                    'late_days' => $isLate ? fake()->numberBetween(1, 7) : 0,
                    'status' => $statuses[array_rand($statuses)],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($submissions) >= $this->batchSize) {
                    DB::table('submissions')->insert($submissions);
                    $submissions = [];
                }
            }
            $bar->advance();
        }

        if (! empty($submissions)) {
            DB::table('submissions')->insert($submissions);
        }

        $bar->finish();
        $this->command->info('');

        $this->submissionIds = Submission::pluck('id')->toArray();
        $this->command->info('  Created '.count($this->submissionIds).' submissions');
    }

    /**
     * Seed grades.
     */
    protected function seedGrades(): void
    {
        $this->command->info('Seeding grades...');

        // Get graded submissions
        $gradedSubmissions = Submission::where('status', 'graded')
            ->select('id', 'assignment_id')
            ->get();

        $bar = $this->command->getOutput()->createProgressBar($gradedSubmissions->count());
        $bar->start();

        $grades = [];
        $now = now();

        foreach ($gradedSubmissions as $submission) {
            $maxPoints = fake()->randomElement([10, 20, 25, 50, 100]);
            $pointsAwarded = fake()->randomFloat(1, $maxPoints * 0.4, $maxPoints);
            $isPublished = fake()->boolean(80);
            $instructorId = $this->instructorIds[array_rand($this->instructorIds)];

            $grades[] = [
                'submission_id' => $submission->id,
                'points_awarded' => $pointsAwarded,
                'max_points' => $maxPoints,
                'feedback' => fake()->optional(0.7)->paragraphs(2, true),
                'rubric_scores' => json_encode([]),
                'graded_by_user_id' => $instructorId,
                'graded_at' => fake()->dateTimeBetween('-30 days', 'now'),
                'is_published' => $isPublished,
                'published_at' => $isPublished ? fake()->dateTimeBetween('-30 days', 'now') : null,
                'version' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($grades) >= $this->batchSize) {
                DB::table('grades')->insert($grades);
                $bar->advance(count($grades));
                $grades = [];
            }
        }

        if (! empty($grades)) {
            DB::table('grades')->insert($grades);
            $bar->advance(count($grades));
        }

        $bar->finish();
        $this->command->info('');
        $this->command->info('  Created grades for graded submissions');
    }

    /**
     * Seed quiz attempts.
     */
    protected function seedQuizAttempts(): void
    {
        $attemptsPerQuiz = 50; // ~50 students attempt each quiz
        $this->command->info('Seeding quiz attempts...');

        $bar = $this->command->getOutput()->createProgressBar(count($this->quizIds));
        $bar->start();

        $attempts = [];
        $now = now();
        $statuses = ['submitted', 'graded', 'graded', 'graded'];

        // Get quiz to course mapping
        $quizCourses = Quiz::select('id', 'course_id')->get()->keyBy('id');

        foreach ($this->quizIds as $quizId) {
            // Get the course and program for this quiz
            $courseId = $quizCourses->get($quizId)?->course_id;
            $programId = $courseId ? ($this->courseProgramIds[$courseId] ?? null) : null;

            // Only use students from the course's program
            $programStudents = $programId && isset($this->programStudentIds[$programId])
                ? $this->programStudentIds[$programId]
                : $this->studentIds;

            if (empty($programStudents)) {
                $bar->advance();

                continue;
            }

            // Random sample of students from the program for this quiz
            $sampleSize = min($attemptsPerQuiz, count($programStudents));
            $selectedStudents = array_rand(array_flip($programStudents), $sampleSize);
            if (! is_array($selectedStudents)) {
                $selectedStudents = [$selectedStudents];
            }

            foreach ($selectedStudents as $studentId) {
                $startTime = fake()->dateTimeBetween('-60 days', 'now');
                $endTime = (clone $startTime)->modify('+'.fake()->numberBetween(10, 90).' minutes');
                $status = $statuses[array_rand($statuses)];
                $score = $status !== 'in_progress' ? fake()->numberBetween(0, 100) : null;

                $attempts[] = [
                    'quiz_id' => $quizId,
                    'student_id' => $studentId,
                    'attempt_number' => 1,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => $status,
                    'score' => $score,
                    'percentage' => $score,
                    'answers_json' => json_encode([]),
                    'questions_order' => json_encode([]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($attempts) >= $this->batchSize) {
                    DB::table('quiz_attempts')->insert($attempts);
                    $attempts = [];
                }
            }
            $bar->advance();
        }

        if (! empty($attempts)) {
            DB::table('quiz_attempts')->insert($attempts);
        }

        $bar->finish();
        $this->command->info('');
        $this->command->info('  Created quiz attempts');
    }

    /**
     * Seed module progress.
     */
    protected function seedModuleProgress(): void
    {
        $progressPerModule = 100; // ~100 students per module
        $this->command->info('Seeding module progress...');

        $bar = $this->command->getOutput()->createProgressBar(count($this->moduleIds));
        $bar->start();

        $progress = [];
        $now = now();
        $statuses = ['not_started', 'in_progress', 'completed', 'completed', 'completed'];

        // Get module to course mapping
        $moduleCourses = CourseModule::select('id', 'course_id')->get()->keyBy('id');

        foreach ($this->moduleIds as $moduleId) {
            // Get the course and program for this module
            $courseId = $moduleCourses->get($moduleId)?->course_id;
            $programId = $courseId ? ($this->courseProgramIds[$courseId] ?? null) : null;

            // Only use students from the course's program
            $programStudents = $programId && isset($this->programStudentIds[$programId])
                ? $this->programStudentIds[$programId]
                : $this->studentIds;

            if (empty($programStudents)) {
                $bar->advance();

                continue;
            }

            // Random sample of students from the program for this module
            $sampleSize = min($progressPerModule, count($programStudents));
            $selectedStudents = array_rand(array_flip($programStudents), $sampleSize);
            if (! is_array($selectedStudents)) {
                $selectedStudents = [$selectedStudents];
            }

            foreach ($selectedStudents as $studentId) {
                $status = $statuses[array_rand($statuses)];

                $progress[] = [
                    'module_id' => $moduleId,
                    'student_id' => $studentId,
                    'status' => $status,
                    'exam_passed_at' => $status === 'completed' ? fake()->dateTimeBetween('-60 days', 'now') : null,
                    'exam_attempts_used' => $status === 'completed' ? fake()->numberBetween(1, 2) : 0,
                    'exam_first_score' => $status === 'completed' ? fake()->numberBetween(50, 100) : null,
                    'exam_best_score' => $status === 'completed' ? fake()->numberBetween(70, 100) : null,
                    'primary_exam_failed' => false,
                    'retake_exam_failed' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($progress) >= $this->batchSize) {
                    DB::table('module_progress')->insert($progress);
                    $progress = [];
                }
            }
            $bar->advance();
        }

        if (! empty($progress)) {
            DB::table('module_progress')->insert($progress);
        }

        $bar->finish();
        $this->command->info('');
        $this->command->info('  Created module progress records');
    }

    /**
     * Seed module item progress.
     */
    protected function seedModuleItemProgress(): void
    {
        $progressPerItem = 50;
        $this->command->info('Seeding module item progress...');

        $bar = $this->command->getOutput()->createProgressBar(count($this->moduleItemIds));
        $bar->start();

        $progress = [];
        $now = now();

        // Get module item to course mapping
        $moduleItemCourses = DB::table('module_items')
            ->join('course_modules', 'module_items.module_id', '=', 'course_modules.id')
            ->select('module_items.id as item_id', 'course_modules.course_id')
            ->get()
            ->keyBy('item_id');

        foreach ($this->moduleItemIds as $itemId) {
            $courseId = $moduleItemCourses->get($itemId)?->course_id ?? $this->courseIds[0];
            $programId = $this->courseProgramIds[$courseId] ?? null;

            // Only use students from the course's program
            $programStudents = $programId && isset($this->programStudentIds[$programId])
                ? $this->programStudentIds[$programId]
                : $this->studentIds;

            if (empty($programStudents)) {
                $bar->advance();

                continue;
            }

            // Random sample of students from the program for this item
            $sampleSize = min($progressPerItem, count($programStudents));
            $selectedStudents = array_rand(array_flip($programStudents), $sampleSize);
            if (! is_array($selectedStudents)) {
                $selectedStudents = [$selectedStudents];
            }

            foreach ($selectedStudents as $studentId) {
                $isCompleted = fake()->boolean(70);
                $lastAccessed = fake()->dateTimeBetween('-60 days', 'now');

                $progress[] = [
                    'user_id' => $studentId,
                    'module_item_id' => $itemId,
                    'course_id' => $courseId,
                    'completed_at' => $isCompleted ? fake()->dateTimeBetween('-60 days', 'now') : null,
                    'last_accessed_at' => $lastAccessed,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($progress) >= $this->batchSize) {
                    DB::table('module_item_progress')->insert($progress);
                    $progress = [];
                }
            }
            $bar->advance();
        }

        if (! empty($progress)) {
            DB::table('module_item_progress')->insert($progress);
        }

        $bar->finish();
        $this->command->info('');
        $this->command->info('  Created module item progress records');
    }

    /**
     * Seed course grades.
     */
    protected function seedCourseGrades(): void
    {
        $gradesPerCourse = 100;
        $this->command->info('Seeding course grades...');

        $bar = $this->command->getOutput()->createProgressBar(count($this->courseIds));
        $bar->start();

        $grades = [];
        $now = now();

        foreach ($this->courseIds as $courseId) {
            // Get the program for this course
            $programId = $this->courseProgramIds[$courseId] ?? null;

            // Only use students from the course's program
            $programStudents = $programId && isset($this->programStudentIds[$programId])
                ? $this->programStudentIds[$programId]
                : $this->studentIds;

            if (empty($programStudents)) {
                $bar->advance();

                continue;
            }

            // Random sample of students from the program for this course
            $sampleSize = min($gradesPerCourse, count($programStudents));
            $selectedStudents = array_rand(array_flip($programStudents), $sampleSize);
            if (! is_array($selectedStudents)) {
                $selectedStudents = [$selectedStudents];
            }

            foreach ($selectedStudents as $studentId) {
                $pointsTotal = fake()->randomElement([100, 200, 500, 1000]);
                $percentage = fake()->randomFloat(2, 40, 100);
                $pointsEarned = round($pointsTotal * ($percentage / 100), 2);

                $grades[] = [
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'points_earned' => $pointsEarned,
                    'points_total' => $pointsTotal,
                    'percentage' => $percentage,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($grades) >= $this->batchSize) {
                    DB::table('course_grades')->insert($grades);
                    $grades = [];
                }
            }
            $bar->advance();
        }

        if (! empty($grades)) {
            DB::table('course_grades')->insert($grades);
        }

        $bar->finish();
        $this->command->info('');
        $this->command->info('  Created course grade records');
    }

    /**
     * Seed announcements.
     */
    protected function seedAnnouncements(): void
    {
        $totalAnnouncements = count($this->courseIds) * $this->config['announcements_per_course'];
        $this->command->info("Seeding announcements ({$totalAnnouncements} total)...");

        $announcements = [];
        $now = now();
        $priorities = ['low', 'medium', 'high'];

        foreach ($this->courseIds as $courseId) {
            $instructorId = $this->instructorIds[array_rand($this->instructorIds)];

            for ($i = 0; $i < $this->config['announcements_per_course']; $i++) {
                $announcements[] = [
                    'course_id' => $courseId,
                    'user_id' => $instructorId,
                    'title' => fake()->sentence(fake()->numberBetween(4, 10)),
                    'content' => fake()->paragraphs(fake()->numberBetween(2, 5), true),
                    'type' => 'course',
                    'priority' => $priorities[array_rand($priorities)],
                    'target_audience' => 'students',
                    'is_published' => fake()->boolean(80),
                    'send_email' => fake()->boolean(30),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($announcements) >= $this->batchSize) {
                    DB::table('announcements')->insert($announcements);
                    $announcements = [];
                }
            }
        }

        if (! empty($announcements)) {
            DB::table('announcements')->insert($announcements);
        }

        $this->command->info('  Created announcements');
    }

    /**
     * Seed login logs.
     */
    protected function seedLoginLogs(): void
    {
        $this->command->info("Seeding login logs ({$this->config['login_logs']} total)...");

        $bar = $this->command->getOutput()->createProgressBar($this->config['login_logs']);
        $bar->start();

        $logs = [];
        $allUserIds = array_merge($this->adminIds, $this->instructorIds, $this->studentIds);
        $statuses = ['success', 'success', 'success', 'success', 'success', 'success', 'success', 'success', 'success', 'failed'];
        $failureReasons = ['Invalid password', 'Account locked', 'Session expired', null];
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        ];

        for ($i = 0; $i < $this->config['login_logs']; $i++) {
            $userId = $allUserIds[array_rand($allUserIds)];
            $status = $statuses[array_rand($statuses)];

            $logs[] = [
                'user_id' => $userId,
                'email' => "user{$userId}@example.com",
                'ip_address' => fake()->ipv4(),
                'user_agent' => $userAgents[array_rand($userAgents)],
                'status' => $status,
                'failure_reason' => $status === 'failed' ? $failureReasons[array_rand($failureReasons)] : null,
                'created_at' => fake()->dateTimeBetween('-90 days', 'now'),
            ];

            if (count($logs) >= $this->batchSize) {
                DB::table('login_logs')->insert($logs);
                $bar->advance(count($logs));
                $logs = [];
            }
        }

        if (! empty($logs)) {
            DB::table('login_logs')->insert($logs);
            $bar->advance(count($logs));
        }

        $bar->finish();
        $this->command->info('');
        $this->command->info("  Created {$this->config['login_logs']} login logs");
    }

    /**
     * Seed course audit logs.
     */
    protected function seedCourseAuditLogs(): void
    {
        $this->command->info("Seeding course audit logs ({$this->config['course_audit_logs']} total)...");

        $bar = $this->command->getOutput()->createProgressBar($this->config['course_audit_logs']);
        $bar->start();

        $logs = [];
        $eventTypes = ['created', 'updated', 'published', 'archived', 'restored'];
        $userTypes = ['admin', 'instructor'];

        for ($i = 0; $i < $this->config['course_audit_logs']; $i++) {
            $courseId = $this->courseIds[array_rand($this->courseIds)];
            $eventType = $eventTypes[array_rand($eventTypes)];
            $userType = $userTypes[array_rand($userTypes)];
            $userId = $userType === 'admin'
                ? $this->adminIds[array_rand($this->adminIds)]
                : $this->instructorIds[array_rand($this->instructorIds)];

            $logs[] = [
                'auditable_type' => Course::class,
                'auditable_id' => $courseId,
                'event_type' => $eventType,
                'old_values' => $eventType !== 'created' ? json_encode(['status' => 'draft']) : null,
                'new_values' => json_encode(['status' => $eventType === 'published' ? 'published' : 'active']),
                'user_id' => $userId,
                'user_type' => $userType,
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'description' => "Course was {$eventType}",
                'created_at' => fake()->dateTimeBetween('-90 days', 'now'),
            ];

            if (count($logs) >= $this->batchSize) {
                DB::table('course_audit_log')->insert($logs);
                $bar->advance(count($logs));
                $logs = [];
            }
        }

        if (! empty($logs)) {
            DB::table('course_audit_log')->insert($logs);
            $bar->advance(count($logs));
        }

        $bar->finish();
        $this->command->info('');
        $this->command->info("  Created {$this->config['course_audit_logs']} course audit logs");
    }
}
