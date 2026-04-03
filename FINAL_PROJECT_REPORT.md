# ClassTrack Final Project Report

## 1. INTRODUCTION

### 1.1 Background

Digital education platforms need better tools to manage classes, monthly learning modules, and payment-based access to resources. In many tuition and class scenarios, content distribution, student tracking, and payment verification are handled manually or across disconnected systems.

The ClassTrack project was developed to provide a centralized web application where administrators can manage courses and monthly content, while students can access learning materials based on paid enrollment status.

### 1.2 Problem Statement

The primary problem addressed by this project is the lack of a centralized and role-based system for:

- Managing class modules month by month.
- Tracking student payment status for each month.
- Restricting educational content until payment is confirmed.
- Maintaining a simple dashboard for both administrators and students.

Without such a system, institutions and tutors face inefficiency, poor record keeping, and inconsistent access control for students.

### 1.3 Objectives

Primary objectives:

- Build a web-based system for managing courses and monthly modules.
- Implement role-based access for Admin and Student users.
- Enable payment-status-based content unlocking.
- Provide an organized way to upload and access resources (recordings/documents).

Secondary objectives:

- Keep the user interface clean and easy to navigate.
- Support future feature growth (notifications, stronger security, payment gateway integration).

### 1.4 Scope and Limitations

Scope of the project:

- Full-stack implementation using PHP, MySQL, HTML, and CSS.
- Admin functionalities: user management (create, edit, delete), course management, month management, enrollment marking, resource upload, live link updates.
- Student functionalities: view courses/modules, access paid content, join live classes, update profile.

Limitations of the current version:

- No online payment gateway integration (payment is manually marked by admin).
- Password handling is basic and can be improved with stronger security practices.
- Limited advanced analytics/reporting features.
- Mobile responsiveness exists at a basic level but can be optimized further.

## 2. REQUIREMENTS SPECIFICATION

### 2.1 Functional Requirements

The system shall:

- Allow users to log in using email and password.
- Redirect users to role-specific dashboards (admin or student).
- Allow admin to create, edit, and delete users.
- Allow admin to create and manage courses.
- Allow admin to create monthly modules for each course.
- Allow admin to enroll students per month and mark payment status as paid.
- Allow admin to upload month-based resources (recording/document links).
- Allow admin to update live class links for each month.
- Allow students to view available courses and monthly modules.
- Allow students to access month content only when payment status is paid.
- Allow students to update profile details.
- Maintain all data in a relational database with proper table relationships.

### 2.2 Non-Functional Requirements

Performance:

- The system should load standard pages within a few seconds under normal local-host conditions.
- Database queries should return expected records with minimal delay for classroom-scale usage.

Usability:

- Interfaces should be simple, readable, and role-focused.
- Navigation should remain consistent across admin and student pages.
- Forms should provide straightforward input structure for quick operation.

Security:

- Session-based access control should block unauthorized page access.
- Role checks should prevent students from entering admin pages.
- Database operations should use prepared statements for most sensitive operations.
- Input handling and output escaping should reduce injection/XSS risks.

Responsiveness:

- Layout should remain usable across desktop, tablet, and mobile viewports.
- Core UI components (cards, buttons, forms) should adapt without breaking structure.

## 3. SYSTEM DESIGN

### 3.1 Design Philosophy

ClassTrack follows a user-centered and role-driven design approach:

- Admin workflows are optimized for management tasks and quick updates.
- Student workflows are optimized for learning access and clarity.
- The interface uses a reusable visual style (buttons, cards, forms, spacing) for consistency.
- The architecture separates responsibilities by module (auth, admin, student, shared database layer).

### 3.2 Use Case Diagram

Actors:

- Admin
- Student

Main use cases:

- Login/Logout
- Manage users (Admin)
- Manage courses (Admin)
- Manage months (Admin)
- Enroll and mark payments (Admin)
- Upload resources (Admin)
- Update live class links (Admin)
- View courses/modules (Student)
- Access paid month resources (Student)
- Update profile (Student)

Diagram placeholder:

[Insert Use Case Diagram Screenshot Here]

### 3.3 Activity Diagram

Selected flow: Student accesses a monthly module.

1. Student logs in.
2. Student selects a course.
3. Student selects a month.
4. System checks enrollment and payment status.
5. If paid, system loads live link and resources.
6. If not paid, system denies access.

Diagram placeholder:

[Insert Activity Diagram Screenshot Here]

### 3.4 Entity-Relationship (ER) Diagram

Database entities:

- users
- courses
- course_months
- enrollments
- resources

Key relationships:

- One admin can create many courses.
- One course can contain many monthly modules.
- One student can have many month-level enrollments.
- One month can contain many resources.

Diagram placeholder:

[Insert ER Diagram Screenshot Here]

## 4. WEBSITE DESIGN & INTERFACE

### 4.1 User Interface Screenshots

Home page screenshot placeholder:

[Insert Home Page Screenshot Here]

