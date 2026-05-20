# CodeXpress Institute Management System

CodeXpress Institute is a Laravel-based education management platform with a public website, admin dashboard, teacher portal, and student portal.

It supports website registrations, pending approval flow, class and subject assignment, assignments, MCQ exams, marks, notifications, and email-ready alerts.

## Stack

- PHP 8.2+
- Laravel 12
- MySQL
- Bootstrap 5
- Bootstrap Icons
- Laravel Mail
- Laravel Excel
- Smalot PDF Parser

## Main Areas

### Public Website

Route:

- `/`

Features:

- Public institute website for CodeXpress Institute
- Light and dark theme switcher
- Course list loaded from the real `subjects` table
- Subject search in the hero section
- Student registration form
- Subject suggestions from the live subject database
- Validation for unavailable subjects
- Student registrations saved to `pending_students`

Registration fields:

- Full Name
- Email
- Phone
- Date of Birth
- Gender
- Subjects the student wants

Extra route:

- `/register-interest`

This route posts website registration requests and now safely redirects to the website register section if opened directly by browser.

## Admin Dashboard

Routes:

- `/admin/login`
- `/dashboard`
- `/students`
- `/teachers`
- `/subjects`
- `/pending-students`

Default admin credentials:

- Username: `Admin`
- Password: `Admin123`

Admin can:

- View dashboard analytics
- Manage students
- Manage teachers
- Manage subjects
- Review pending student requests from website
- Approve or dismiss pending students
- Assign class on approval
- Finalize subject assignment on approval
- Import and export records

## Pending Student Approval Flow

When a student registers through the website:

1. The request is stored in `pending_students`
2. It appears in the `Pending Students` admin section
3. Admin can approve or dismiss it
4. On approval, the system creates the real student account
5. The system assigns class
6. The system assigns selected subjects
7. The system creates the student portal login
8. The system generates the registration number automatically

Important update:

- In the approve modal, admin now sees only the subjects the student actually selected
- It no longer shows the entire subject catalog

## Student Management

Admin student page:

- `/students`

Features:

- Add student
- Edit student
- Delete student
- Assign subjects
- Assign class
- Search students
- Export students

### Registration Number Format

Student registration numbers are auto-generated in ordered format:

- `REG10001`
- `REG10002`
- `REG10003`

Behavior:

- Pending-student approval uses this format
- Manual `Register New Student` modal also auto-generates this format
- New student modal opens with the next Reg No already filled in

Default generated student password:

- `Abc123`

## Teacher Management

Admin teacher page:

- `/teachers`

Features:

- Add teacher
- Edit teacher
- Delete teacher
- Assign subjects taught
- Assign class
- Search teachers
- Export teachers

### Employee Number Format

Teacher employee numbers are auto-generated in ordered format:

- `T100`
- `T101`
- `T102`

Behavior:

- `Add New Teacher` modal auto-fills the next employee number
- Edit mode keeps the existing employee number editable

Default generated teacher password:

- `Abc123`

## Subject Management

Admin subject page:

- `/subjects`

Features:

- Add subject
- Edit subject
- Delete subject
- Mark subject active/inactive
- Search subjects
- Export subjects

### Subject Code Format

Subject codes are auto-generated in ordered format:

- `SUB001`
- `SUB002`
- `SUB003`

Behavior:

- `Add New Subject` modal auto-fills the next subject code
- Edit mode keeps the current subject code editable

## Teacher Portal

Routes:

- `/teacher/login`
- `/teacher/dashboard`

Teacher can:

- Log in with teacher email and password
- View dashboard statistics
- View class students
- View assigned subjects
- Upload course content
- Publish assignments
- Edit assignments
- Delete assignments
- View assignment submissions
- Grade assignments
- Create MCQ tests
- Import MCQ questions from PDF, DOCX, XLSX, and XLS
- Schedule quiz start time
- Set minute-based quiz durations such as `5 Min`, `10 Min`, `20 Min`
- Set custom quiz duration
- View MCQ results
- View marks for assignments and MCQs
- Receive notifications when students submit work

## Student Portal

Routes:

- `/student/login`
- `/student/dashboard`

Student can:

- Log in with student email and password
- View enrolled courses
- Open course content
- View assignments
- Submit assignments
- View graded assignment marks
- Open MCQ tests
- Take MCQ exams
- View MCQ results
- View marks for assignments and MCQs
- Receive notifications for assignments and MCQs

Student MCQ cards now show:

