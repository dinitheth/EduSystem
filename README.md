# 🎓 EduSystem — Laravel Education Portal

A full-featured, multi-portal school management system built with **Laravel 11**, **MySQL**, and **Bootstrap 5**. Supports separate portals for **Administrators**, **Teachers**, and **Students** with role-based authentication, course content management, MCQ testing, assignment submission & grading, and more.

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Database Schema](#-database-schema)
- [Routes](#-routes)
- [Installation](#-installation)
- [Default Credentials](#-default-credentials)
- [Portals Overview](#-portals-overview)

---

## ✨ Features

### 🏫 Admin Panel (`/dashboard`)
- Manage **Students**, **Teachers**, and **Subjects** via CRUD tables
- **Import** data via Excel/CSV (using Laravel Excel)
- **Export** data to Excel/CSV
- Bulk assign subjects to students and teachers

### 👨‍🏫 Teacher Portal (`/teacher/*`)
- Secure session-based login
- **Dashboard** with live date/time clock, assignment stats, MCQ stats, recent student submissions
- **My Students** — view all students in the teacher's class with live search
- **Course Content Manager** — add/remove multi-type content per subject (Text, PDF, Video, Link, YouTube embed with live preview)
- **Assignments** — create and post PDF assignments; view submission count; grade individual submissions with marks, max marks, feedback
- **Download All Submissions (ZIP)** — bulk-download all student submission files as a ZIP
- **MCQ Tests** — create multi-question MCQs with timed exams; view student results
- **Results & Marks** — full marks ledger for all MCQ submissions

### 👩‍🎓 Student Portal (`/student/*`)
- Secure session-based login
- **Dashboard** with live clock, subject/assignment/MCQ stats
- **Courses** — view enrolled subjects; click any card to open a full-screen content drawer showing all published course materials (Text, PDF, Video, YouTube)
- **Assignments** — view class assignments; submit work (notes + file upload); see submission status; download assignment PDFs
- **MCQ Tests** — take timed multiple-choice tests
- **My Marks** — academic marks overview with grade badges and progress bars

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 11 (PHP 8.2+) |
| **Database** | MySQL 8 |
| **Frontend** | Bootstrap 5.3, Bootstrap Icons 1.11, Inter (Google Fonts) |
| **Auth** | Custom session-based (no Laravel Breeze/Sanctum) |
| **File Storage** | `Storage::disk('public')` — symlinked via `php artisan storage:link` |
| **Import/Export** | Laravel Excel (Maatwebsite) |
| **ZIP Download** | PHP `ZipArchive` (native) |

---

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Portal/
│   │   │   ├── StudentAuthController.php      ← Student login/logout
│   │   │   ├── TeacherAuthController.php      ← Teacher login/logout
│   │   │   ├── StudentPortalController.php    ← All student portal pages
│   │   │   └── TeacherPortalController.php    ← All teacher portal pages
│   │   ├── DashboardController.php            ← Admin dashboard
│   │   ├── StudentController.php              ← Admin student CRUD
│   │   ├── TeacherController.php              ← Admin teacher CRUD
│   │   ├── SubjectController.php              ← Admin subject CRUD
│   │   ├── ImportController.php               ← Excel import
│   │   └── ExportController.php               ← Excel export
│   └── Middleware/
│       ├── StudentAuth.php                    ← Guards student routes
│       └── TeacherAuth.php                    ← Guards teacher routes
├── Models/
│   ├── Student.php
│   ├── Teacher.php
│   ├── Subject.php
│   ├── Assignment.php
│   ├── AssignmentSubmission.php               ← Student work submissions + grades
│   ├── CourseContent.php                      ← Multi-type course material
│   ├── Mcq.php
│   ├── McqQuestion.php
│   ├── McqOption.php
│   ├── McqSubmission.php
│   ├── McqAnswer.php
│   ├── Mark.php
│   ├── StudentLogin.php
│   └── TeacherLogin.php
resources/views/
├── portal/
│   ├── layout.blade.php                       ← Shared portal layout (sidebar + topbar + clock)
│   ├── student/
│   │   ├── login.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── courses.blade.php                  ← Full-screen content drawer
│   │   ├── assignments.blade.php              ← Submit work inline
│   │   ├── mcqs.blade.php
│   │   ├── marks.blade.php
│   │   └── (mcq_take, mcq_result…)
│   └── teacher/
│       ├── login.blade.php
│       ├── dashboard.blade.php
│       ├── students.blade.php                 ← Class student list + search
│       ├── courses.blade.php                  ← Course content manager drawer
│       ├── assignments.blade.php              ← Post & grade assignments
│       ├── assignment_submissions.blade.php   ← Grade submissions + ZIP download
│       ├── mcqs.blade.php
│       ├── mcq_create.blade.php
│       ├── mcq_results.blade.php
│       └── marks.blade.php
database/migrations/
├── create_students_table
├── create_teachers_table
├── create_subjects_table
├── create_student_logins_table
├── create_teacher_logins_table
├── create_student_subject_table               ← Pivot
├── create_teacher_subject_table               ← Pivot
├── create_assignments_table
├── create_assignment_submissions_table        ← marks, feedback, graded_by
├── create_course_contents_table               ← type, title, file_path, url
├── create_mcqs_table
├── create_mcq_questions_table
├── create_mcq_options_table
├── create_mcq_submissions_table
├── create_mcq_answers_table
└── create_marks_table
```

---

## 🗄 Database Schema

### Core Tables

| Table | Key Fields |
|---|---|
| `students` | `id`, `full_name`, `email`, `reg_no`, `class`, `phone` |
| `teachers` | `id`, `full_name`, `email`, `employee_no`, `class`, `specialization`, `department` |
| `subjects` | `id`, `subject_name`, `subject_code`, `description` |
| `student_subject` | `student_id`, `subject_id` (pivot) |
| `teacher_subject` | `teacher_id`, `subject_id` (pivot) |

### Assignment & Submission

| Table | Key Fields |
|---|---|
| `assignments` | `teacher_id`, `subject_id`, `class`, `title`, `description`, `file_path`, `due_date` |
| `assignment_submissions` | `assignment_id`, `student_id`, `notes`, `file_path`, `status`, `marks`, `max_marks`, `feedback`, `graded_by`, `graded_at` |

### Course Content

| Table | Key Fields |
|---|---|
| `course_contents` | `subject_id`, `teacher_id`, `type` (text/pdf/video/link/youtube), `title`, `description`, `content_text`, `file_path`, `url`, `sort_order` |

### MCQ System

| Table | Key Fields |
|---|---|
| `mcqs` | `teacher_id`, `subject_id`, `class`, `title`, `time_limit`, `expires_at` |
| `mcq_questions` | `mcq_id`, `question`, `order` |
| `mcq_options` | `question_id`, `option_text`, `is_correct` |
| `mcq_submissions` | `mcq_id`, `student_id`, `score`, `total`, `submitted_at` |
| `mcq_answers` | `submission_id`, `question_id`, `option_id` |
| `marks` | `mcq_submission_id`, `student_id`, `subject_id`, `score`, `total`, `type` |

---

## 🛣 Routes

### Admin / General (no auth)
```
GET  /                          → redirect to /dashboard
GET  /dashboard                 → DashboardController@index
POST /students                  → StudentController@store
PUT  /students/{id}             → StudentController@update
DELETE /students/{id}           → StudentController@destroy
POST /teachers                  → TeacherController@store
POST /subjects                  → SubjectController@store
GET  /import                    → ImportController@index
POST /import                    → ImportController@import
POST /import/confirm            → ImportController@confirm
POST /export                    → ExportController@export
```

### 🎓 Student Portal (prefix: `/student`, middleware: `student.auth`)
```
GET  /student/login             → StudentAuthController@showLogin
POST /student/login             → StudentAuthController@login
POST /student/logout            → StudentAuthController@logout

GET  /student/dashboard         → StudentPortalController@dashboard
GET  /student/courses           → StudentPortalController@courses
GET  /student/courses/{id}/content (JSON) → StudentPortalController@courseContent
GET  /student/assignments       → StudentPortalController@assignments
POST /student/assignments/{id}/submit → StudentPortalController@submitAssignment
GET  /student/mcqs              → StudentPortalController@mcqs
GET  /student/mcqs/{id}         → StudentPortalController@takeMcq
POST /student/mcqs/{id}/submit  → StudentPortalController@submitMcq
GET  /student/results/{id}      → StudentPortalController@mcqResult
GET  /student/marks             → StudentPortalController@marks
```

### 👨‍🏫 Teacher Portal (prefix: `/teacher`, middleware: `teacher.auth`)
```
GET  /teacher/login             → TeacherAuthController@showLogin
POST /teacher/login             → TeacherAuthController@login
POST /teacher/logout            → TeacherAuthController@logout

GET  /teacher/dashboard         → TeacherPortalController@dashboard
GET  /teacher/students          → TeacherPortalController@students
GET  /teacher/courses           → TeacherPortalController@courses
GET  /teacher/courses/{id}/content (JSON)  → TeacherPortalController@courseContent
POST /teacher/courses/{id}/content         → TeacherPortalController@storeCourseContent
DELETE /teacher/courses/{id}/content/{cid} → TeacherPortalController@deleteCourseContent
GET  /teacher/assignments       → TeacherPortalController@assignments
POST /teacher/assignments       → TeacherPortalController@storeAssignment
GET  /teacher/assignments/{id}/submissions → TeacherPortalController@viewSubmissions
POST /teacher/assignments/{id}/submissions/{sid}/grade → TeacherPortalController@gradeSubmission
GET  /teacher/assignments/{id}/submissions/download-all → TeacherPortalController@downloadAllSubmissions
GET  /teacher/mcqs              → TeacherPortalController@mcqs
GET  /teacher/mcqs/create       → TeacherPortalController@createMcq
POST /teacher/mcqs              → TeacherPortalController@storeMcq
GET  /teacher/mcqs/{id}/results → TeacherPortalController@results
GET  /teacher/marks             → TeacherPortalController@marks
```

---

## ⚙️ Installation

```bash
# 1. Clone the repo
git clone <repo-url> edusystem
cd edusystem

# 2. Install PHP dependencies
composer install

# 3. Copy environment file and configure DB
cp .env.example .env
# Edit .env: set DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 4. Generate app key
php artisan key:generate

# 5. Run migrations
php artisan migrate

# 6. Link storage (for file uploads)
php artisan storage:link

# 7. (Optional) Seed sample data
php artisan db:seed

# 8. Start the development server
php artisan serve
```

> **Requirements:** PHP 8.2+, MySQL 8, Composer, Node (optional for assets), PHP `zip` extension enabled for ZIP download feature.

---

## 🔑 Default Credentials

| Role | Email | Password |
|---|---|---|
| **Student** | `kasun.perera@gmail.com` | `Abc123` |
| **Teacher** | `amal.jayawickrama@school.lk` | `Abc123` |

> Admin panel is at `/dashboard` (no login required — add middleware if needed for production).

---

## 🖥 Portals Overview

### Student Flow
1. Login at `/student/login`
2. **Dashboard** — overview of enrolled subjects, pending assignments, MCQs, marks
3. **Courses** — click any subject card → full-screen content drawer (YouTube, PDFs, text, links)
4. **Assignments** — download assignment PDF → submit notes + file → teacher grades it
5. **MCQ Tests** — take timed tests → view instant result with score and grade
6. **My Marks** — academic record with grade badges (A/B/C/F)

### Teacher Flow
1. Login at `/teacher/login`
2. **Dashboard** — summary with EMP No badge, quick stats, recent submissions
3. **My Students** — searchable list of all Class students with reg no, phone, subjects
4. **Courses** — manage course content per subject (add Text/PDF/Video/YouTube/Link, delete)
5. **Assignments** — post new assignments with PDF; view number of submissions; click "Grade" to open grading page
6. **Grading** — per-student: view notes, download file (PDF/Word/Image etc.), enter marks/max marks/feedback → "Download All (ZIP)" for bulk download
7. **MCQ Tests** — create multi-question MCQs with time limits
8. **Results & Marks** — full student performance ledger

---

## 📎 Notes

- **File Storage:** All uploaded files (assignments, submissions, course PDFs/videos) are stored in `storage/app/public/` and served via the `public` disk symlink.
- **Security:** Both portals use custom session middleware (`student.auth`, `teacher.auth`). Data is always scoped to the authenticated user's class/subjects — no cross-portal data leakage.
- **ZIP Download:** Requires PHP's `zip` extension. Check with `php -m | findstr zip` (Windows) or `php -m | grep zip` (Linux).
- **YouTube Embeds:** The `CourseContent` model automatically extracts the YouTube video ID from any YouTube URL format and generates a safe embed URL.

---

*Built with ❤️ using Laravel 11 · Bootstrap 5 · MySQL*
