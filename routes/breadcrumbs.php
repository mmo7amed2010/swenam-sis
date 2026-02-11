<?php

use App\Models\Student;
use App\Models\StudentApplication;
use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push(__('Home'), route('dashboard'));
});

// Home > Home
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(__('Home'), route('dashboard'));
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

// Home > Dashboard > Agents
Breadcrumbs::for('admin.agents.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('Agents'), route('admin.agents.index'));
});

// Home > Dashboard > Agents > [Agent]
Breadcrumbs::for('admin.agents.show', function (BreadcrumbTrail $trail, User $agent) {
    $trail->parent('admin.agents.index');
    $trail->push($agent->name, route('admin.agents.show', $agent));
});

// ============================================
// Student Breadcrumbs
// ============================================

// Home > My Application
Breadcrumbs::for('student.program.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('My Application'), route('student.program.index'));
});

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

// ============================================
// Announcements Breadcrumbs
// ============================================

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
