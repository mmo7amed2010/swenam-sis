# System Overview - CampusLink LMS

## What is the System

CampusLink is a Learning Management System (LMS) built with Laravel 12 and PHP 8.3, designed to manage academic programs, courses, student learning, and assessment workflows. The system serves three primary user types: Students, Instructors, and Administrators.

---

## System Architecture

### Technology Stack

- **Framework**: Laravel 12
- **PHP Version**: 8.3
- **Database**: MySQL
- **Frontend Theme**: Metronic
- **Authorization**: Spatie Permission package for role-based access control
- **Architecture Pattern**: Service/Repository pattern for business logic separation
- **UI Components**: DataTables for admin listings, AJAX modals for CRUD operations

### Key Architectural Patterns

1. **Service Layer**: Business logic encapsulated in service classes (e.g., `StudentDashboardService`, `ModuleItemProgressService`)
2. **Repository Pattern**: Data access abstraction (e.g., `LessonProgressRepository`, `AssignmentRepository`)
3. **Polymorphic Relationships**: `ModuleItem` uses polymorphic relations to link Lessons, Quizzes, and Assignments
4. **Trait-Based Reusability**: `HandlesTransactions`, `HandlesDataTableRequests` for common controller functionality

---

## User Types and Access Model

### Student (`user_type='student'`)

**Access Model**: Program-based course access (no explicit enrollment required)

- Students are assigned to a `Program` via `User.program_id`
- Course access is determined by `Course.program_id` matching `User.program_id`
- All courses in the student's program are automatically accessible
- No `CourseEnrollment` records are created or used for access control

**Key Relationships**:
- `User` -> `Student` (via `user_id`) - dual identity pattern
- `User.program_id` -> `Program.id`
- Progress tracked via `ModuleItemProgress` and `LessonProgress` tables

**Primary Features**:
- Dashboard with progress overview, upcoming deadlines, course cards
- Course content viewing with module/lesson navigation
- Assignment submission and resubmission
- Quiz taking with attempt tracking
- Grade viewing and transcript download
- Program overview with course roadmap

### Instructor (`user_type='instructor'`)

**Access Model**: Course assignment via `course_instructors` pivot table

- Instructors are assigned to courses through `CourseInstructor` records
- Roles: `lead` (primary instructor) or `co-instructor`
- Access controlled by checking `course_instructors` table for active assignments

**Key Relationships**:
- `User` -> `CourseInstructor` -> `Course`
- Can be assigned to multiple courses
- Soft removal via `removed_at` timestamp

**Primary Features**:
- Course management (view assigned courses)
- Assignment creation and management
- Quiz/exam creation
- Submission grading (draft/publish workflow)
- Submission file preview and download
- **Note**: Dashboard currently returns empty data (incomplete implementation)

### Admin (`user_type='admin'` + Spatie Roles/Permissions)

**Access Model**: Dual authorization system

- Primary check: `user_type='admin'`
- Secondary check: Spatie permissions (e.g., `manage courses`, `delete courses`)
- Can have granular permissions via Spatie roles

**Primary Features**:
- Program management (CRUD operations)
- Course management (create, edit, publish, archive, clone)
- Student and instructor management
- Application review and approval workflow
- User management with role assignment
- **Note**: Dashboard shows "Coming Soon" placeholder (incomplete implementation)

---

## Core Business Flows

### 1. Student Application Flow

```
Student Application (Multi-step form)
  -> Admin Review
  -> Approval/Rejection
  -> If Approved: Student record created + Program assignment
  -> Welcome email sent
```

**Key Models**: `StudentApplication`, `Student`, `User`, `Program`

### 2. Course Creation and Publishing Flow

```
Admin creates Course (draft status)
  -> Assigns to Program
  -> Creates Modules
  -> Adds Content (Lessons, Quizzes, Assignments via ModuleItem)
  -> Assigns Instructors
  -> Publishes Course (status: active)
  -> Students in program gain automatic access
```

**Key Models**: `Course`, `CourseModule`, `ModuleItem`, `ModuleLesson`, `Quiz`, `Assignment`, `CourseInstructor`

### 3. Student Learning Flow

```
Student accesses Course (via program membership)
  -> Views Course Content (modules, lessons, quizzes, assignments)
  -> Marks Lessons as Complete (manual action)
  -> Takes Quizzes (auto-completes on submission)
  -> Submits Assignments (auto-completes on submission)
  -> Progress tracked via ModuleItemProgress (weight-based calculation)
```

**Progress Calculation**:
- Weight-based: `(completed_item_weights / total_item_weights) * 100`
- Quiz/Assignment weights = `total_points`
- Lesson weights = 1 (default)

### 4. Assessment and Grading Flow

**Assignments**:
```
Student submits Assignment
  -> Submission record created
  -> Instructor views submission
  -> Instructor grades (draft or publish)
  -> If published: Email notification sent to student
  -> CourseGrade updated automatically
```

**Quizzes**:
```
Student starts Quiz Attempt
  -> Answers saved in real-time (AJAX)
  -> Student submits attempt
  -> Auto-graded (MCQ/True-False only)
  -> Results displayed immediately
  -> CourseGrade updated automatically
```

**Key Models**: `Submission`, `Grade`, `QuizAttempt`, `CourseGrade`

---

## Data Model Structure

### Program Hierarchy

```
Program
  └── Course (via program_id)
      └── CourseModule (via course_id)
          └── ModuleItem (polymorphic, via module_id)
              ├── ModuleLesson (itemable_type/itemable_id)
              ├── Quiz (itemable_type/itemable_id)
              └── Assignment (itemable_type/itemable_id)
```

### User Identity Model

