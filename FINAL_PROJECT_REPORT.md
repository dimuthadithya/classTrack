# ClassTrack Final Project Report

## 1. Project Overview

ClassTrack is a web-based learning management and tuition tracking system built for campus project use. The system supports two user roles:

- Admin: manages users, classes, monthly modules, paid enrollments, resources, and live class links.
- Student: browses classes, views monthly modules, unlocks paid content, joins live classes, and updates profile details.

The project combines backend PHP pages with MySQL data storage and a shared CSS design system. It also includes a static HTML prototype set used for UI mock/demo screens.

## 2. Project Goals

- Provide a simple class and monthly module management platform.
- Track student payment status per month.
- Restrict content access based on payment (paid vs locked).
- Allow admins to upload study resources and recordings.
- Give students a straightforward dashboard for learning access.

## 3. Technology Stack

- Backend: PHP (procedural pages)
- Database: MySQL (via PDO)
- Frontend: HTML + CSS + minimal inline JS
- Icons: Font Awesome CDN
- Runtime environment (expected): Apache + PHP + MySQL (for example XAMPP)

## 4. High-Level Architecture

The system is structured by role-specific modules:

- Public/Auth pages in project root:
  - index.php (landing page)
  - login.php (authentication)
  - logout.php (session termination)
- Admin module:
  - admin/dashboard.php
  - admin/users.php
  - admin/courses.php
  - admin/view_course.php
  - admin/view_month.php
- Student module:
  - student/dashboard.php
  - student/course.php
  - student/month.php
  - student/profile.php
- Shared database connection:
  - includes/db.php
- Database definition:
  - database.sql
- Shared styling:
  - css/style.css
- Static prototype screens:
  - html/...

## 5. Database Analysis

### 5.1 Database Name

- class_track

### 5.2 Tables and Purpose

1. users

- Stores all system users (admin and student).
- Key fields: email (unique), password, role, full_name.

2. courses

- Represents classes/grades (for example Grade 10 Science).
- Linked to creator admin through created_by.

3. course_months

- Represents monthly units/modules within a class.
- Stores month name, year, live class link, and monthly fee.

4. enrollments

- Links students to specific monthly modules.
- Includes payment_status (pending/paid).

5. resources

- Stores month-level educational content.
- Resource type supports recording or document.

### 5.3 Relationship Summary

- users (admin) -> courses (created_by)
- courses -> course_months
- users (student) + course_months -> enrollments
- course_months -> resources

This creates a tuition-model structure where payment and access are tracked at month level rather than whole course level.

## 6. Functional Analysis (A-Z System Flow)

### A. Landing and Login

- index.php presents a marketing-style entry page and login CTA.
- login.php validates credentials and starts role-based session.
- After login:
  - admin -> admin/dashboard.php
  - student -> student/dashboard.php

### B. Session and Role Control

Every protected admin/student page checks:

- session user existence
- correct role value

Unauthorized users are redirected to login.

### C. Admin Workflow

1. Dashboard

- Shows counts of students, courses, and enrollments.

2. User Management

- Admin can add users with role selection (student/admin).
- Lists all users.

3. Class Management

- Admin creates classes/grades.
- Admin enters class detail page to manage months.

4. Month Management

- Add month per class with year, fee, and optional live link.

5. Month Operations

- Enroll student and mark payment as paid.
- Add resources (recordings/documents) per month.
- Update live link per month.
- View list of paid students and resources.

### D. Student Workflow

1. Student Dashboard

- Lists all available classes.

2. Class View

- Shows all months for selected class.
- Displays lock/unlock state by payment status.

3. Month View

- Access allowed only for paid enrollment.
- Student can:
  - join live class if live link exists
  - watch recordings
  - download/view documents

4. Profile

- Student can update name and optionally password.
- Email is displayed but not editable.

## 7. UI/UX and Frontend Structure

- Single shared style file (css/style.css).
- Consistent layout blocks:
  - sticky header
  - card-based content areas
  - utility classes (container, spacing)
- Static HTML mirror exists under html folder:
  - used as prototype/demo version of PHP pages
  - includes simulated data and demo links

## 8. Setup and Execution Guide

### 8.1 Prerequisites

- PHP runtime
- MySQL server
- Apache web server
- Browser

### 8.2 Database Setup

1. Execute database.sql to create schema.
2. Ensure credentials in includes/db.php match local environment.

### 8.3 Run Application

1. Place project in web server root (for example htdocs).
2. Start Apache and MySQL.
3. Open index.php or login.php in browser.
4. Login with a user account stored in users table.

## 9. Strengths

- Clear separation of admin and student responsibilities.
- Simple and understandable database model for tuition logic.
- Payment-gated content flow implemented.
- Modular file organization by role.
- Basic responsiveness and clean UI consistency.

## 10. Conclusion

ClassTrack delivers the core objectives of a campus-level tuition and class management platform with role-based access, monthly payment-gated modules, and resource distribution. The architecture is clear and functionally complete for an academic project demonstration.

---

Report prepared from full repository analysis on 2026-04-02.