- Start time
- Duration
- Pending / Starts Soon / Completed / Expired state

## Session and Login Persistence

The system uses Laravel database sessions.

Current session lifetime:

- `SESSION_LIFETIME=525600`

That is 525,600 minutes, or about 1 year.

This applies to:

- Admin login
- Teacher login
- Student login

Current session behavior:

- Sessions do not expire on browser close
- Admin, teacher, and student logins regenerate sessions cleanly
- Admin, teacher, and student logouts invalidate sessions cleanly

After changing session config, run:

```bash
php artisan config:clear
```

## Login Record Sync

The system keeps portal login accounts in:

- `student_logins`
- `teacher_logins`

Admin-side create and update flows automatically keep these tables in sync.

That means:

- New student records get a portal login automatically
- Updated student email also updates student login email
- New teacher records get a portal login automatically
- Updated teacher email also updates teacher login email

## MCQ Features

### Per-Student Question Order

MCQ question order is randomized per student in a stable way.

File:

- `app/Http/Controllers/Portal/StudentPortalController.php`

Method:

- `applyStudentQuestionOrder()`

How it works:

1. The system combines:
   `MCQ ID + Student ID + Question ID`
2. It creates a key like:
   `10-25-101`
3. It runs `crc32()` on that key
4. Each question gets its own hash value
5. Questions are sorted by those hash values

Result:

- Different students see different question order
- The same student keeps the same order for the same MCQ
- Result view matches the same order seen during the exam

### MCQ Scheduling

Teachers can set:

- Quiz start time
- Quiz duration in minutes
- Custom duration if needed

Behavior:

- Students cannot open the quiz before start time
- Students cannot submit after expiry
- Student take page timer shows readable time like:
  - `5 Min`
  - `4 Min 22 Sec`
  - `1 Hr 10 Min`

### MCQ Import

Teachers can import questions from:

- PDF
- DOCX
- XLSX
- XLS

Current behavior:

- Imports question text
- Imports answer options
- Ignores numbering like `1.` or `A)`
- Does not auto-select the correct answer
- Teacher still selects the correct answer manually before publishing

Main files:

- `app/Services/McqQuestionImporter.php`
- `app/Http/Controllers/Portal/TeacherPortalController.php`
- `resources/views/portal/teacher/mcq_create.blade.php`

### MCQ Navigation Panel

Student MCQ take page includes a CBT-style question navigation sidebar.

File:

- `resources/views/portal/student/mcq_take.blade.php`

Features:

- `Questions` title
- Compact circular question indicators
- Blue for current question
- Green for answered question
- Light/gray for unanswered question
- Tight spacing for large quizzes
- Hover animation
- Answered count

## Assignment Features

Teachers can:

- Post assignments
- Edit assignments
- Delete assignments
- View submissions
- Grade each submission

Students can:

- Download assignment files
- Submit assignments
- See submitted state
- See marks and grade after grading

## Marks

Teacher marks page:

- `/teacher/marks`

Student marks page:

- `/student/marks`

Both pages show:

- Assignment marks
- MCQ marks

Current grade rule:

- `A` for 75% and above
- `B` for 60% to 74%
- `C` for 40% to 59%
- `F` below 40%

## Notifications

The system uses `portal_notifications`.

Student notifications:

- New assignment published for their class and subject
- New MCQ published for their class and subject

Teacher notifications:

- Student assignment submission
- Student MCQ submission

Routing behavior:

- If a subject is selected, only students assigned to that subject receive the notification
- If no subject is selected, all students in the target class receive the notification

## Email Alerts

Email alerts are handled through Laravel Mail.

Current local mail setting:

- `MAIL_MAILER=log`

That means:

- Emails are written to `storage/logs/laravel.log`
- Emails are not sent to real inboxes until SMTP is configured

To send real emails, update `.env` for SMTP and clear config:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="CodeXpress Institute"
```

```bash
php artisan config:clear
```

## File Storage

Public uploads are stored in:

- `storage/app/public/assignments`
- `storage/app/public/assignment-submissions`
- `storage/app/public/course-content`

Create the storage link if needed:

```bash
php artisan storage:link
```

## Useful Commands

```bash
php artisan migrate
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan test
php artisan storage:link
```

## Notes

- Student, teacher, and subject create modals now auto-generate their codes
- Pending student approval creates portal-ready student accounts
- Website registration and admin approval are connected to the same real data flow
- Public website theme preference is stored in `localStorage`
