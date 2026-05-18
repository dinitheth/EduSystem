# EduSystem - Laravel Education Portal

EduSystem is a Laravel-based school management and learning portal with separate Admin, Teacher, and Student experiences. It supports student/teacher/subject management, class-based course delivery, assignments, MCQ tests, marks, portal notifications, and email alerts.

## Tech Stack

- PHP 8.2+
- Laravel 12
- SQLite or MySQL
- Bootstrap 5
- Bootstrap Icons
- Laravel Excel / Maatwebsite Excel
- Laravel Mail
- Vite

## Main Portals

### Admin Portal

Admin routes are protected by a custom session login.

- Login: `/admin/login`
- Username: `Admin`
- Password: `Admin123`

Admin can:

- View the dashboard.
- Manage students.
- Manage teachers.
- Manage subjects.
- Import students, teachers, and subjects from CSV/Excel.
- Preview import data before saving.
- Export filtered students, teachers, and subjects.

### Teacher Portal

Teacher routes use custom session authentication through `teacher_logins`.

Teacher can:

- Log in with registered teacher email and password.
- View dashboard statistics.
- View students in their assigned class.
- View assigned subjects.
- Add course content by subject.
- Publish assignments for their class.
- Edit and delete posted assignments.
- Download assignment PDFs.
- View student assignment submissions.
- Grade assignment submissions with marks, max marks, and feedback.
- Download assignment submissions as ZIP.
- Export assignment submissions to Excel.
- Create MCQ tests for assigned subjects.
- View MCQ submissions and results.
- View both assignment marks and MCQ marks in Results & Marks.
- Receive bell notifications and email alerts when students submit assignments or MCQs.

### Student Portal

Student routes use custom session authentication through `student_logins`.

Student can:

- Log in with registered student email and password.
- View dashboard statistics.
- View enrolled subjects.
- View course content for enrolled subjects.
- View class assignments.
- Submit assignment notes and files.
- See assignment mark and grade after teacher grading.
- View available MCQ tests.
- Take MCQ tests before expiry.
- View completed MCQ results even after the test expires.
- View both assignment marks and MCQ marks in My Marks.
- Receive bell notifications and email alerts when teachers publish assignments or MCQs for their class and subject.

## Authentication

The application uses custom session authentication:

- Admin login is hardcoded in `AdminAuthController`.
- Student login uses `student_logins`.
- Teacher login uses `teacher_logins`.

To generate/reset all student and teacher portal passwords to `Abc123`, run:

```bash
php seed_logins.php
```

## Notifications

The system has database-backed portal notifications using the `portal_notifications` table.

Notification rules:

- When a teacher publishes an assignment, only students in the assignment class and selected subject receive it.
- When a teacher publishes an MCQ, only students in the MCQ class and selected subject receive it.
- If an assignment or MCQ has no subject selected, all students in that class receive it.
- When a student submits an assignment, the assignment teacher receives it.
- When a student submits an MCQ, the MCQ teacher receives it.

The bell icon appears in the Student and Teacher portal top bar. Unread notifications show a badge. Clicking a notification marks it as read and opens the related page.

## Email Alerts

Email notifications are sent through Laravel Mail.

Current `.env` default may be:

```env
MAIL_MAILER=log
```

With `MAIL_MAILER=log`, emails are not sent to inboxes. They are written to:

```text
storage/logs/laravel.log
```

To send real email, configure SMTP in `.env`, for example:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="EduSystem"
```

For Gmail, use a Google App Password, not your normal Gmail password.

After changing mail settings, run:

```bash
php artisan config:clear
```

## MCQ Question Randomization

MCQ questions are randomized per student using a deterministic hash-based order.

The system sorts each question using a value built from:

- MCQ ID
- Student ID
- Question ID

This means:

- Student A receives one stable question order.
- Student B receives a different stable question order.
- The same student sees the same order while taking the test and viewing the result.
- No database mutation is needed.

The implementation is in:

- `app/Http/Controllers/Portal/StudentPortalController.php`

Key method:

```php
private function applyStudentQuestionOrder(Mcq $mcq, Student $student): void
```

It loads the MCQ questions, sorts them by `crc32($mcq->id.'-'.$student->id.'-'.$question->id)`, then replaces the loaded Eloquent relation with that ordered collection.

## Marks

The marks system supports:

- MCQ marks from submitted MCQ tests.
- Assignment marks from teacher-graded assignment submissions.

Assignment marks are linked through `assignment_submission_id`, so they stay connected to the exact student submission.

Student marks page:

- `/student/marks`
- Shows both MCQ and assignment marks.

Teacher marks page:

- `/teacher/marks`
- Shows both MCQ and assignment marks for the teacher's own tests and assignments.

Grades:

- A: 75% and above
- B: 60% to 74%
- C: 40% to 59%
- F: below 40%

## Assignment Workflow

Teacher:

1. Publishes an assignment.
2. Students in the class and selected subject receive notification/email.
3. Teacher can edit or delete the assignment.
4. Teacher views submissions.
5. Teacher grades submissions.

Student:

1. Opens assignments page.
2. Downloads assignment PDF if available.
3. Submits notes and optional file.
4. Sees submitted status.
5. Sees mark and grade after teacher grading.

## MCQ Workflow

Teacher:

1. Creates an MCQ with questions and options.
2. Chooses the correct answer for each question.
3. Publishes the MCQ to their class and subject.
4. Students receive notification/email.
5. Teacher views submissions and results.

Student:

1. Opens MCQ page.
2. Starts available tests before expiry.
3. Receives randomized question order.
4. Submits answers.
5. Views result immediately.
6. Can still view completed results after expiry.

## File Storage

Uploaded files are stored on the public disk:

- Assignment PDFs: `storage/app/public/assignments`
- Student submissions: `storage/app/public/assignment-submissions`
- Course content PDFs/videos: `storage/app/public/course-content`

Create the public storage link if needed:

```bash
php artisan storage:link
```

## Useful Commands

Install dependencies:

```bash
composer install
npm install
```

Run migrations:

```bash
php artisan migrate
```

Run app locally:

```bash
php artisan serve
```

Build frontend assets:

```bash
npm run build
```

Run tests:

```bash
php artisan test
```

Reset portal login passwords:

```bash
php seed_logins.php
```

## Important Notes

- Admin auth is simple hardcoded session auth and should be replaced with database-backed admin users before production.
- Real email delivery requires SMTP configuration.
- If both student and teacher were previously logged in inside the same browser session, log out and log in again after the session-scoping fix so the notification bell shows the correct portal notifications.
