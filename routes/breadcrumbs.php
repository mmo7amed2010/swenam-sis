<?php

use App\Models\Instructor;
use App\Models\Program;
use App\Models\Student;
use App\Models\StudentApplication;
use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Spatie\Permission\Models\Role;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push(__('Home'), route('dashboard'));
});

// Home > Dashboard
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(__('Dashboard'), route('dashboard'));
});

// Home > Dashboard > User Management
Breadcrumbs::for('user-management.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('User Management'), route('user-management.users.index'));
});

// Home > Dashboard > User Management > Users
Breadcrumbs::for('user-management.users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push(__('Users'), route('user-management.users.index'));
});

// Home > Dashboard > User Management > Users > [User]
Breadcrumbs::for('user-management.users.show', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('user-management.users.index');
    $trail->push(ucwords($user->name), route('user-management.users.show', $user));
});



// Home > Dashboard > User Management > Permission
Breadcrumbs::for('user-management.permissions.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push(__('Permissions'), route('user-management.permissions.index'));
});

// Home > Dashboard > Translations
Breadcrumbs::for('translations.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('Translations Management'), route('translations.index'));
});

// Home > Dashboard > Department Management
Breadcrumbs::for('admin.departments.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('Department Management'), route('admin.departments.index'));
});

// Home > Dashboard > User Management > Students
Breadcrumbs::for('admin.students.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push(__('Students'), route('admin.students.index'));
});

// Home > Dashboard > User Management > Students > [Student]
Breadcrumbs::for('admin.students.show', function (BreadcrumbTrail $trail, Student $student) {
    $trail->parent('admin.students.index');
    $studentName = trim($student->full_name) ?: $student->student_number ?: __('Student');
    $trail->push($studentName, route('admin.students.show', $student));
});

// Home > Dashboard > User Management > Students > [Student] > Edit
Breadcrumbs::for('admin.students.edit', function (BreadcrumbTrail $trail, Student $student) {
    $trail->parent('admin.students.show', $student);
    $trail->push(__('Edit'), route('admin.students.edit', $student));
});

// Home > Dashboard > User Management > Instructors
Breadcrumbs::for('admin.instructors.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push(__('Instructors'), route('admin.instructors.index'));
});

// Home > Dashboard > User Management > Instructors > Create
Breadcrumbs::for('admin.instructors.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.instructors.index');
    $trail->push(__('Create Instructor'), route('admin.instructors.create'));
});

// Home > Dashboard > User Management > Instructors > [Instructor]
Breadcrumbs::for('admin.instructors.show', function (BreadcrumbTrail $trail, Instructor $instructor) {
    $trail->parent('admin.instructors.index');
    $trail->push($instructor->name, route('admin.instructors.show', $instructor));
});

// Home > Dashboard > User Management > Instructors > [Instructor] > Edit
Breadcrumbs::for('admin.instructors.edit', function (BreadcrumbTrail $trail, Instructor $instructor) {
    $trail->parent('admin.instructors.show', $instructor);
    $trail->push(__('Edit'), route('admin.instructors.edit', $instructor));
});

// Home > Dashboard > Programs
Breadcrumbs::for('admin.programs.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('Programs'), route('admin.programs.index'));
});

// Home > Dashboard > Programs > [Program]
Breadcrumbs::for('admin.programs.show', function (BreadcrumbTrail $trail, Program $program) {
    $trail->parent('admin.programs.index');
    $trail->push($program->name, route('admin.programs.show', $program));
});

// Home > Dashboard > Applications
Breadcrumbs::for('admin.applications.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('Applications'), route('admin.applications.index'));
});

// Home > Dashboard > Applications > [Application]
Breadcrumbs::for('admin.applications.show', function (BreadcrumbTrail $trail, StudentApplication $application) {
    $trail->parent('admin.applications.index');
    $trail->push($application->reference_number, route('admin.applications.show', $application));
});

// ============================================
// Student Breadcrumbs
// ============================================

// Home > My Program > [Course]
// Note: student.courses.index removed - courses are displayed in student.program.index
Breadcrumbs::for('student.courses.show', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('student.program.index');
    $trail->push($course->name, route('student.courses.show', $course));
});

