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

## Product Website and Setup Layer

The project includes a separate product information website and an admin setup layer for configuring the system for different education organizations.

This does not replace the current CodeXpress Institute website or portals.

Routes:

- `/edusystem-cloud`
- `/organization-settings`

Purpose:

- `/` remains the CodeXpress Institute website
- `/edusystem-cloud` is the product information website for EduSystem Cloud
- `/organization-settings` is the admin setup panel for organization branding, portals, dashboards, modules, widgets, and workflows

### SaaS Product Website

The product website explains:

- What the system is
- How the system works
- Which organizations can use it
- Student, teacher, and admin portal value
- Student registration and approval flow
- Assignment, MCQ, marks, notification, and reporting workflows
- Organization setup and system configuration options
- Pricing plans in LKR

Pricing plans currently shown:

- Free Trial: `LKR 0` for 14 days
- Plus: `LKR 12,500` per month
- Pro: `LKR 28,500` per month

Plan positioning:

- Free Trial is for demo and testing
- Plus is for active small or medium institutes
- Pro is for larger or highly customized organizations

Current SaaS foundation:

- Organization profiles now support plan metadata
- CodeXpress Institute is treated as a Pro organization
- Plan fields include plan name, status, student limit, trial end date, and subscription end date
- Admin top bar shows the active subscription plan
- Admin dashboard shows an organization subscription summary
- Full multi-tenancy is the next architecture step

Multi-tenancy means one Laravel application and one database can serve many organizations, while every organization only sees its own students, teachers, subjects, assignments, MCQs, marks, notifications, and settings.

To make this fully production-ready, every tenant-owned table should receive an `organization_profile_id`, and all admin, teacher, student, report, notification, import, and export queries should be scoped through the active organization.

### Organization Customization Panel

Admin route:

- `/organization-settings`

This page stores organization-level setup settings in the `organization_profiles` table.

Organization admins can configure:

- Organization name
- Logo
- Logo upload requires exactly `512 x 512 px`
- Primary color
- Secondary color
- Accent color
- Light, dark, or system theme mode
- Contact email
- Contact phone
- Address
- Enabled modules
- Dashboard widgets
- Workflow controls
- Portal section visibility planning

Setup controls are organized into clear groups:

- System Modules: Academic Records, Teaching Workflow, Communication and Operations
- Dashboard Widgets: Core Dashboard Cards, Activity and Alerts
- Workflow Controls: Automatic Numbering, Access Rules, Assessment Rules, Notifications
- Portal Sections: Admin Portal, Teacher Portal, Student Portal

Current applied behavior:

- Logo and organization name appear in the admin sidebar
- Logo and organization name appear in teacher and student portal sidebars
- Contact email and address appear in the admin topbar
- Contact phone appears under the organization name in the admin sidebar
- Enabled modules control admin sidebar visibility
- Dashboard widgets control visible admin dashboard cards/sections
- The setup preview updates live while editing organization name, theme, colors, contact details, modules, widgets, and workflow controls

Workflow controls include:

- Auto-generated student registration numbers
- Auto-generated teacher employee numbers
- Auto-generated subject codes
- Admin approval for website registrations
- Teacher subject access limits
- Student class access limits
- MCQ start-time enforcement
- MCQ expiry enforcement
- Assignment grade syncing into marks
- Email-ready notification workflow

Current table:

- `organization_profiles`

Current model:

- `app/Models/OrganizationProfile.php`

Current controllers:

- `app/Http/Controllers/SaasProductController.php`
- `app/Http/Controllers/OrganizationSettingsController.php`

Current views:

- `resources/views/saas/product.blade.php`
- `resources/views/organization_settings/edit.blade.php`

This is the foundation for turning the current institute system into a multi-organization SaaS product. The next step for a full commercial SaaS version would be tenant isolation, subscription billing, organization-specific domains, and applying saved organization settings dynamically across each organization's public website and dashboards.

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
- View class students with server-side pagination and search
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
- Supports Unicode text for Sinhala and Tamil PDFs when the PDF contains extractable text
- Uses Python PDF extraction first, then falls back to the PHP PDF parser
- Repairs common Sinhala/Tamil PDF extraction artifacts caused by broken glyph mapping

Main files:

- `app/Services/McqQuestionImporter.php`
- `app/Http/Controllers/Portal/TeacherPortalController.php`
- `resources/views/portal/teacher/mcq_create.blade.php`

Python helper packages:

- `pdfminer.six`
- `pypdf`

Install them with:

```bash
pip install -r requirements.txt
```

Sinhala/Tamil PDF import fix:

The old import path used only the PHP PDF parser. Some Sinhala and Tamil PDFs produced replacement characters like `�`, question marks inside words, or stray letters such as `J` and `0`.

The current importer:

1. Extracts text with Python `pdfminer.six`
2. Falls back to `pypdf` if needed
3. Falls back to `smalot/pdfparser` only if Python extraction gives no usable text
4. Forces UTF-8 output
5. Cleans common Sinhala and Tamil extraction artifacts line by line before filling the MCQ fields

Verified sample files:

- `CS_Quiz_Class_D_Sinhala.pdf`
- `CS_Quiz_Class_D_Tamil.pdf`

Both import 20 questions and keep Sinhala/Tamil text readable in the MCQ fields.

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

Send a test email after SMTP is configured:

```bash
php artisan mail:test student@example.com
```

If `MAIL_MAILER=log`, the system writes a warning to `storage/logs/laravel.log` explaining that the email was not delivered to a real inbox.

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