**Dual Identity Pattern**:
- `User` table: Core authentication and basic info
- `Student` table: Extended student-specific data (student_number, enrollment_date, etc.)
- Relationship: `Student.user_id` -> `User.id`
- Both tables store `first_name`, `last_name`, `email` (data duplication)

### Progress Tracking Models

**Two Parallel Systems**:

1. **Legacy System**: `LessonProgress`
   - Tracks only lessons
   - Simple count-based progress: `(completed_lessons / total_lessons) * 100`
   - Used by `LessonProgressRepository`

2. **New System**: `ModuleItemProgress`
   - Tracks all module items (lessons, quizzes, assignments)
   - Weight-based progress calculation
   - Used by `ModuleItemProgressService`

**Note**: Both systems are currently active, causing potential inconsistencies.

### Grading Models

- `Grade`: Assignment grades (versioned, draft/published workflow)
- `CourseGrade`: Aggregated course grades (calculated from assignments + quizzes)
- `QuizAttempt`: Quiz scores (auto-graded)

---

## Key System Components

### Controllers

**Student Controllers**:
- `StudentCourseController`: Course listing and detail views
- `StudentAssignmentController`: Assignment viewing and submission
- `Student/QuizController`: Quiz taking and results
- `Student/GradeController`: Grade viewing and transcript
- `Student/ModuleItemController`: Lesson completion tracking

**Instructor Controllers**:
- `Instructor/CourseController`: Assigned course viewing
- `Admin/AssignmentGradeController`: Submission grading interface

**Admin Controllers**:
- `Admin/ProgramController`: Program management
- `Admin/CourseController`: Course CRUD operations
- `Admin/CourseModuleController`: Module management
- `Admin/AssignmentController`: Assignment management
- `Admin/QuizController`: Quiz/exam management
- `Admin/StudentController`: Student management
- `Admin/InstructorController`: Instructor management

### Services

**Progress Services**:
- `ModuleItemProgressService`: Weight-based progress calculation (new system)
- `LessonProgressRepository`: Lesson-only progress (legacy system)
- `ProgramProgressService`: Program-level progress aggregation

**Grading Services**:
- `AssignmentGradingService`: Assignment grading workflow
- `QuizGradingService`: Auto-grading for quizzes
- `CourseGradeService`: Course-level grade aggregation

**Dashboard Services**:
- `StudentDashboardService`: Student dashboard data aggregation
- **Missing**: `InstructorDashboardService` (instructor dashboard returns empty data)
- **Missing**: `AdminDashboardService` (admin dashboard shows placeholder)

### Models

**Core Models**: `User`, `Student`, `Instructor`, `Program`, `Course`, `CourseModule`, `ModuleItem`

**Content Models**: `ModuleLesson`, `Quiz`, `QuizQuestion`, `Assignment`

**Progress Models**: `ModuleItemProgress`, `LessonProgress`

**Assessment Models**: `Submission`, `Grade`, `QuizAttempt`, `CourseGrade`

**Relationship Models**: `CourseInstructor`, `CourseEnrollment` (unused)

---

## Key Files Reference

### Routing and Access Control

- **`app/Http/Controllers/DashboardController.php`**: Routes users to appropriate dashboard based on `user_type`
- **`app/Http/Middleware/CheckDashboardAccess.php`**: Dashboard access authorization
- **`app/Http/Middleware/EnsureUserIsStudent.php`**: Student-only route protection

### Progress Tracking

- **`app/Services/ModuleItemProgressService.php`**: Weight-based progress calculation (primary system)
- **`app/Services/ProgramProgressService.php`**: Program-level progress and GPA calculation
- **`app/Repositories/LessonProgressRepository.php`**: Legacy lesson-only progress tracking

### Grading

- **`app/Services/CourseGradeService.php`**: Course grade aggregation from assignments and quizzes
- **`app/Services/AssignmentGradingService.php`**: Assignment grading with draft/publish workflow
- **`app/Services/QuizGradingService.php`**: Auto-grading for MCQ and True/False questions

### User Management

- **`app/Models/User.php`**: Core user model with `user_type` field and helper methods (`isStudent()`, `isInstructor()`, `isAdmin()`)
- **`app/Models/Student.php`**: Extended student data model
- **`app/Models/Instructor.php`**: Instructor model (extends User with global scope)

---

## System Status Summary

### Fully Implemented

✅ Student dashboard with real data  
✅ Course content viewing and navigation  
✅ Assignment submission and resubmission  
✅ Quiz taking with attempt tracking  
✅ Assignment grading workflow  
✅ Course grade calculation  
✅ Program progress tracking  
✅ Student grade viewing and transcript  

### Partially Implemented

⚠️ Instructor dashboard (returns empty arrays)  
⚠️ Admin dashboard (placeholder only)  
⚠️ Progress tracking (dual systems running in parallel)  

### Not Implemented

❌ Laravel Policies for authorization  
❌ Instructor gradebook view  
❌ Standardized ordering field names  

---

## Technical Debt and Known Issues

1. **Dual Progress Systems**: Both `LessonProgress` and `ModuleItemProgress` are active
2. **Unused Enrollment Model**: `CourseEnrollment` table exists but is never used
3. **ID Reference Inconsistency**: Mix of `User.id` and `Student.id` references
4. **Ordering Field Names**: Inconsistent naming (`order_index`, `order_number`, `order_position`)
5. **N+1 Query Issues**: Progress calculated in Blade templates with service calls

---

## Conclusion

CampusLink LMS is a functional learning management system with a solid foundation in Laravel. The core student learning workflow is complete and operational. However, there are several architectural inconsistencies and incomplete implementations that need attention, particularly around progress tracking, enrollment models, and instructor/admin dashboards.