// Home > My Program
Breadcrumbs::for('student.program.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('My Program'), route('student.program.index'));
});

// Home > Grades
Breadcrumbs::for('student.grades.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('Grades'), route('student.grades.index'));
});

// Alias for student.grades
Breadcrumbs::for('student.grades', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('Grades'), route('student.grades.index'));
});

// Home > Grades > [Course]
Breadcrumbs::for('student.grades.show', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('student.grades.index');
    $trail->push($course->name, route('student.grades.show', $course));
});

// Home > Assignments (disabled - assignments accessible through module items)
// Breadcrumbs::for('student.assignments.index', function (BreadcrumbTrail $trail) {
//     $trail->parent('dashboard');
//     $trail->push(__('Assignments'), route('student.assignments.index'));
// });

// Add your custom breadcrumbs here

// ============================================
// Notifications Breadcrumbs
// ============================================

// Home > Notifications
Breadcrumbs::for('notifications.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('Notifications'), route('notifications.index'));
});

// Home > Notification Settings
Breadcrumbs::for('settings.notifications', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('Notification Settings'), route('settings.notifications'));
});

// Home > Announcements
Breadcrumbs::for('announcements.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('Announcements'), route('announcements.index'));
});

// Home > Announcements > [Announcement]
Breadcrumbs::for('announcements.show', function (BreadcrumbTrail $trail, $announcement) {
    $trail->parent('announcements.index');
    $trail->push($announcement->title, route('announcements.show', $announcement));
});

// ============================================
// Admin Announcements Breadcrumbs
// ============================================

// Home > Dashboard > Announcements
Breadcrumbs::for('admin.announcements.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('System Announcements'), route('admin.announcements.index'));
});

// Home > Dashboard > Announcements > Create
Breadcrumbs::for('admin.announcements.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.announcements.index');
    $trail->push(__('Create Announcement'), route('admin.announcements.create'));
});

// Home > Dashboard > Announcements > [Announcement]
Breadcrumbs::for('admin.announcements.show', function (BreadcrumbTrail $trail, $announcement) {
    $trail->parent('admin.announcements.index');
    $trail->push($announcement->title, route('admin.announcements.show', $announcement));
});

// Home > Dashboard > Announcements > [Announcement] > Edit
Breadcrumbs::for('admin.announcements.edit', function (BreadcrumbTrail $trail, $announcement) {
    $trail->parent('admin.announcements.show', $announcement);
    $trail->push(__('Edit'), route('admin.announcements.edit', $announcement));
});

// ============================================
// Instructor Announcements Breadcrumbs
// ============================================

// Home > My Courses > [Course] > Announcements
Breadcrumbs::for('instructor.announcements.index', function (BreadcrumbTrail $trail, $program, $course) {
    $trail->parent('dashboard');
    $trail->push(__('My Courses'), route('instructor.courses.index'));
    $trail->push($course->name, route('instructor.courses.show', [$program, $course]));
    $trail->push(__('Announcements'), route('instructor.announcements.index', [$program, $course]));
});

// Home > My Courses > [Course] > Announcements > Create
Breadcrumbs::for('instructor.announcements.create', function (BreadcrumbTrail $trail, $program, $course) {
    $trail->parent('instructor.announcements.index', $program, $course);
    $trail->push(__('Create'), route('instructor.announcements.create', [$program, $course]));
});

// Home > My Courses > [Course] > Announcements > [Announcement]
Breadcrumbs::for('instructor.announcements.show', function (BreadcrumbTrail $trail, $program, $course, $announcement) {
    $trail->parent('instructor.announcements.index', $program, $course);
    $trail->push($announcement->title, route('instructor.announcements.show', [$program, $course, $announcement]));
});

// Home > My Courses > [Course] > Announcements > [Announcement] > Edit
Breadcrumbs::for('instructor.announcements.edit', function (BreadcrumbTrail $trail, $program, $course, $announcement) {
    $trail->parent('instructor.announcements.index', $program, $course);
    $trail->push($announcement->title, route('instructor.announcements.show', [$program, $course, $announcement]));
    $trail->push(__('Edit'), route('instructor.announcements.edit', [$program, $course, $announcement]));
});