Resources-related page screenshot placeholder (example: student month view or admin month resource panel):

[Insert Resources Page Screenshot Here]

Contact/communication-equivalent screenshot placeholder (example: profile/help or a selected communication page in your implementation):

[Insert Contact/Equivalent Page Screenshot Here]

### 4.2 Color Scheme and Typography

Primary and secondary colors used in the stylesheet:

- Primary: #4f46e5
- Primary Hover: #4338ca
- Secondary (muted text): #64748b
- Background: #f8fafc
- Card Background: #ffffff
- Main Text: #1e293b
- Success: #10b981
- Danger: #ef4444

Typography:

- Main font family: Inter (Google Fonts), fallback sans-serif.

Currency display standard:

- Fees are displayed in Sri Lankan Rupees (LKR) in both admin and student module views.

### 4.3 Responsive Design

Responsive behavior implemented:

- Viewport metadata is used in page layouts.
- Flexible containers, cards, and grid sections are used for adaptation.
- Form controls and buttons use percentage/full-width patterns in key views.

Current status note:

- The interface is usable on mobile and tablet, but additional media-query tuning can further improve small-screen spacing and column stacking behavior.

## 5. IMPLEMENTATION DETAILS

### 5.1 Technologies Used

Frontend:

- HTML5
- CSS3
- Font Awesome (icon library)

Backend:

- PHP (session handling, role logic, CRUD operations)

Database:

- MySQL (schema and relational constraints)
- PDO for database connection and query execution

Development tools:

- Visual Studio Code
- Browser developer tools (for layout/debug checks)

### 5.2 Technical Functionalities

Core implementation details include:

- Session-based authentication and role verification on protected pages.
- Centralized database connection via PDO.
- Prepared statements for major create/read/update operations.
- Admin user CRUD management (create, edit, delete) from a single user-management page.
- Admin safety controls that prevent deleting the current logged-in user and prevent deleting/demoting the last admin account.
- Enrollment logic that ties access rights to payment status.
- Month-based resource model separating recordings and documents.
- Admin controls for live class links and resource management.
- Student-side content access checks with direct denial for unpaid users.
- Currency labels standardized to LKR for monthly fee presentation.
- Font Awesome icons integrated for action clarity (edit/delete controls).
- Structured UI components for consistent visual behavior.

## 6. TESTING AND EVALUATION

Testing performed:

- Authentication testing:
  - Valid and invalid login attempts.
  - Role-based redirect checks.

- Authorization testing:
  - Admin-only page blocking for student sessions.
  - Student-only page blocking for admin sessions.

- Functional testing:
  - Create users, courses, and months.
  - Edit existing users and validate updated values.
  - Delete non-critical users and verify removal from list.
  - Verify safeguards for protected cases (cannot delete self, cannot remove last admin).
  - Enroll student and mark payment as paid.
  - Add resources and verify visibility in student month view.
  - Update student profile details.
  - Verify monthly fee labels display as LKR in admin and student pages.

- Access-control testing:
  - Verify unpaid students cannot open month content.
  - Verify paid students can access resources and live links.

- UI/usability testing:
  - Navigation clarity and form usability.
  - Basic checks across desktop and smaller viewports.

Evaluation summary:

- The system meets the primary academic requirements for class/month management and payment-gated content access. Additional refinement in security hardening and advanced responsive optimization is recommended for production-level deployment.

## 7. CONCLUSION

### 7.1 Achievements

Successfully delivered outcomes:

- Built a functional full-stack web application.
- Implemented complete Admin and Student modules.
- Designed and integrated a relational database for class tracking.
- Implemented full admin user lifecycle management (create, edit, delete) with account-protection safeguards.
- Enforced payment-based month access control.
- Enabled resource and live-link management by month.
- Standardized fee presentation using LKR currency labels across user-facing fee views.

### 7.2 Challenges Faced

Technical and implementation challenges included:

- Designing a clear month-level data relationship for enrollments/resources.
- Maintaining consistent role checks across multiple pages.
- Balancing simple UI design with functional completeness.
- Ensuring reliable access-control logic for paid/unpaid paths.

### 7.3 Future Enhancements

Potential future improvements:

- Integrate secure password hashing and stronger validation.
- Add online payment gateway support and automated payment verification.
- Add notifications (email/SMS/in-app) for new resources and class links.
- Improve mobile responsiveness with expanded media-query rules.
- Add analytics/reporting dashboards for admin insights.
- Add audit logs and activity history.

## 8. REFERENCES

Technical documentation and learning resources:

- MDN Web Docs (HTML, CSS, JavaScript, HTTP concepts).
- PHP Manual (sessions, PDO, form handling).
- MySQL Documentation (schema design, constraints, SQL syntax).
- W3Schools (quick syntax reference and examples).

Design and assets:

- Google Fonts (Inter font family).
- Font Awesome documentation and icon library.

Academic/curriculum resources:

- Module lecture notes and practical guidelines provided during the course.
- Project proposal and internal course documentation used during planning.
