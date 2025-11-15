# Horizon Sentinel - Development Task List

This document serves as the central hub for tracking all development tasks for the Horizon Sentinel project. Each task is generated based on the PRD and detailed in accordance with the `generate-task.md` guide.

---

## Current Sprint/Focus: Initial Setup & Authentication

### 1. Project Setup
*   **Task ID:** HS-SETUP-001
*   **Description:** Initialize Laravel project and configure environment.
*   **Acceptance Criteria:** Laravel project created, `.env` file configured for basic app name/key.
*   **Status:** DONE
*   **Estimated Effort:** 0.5 hours

*   **Task ID:** HS-SETUP-002
*   **Description:** Create initial documentation files (`create-prd.md`, `generate-task.md`, `process-task-list.md`).
*   **Acceptance Criteria:** All three `.md` files exist in the project root.
*   **Status:** DONE
*   **Estimated Effort:** 0.25 hours

*   **Task ID:** HS-SETUP-003
*   **Description:** Populate `create-prd.md` with initial project requirements.
*   **Acceptance Criteria:** PRD document contains detailed sections on introduction, goals, problem, solution, features, non-functional requirements, tech stack, and success metrics.
*   **Status:** DONE (initial version)
*   **Estimated Effort:** 1 hour

### 2. Database & Environment Configuration
*   **Task ID:** HS-DB-001
*   **Description:** Configure database connection in `.env` and `config/database.php`.
*   **Acceptance Criteria:** Application can connect to a local MySQL database.
*   **Status:** PENDING
*   **Estimated Effort:** 0.5 hours

*   **Task ID:** HS-DB-002
*   **Description:** Run initial database migrations (`php artisan migrate`).
*   **Acceptance Criteria:** `users` table (and others from framework) exist in the database.
*   **Status:** PENDING
*   **Estimated Effort:** 0.25 hours

### 3. User Authentication
*   **Task ID:** HS-AUTH-001
*   **Description:** Install Laravel Breeze (or Jetstream) for authentication scaffolding.
*   **Acceptance Criteria:** Breeze installed, views published, `npm install && npm run dev` successful.
*   **Status:** PENDING
*   **Estimated Effort:** 1 hour

*   **Task ID:** HS-AUTH-002
*   **Description:** Implement user registration and login functionality.
*   **Acceptance Criteria:** Users can register an account, log in, and log out successfully.
*   **Status:** PENDING
*   **Estimated Effort:** 0.5 hours
*   **Dependencies:** HS-AUTH-001

*   **Task ID:** HS-AUTH-003
*   **Description:** Add 'role' column to the `users` table and update registration.
*   **Acceptance Criteria:** `users` table has a `role` column (e.g., 'employee', 'manager'), and new users can be assigned a default role.
*   **Status:** PENDING
*   **Estimated Effort:** 1 hour
*   **Dependencies:** HS-DB-002

---

## Next Up: Core Data Model & Employee Interface

### 4. Core Data Model - Database Design

*   **Task ID:** HS-DB-003
*   **Description:** Create migration for extending `users` table with role and manager relationship.
*   **Acceptance Criteria:**
    - Migration file created that adds `role` enum column (default: 'employee').
    - Migration adds `manager_id` foreign key column (nullable, references users.id).
    - Migration runs successfully without errors.
*   **Status:** PENDING
*   **Estimated Effort:** 1 hour
*   **Dependencies:** HS-DB-002

*   **Task ID:** HS-DB-004
*   **Description:** Create `leave_requests` table migration.
*   **Acceptance Criteria:**
    - Migration includes all required columns: id, user_id, manager_id, leave_type, start_date, end_date, status, employee_notes, manager_notes, submitted_at, reviewed_at, timestamps.
    - Proper foreign key constraints to users table.
    - Indexes on user_id, manager_id, and status for query performance.
    - Enum definitions for leave_type and status match PRD specifications.
*   **Status:** PENDING
*   **Estimated Effort:** 1.5 hours
*   **Dependencies:** HS-DB-003

*   **Task ID:** HS-DB-005
*   **Description:** Create `leave_request_history` table migration (optional for MVP, recommended).
*   **Acceptance Criteria:**
    - Migration includes: id, leave_request_id, action, performed_by_user_id, notes, created_at.
    - Foreign key constraints properly set.
    - Index on leave_request_id for efficient history queries.
*   **Status:** PENDING
*   **Estimated Effort:** 0.75 hours
*   **Dependencies:** HS-DB-004

*   **Task ID:** HS-BE-001
*   **Description:** Create User model relationships and role methods.
*   **Acceptance Criteria:**
    - User model has `manager()` belongsTo relationship.
    - User model has `directReports()` hasMany relationship.
    - User model has `leaveRequests()` hasMany relationship.
    - Helper methods: `isManager()`, `isEmployee()`, `getManagerAttribute()`.
*   **Status:** PENDING
*   **Estimated Effort:** 1 hour
*   **Dependencies:** HS-DB-003

*   **Task ID:** HS-BE-002
*   **Description:** Create LeaveRequest model with relationships and scopes.
*   **Acceptance Criteria:**
    - LeaveRequest model created with proper fillable/guarded properties.
    - Relationships: `user()` belongsTo, `manager()` belongsTo, `history()` hasMany.
    - Date casting for start_date, end_date, submitted_at, reviewed_at.
    - Enums/constants for leave_type and status values.
    - Query scopes: `pending()`, `approved()`, `denied()`, `forManager($managerId)`, `overlapping($startDate, $endDate)`.
*   **Status:** PENDING
*   **Estimated Effort:** 2 hours
*   **Dependencies:** HS-DB-004, HS-BE-001

*   **Task ID:** HS-BE-003
*   **Description:** Create LeaveRequestHistory model (if history table is implemented).
*   **Acceptance Criteria:**
    - LeaveRequestHistory model with relationships to LeaveRequest and User.
    - Protected attributes to prevent mass assignment issues.
    - Method to create history entries: `LeaveRequest::recordHistory($action, $userId, $notes)`.
*   **Status:** PENDING
*   **Estimated Effort:** 0.75 hours
*   **Dependencies:** HS-DB-005

*   **Task ID:** HS-BE-004
*   **Description:** Create database seeders for development/testing data.
*   **Acceptance Criteria:**
    - UserSeeder creates 2 managers, 1 HR admin, and 10 employees with proper relationships.
    - Passwords are hashed; default password documented in seeder comments.
    - LeaveRequestSeeder creates sample requests in various states (pending, approved, denied).
    - Seed data includes some overlapping leaves for conflict detection testing.
    - LeaveBalanceSeeder creates initial balances for all employees.
    - HolidaySeeder creates sample company holidays.
*   **Status:** PENDING
*   **Estimated Effort:** 2 hours
*   **Dependencies:** HS-BE-002, HS-BE-005, HS-BE-007

### 4B. Leave Balance Management System (NEW - CRITICAL FOR MVP)

*   **Task ID:** HS-BE-005
*   **Description:** Create leave_balances table migration.
*   **Acceptance Criteria:**
    - Migration includes columns: id, user_id, leave_type, total_allocated, used, pending, available, year, timestamps.
    - Unique constraint on (user_id, leave_type, year).
    - Foreign key to users table.
    - Indexes on user_id and year.
*   **Status:** PENDING
*   **Estimated Effort:** 1 hour
*   **Dependencies:** HS-DB-003

*   **Task ID:** HS-BE-006
*   **Description:** Create leave_balance_history table migration.
*   **Acceptance Criteria:**
    - Migration includes: id, leave_balance_id, change_amount, change_type, leave_request_id, performed_by_user_id, notes, created_at.
    - Foreign keys to leave_balances, leave_requests, users tables.
    - Index on leave_balance_id.
*   **Status:** PENDING
*   **Estimated Effort:** 0.75 hours
*   **Dependencies:** HS-BE-005, HS-DB-004

*   **Task ID:** HS-BE-007
*   **Description:** Create company_holidays table migration.
*   **Acceptance Criteria:**
    - Migration includes: id, name, date, is_recurring, region, timestamps.
    - Index on date for quick lookups.
*   **Status:** PENDING
*   **Estimated Effort:** 0.5 hours
*   **Dependencies:** HS-DB-002

*   **Task ID:** HS-BE-008
*   **Description:** Create LeaveBalance model with relationships and methods.
*   **Acceptance Criteria:**
    - LeaveBalance model with relationships: user(), balanceHistory(), pendingRequests().
    - Methods: updateAvailable(), deductBalance($amount), restoreBalance($amount).
    - Accessor for formatted balance display.
    - Scope: forYear($year), forUser($userId).
*   **Status:** PENDING
*   **Estimated Effort:** 2 hours
*   **Dependencies:** HS-BE-005

*   **Task ID:** HS-BE-009
*   **Description:** Create CompanyHoliday model with helper methods.
*   **Acceptance Criteria:**
    - CompanyHoliday model with date casting.
    - Static methods: getHolidaysInRange($startDate, $endDate), isHoliday($date).
    - Scope: upcoming(), forRegion($region).
*   **Status:** PENDING
*   **Estimated Effort:** 1 hour
*   **Dependencies:** HS-BE-007

*   **Task ID:** HS-BE-010
*   **Description:** Create LeaveBalanceService for business logic.
*   **Acceptance Criteria:**
    - Service class with methods:
      - calculateWorkingDays($startDate, $endDate, $excludeHolidays = true)
      - validateBalanceSufficiency($userId, $leaveType, $days)
      - deductFromBalance($leaveRequestId)
      - restoreToBalance($leaveRequestId)
      - getAvailableBalance($userId, $leaveType)
    - Service handles all balance calculations and holiday exclusions.
    - Unit tests with 80%+ coverage.
*   **Status:** PENDING
*   **Estimated Effort:** 3 hours
*   **Dependencies:** HS-BE-008, HS-BE-009

*   **Task ID:** HS-FE-020
*   **Description:** Add leave balance display to employee dashboard.
*   **Acceptance Criteria:**
    - Dashboard widget showing current balance by leave type.
    - Visual progress bars for balance consumption.
    - Link to detailed balance history page.
    - Pending requests impact shown separately.
*   **Status:** PENDING
*   **Estimated Effort:** 2 hours
*   **Dependencies:** HS-BE-008

*   **Task ID:** HS-FE-021
*   **Description:** Integrate balance validation into leave request form.
*   **Acceptance Criteria:**
    - Real-time balance check when dates are selected.
    - Warning message if insufficient balance.
    - Form submission blocked if balance exceeded.
    - Display "Available balance: X days" above submit button.
*   **Status:** PENDING
*   **Estimated Effort:** 2 hours
*   **Dependencies:** HS-BE-010, HS-FE-007

### 4C. Notification System (NEW - CRITICAL FOR MVP)

*   **Task ID:** HS-NOTIF-001
*   **Description:** Configure Laravel Mail settings and create email templates.
*   **Acceptance Criteria:**
    - Mail configuration in .env and config/mail.php.
    - Markdown email templates created for: leave_request_submitted, leave_request_approved, leave_request_denied, leave_request_cancelled.
    - Templates include all relevant request details and action buttons.
    - Templates use company branding/styling.
*   **Status:** PENDING
*   **Estimated Effort:** 2 hours
*   **Dependencies:** HS-SETUP-001

*   **Task ID:** HS-NOTIF-002
*   **Description:** Create notification classes using Laravel Notifications.
*   **Acceptance Criteria:**
    - Notification classes: LeaveRequestSubmitted, LeaveRequestApproved, LeaveRequestDenied, LeaveRequestCancelled.
    - Each notification supports mail and database channels.
    - Notification includes request details, links to view request.
*   **Status:** PENDING
*   **Estimated Effort:** 2 hours
*   **Dependencies:** HS-NOTIF-001

*   **Task ID:** HS-NOTIF-003
*   **Description:** Integrate notifications into LeaveRequest workflow.
*   **Acceptance Criteria:**
    - Send LeaveRequestSubmitted notification to manager when request is created.
    - Send LeaveRequestApproved notification to employee on approval.
    - Send LeaveRequestDenied notification to employee on denial.
    - Send LeaveRequestCancelled notification to manager when employee cancels.
    - Notifications dispatched via queued jobs.
*   **Status:** PENDING
*   **Estimated Effort:** 2 hours
*   **Dependencies:** HS-NOTIF-002, HS-FE-008

*   **Task ID:** HS-NOTIF-004
*   **Description:** Create in-app notification center UI.
*   **Acceptance Criteria:**
    - Notification bell icon in header showing unread count.
    - Dropdown showing recent notifications.
    - Mark as read functionality.
    - Link to full notifications page.
    - Real-time updates using polling (future: WebSockets).
*   **Status:** PENDING
*   **Estimated Effort:** 3 hours
*   **Dependencies:** HS-NOTIF-002

### 5. Employee Interface - Leave Request Management

*   **Task ID:** HS-FE-001
*   **Description:** Create LeaveRequestController with resource methods.
*   **Acceptance Criteria:**
    - Controller created with methods: index(), create(), store(), show(), edit(), update(), destroy().
    - Proper authorization using policies or middleware.
    - Route definitions in web.php for employee leave request routes.
*   **Status:** PENDING
*   **Estimated Effort:** 1 hour
*   **Dependencies:** HS-BE-002

*   **Task ID:** HS-FE-002
*   **Description:** Create LeaveRequestPolicy for authorization.
*   **Acceptance Criteria:**
    - Policy defines: view, viewAny, create, update, delete, cancel permissions.
    - Employees can only view/edit their own requests.
    - Only pending requests can be cancelled by employees.
    - Policy registered in AuthServiceProvider.
*   **Status:** PENDING
*   **Estimated Effort:** 1 hour
*   **Dependencies:** HS-FE-001

*   **Task ID:** HS-FE-003
*   **Description:** Create LeaveRequestFormRequest for validation.
*   **Acceptance Criteria:**
    - Validation rules for: leave_type, start_date, end_date, employee_notes.
    - Custom validation: start_date must be before or equal to end_date.
    - Custom validation: Check for overlapping employee requests.
    - Proper error messages for validation failures.
*   **Status:** PENDING
*   **Estimated Effort:** 1.5 hours
*   **Dependencies:** HS-BE-002

*   **Task ID:** HS-FE-004
*   **Description:** Implement LeaveRequestController@index - List employee's leave requests.
*   **Acceptance Criteria:**
    - Retrieves all leave requests for authenticated user.
    - Orders by submitted_at descending (newest first).
    - Includes pagination (15 per page).
    - Returns view with leave requests collection.
*   **Status:** PENDING
*   **Estimated Effort:** 0.75 hours
*   **Dependencies:** HS-FE-001

*   **Task ID:** HS-FE-005
*   **Description:** Create Blade view for employee leave request list (index).
*   **Acceptance Criteria:**
    - Uses app layout with navigation.
    - Displays table/list of leave requests with: dates, type, status, submission date.
    - Status badges color-coded (pending=yellow, approved=green, denied=red).
    - Link/button to create new request.
    - Action buttons: View details, Cancel (for pending only).
    - Pagination controls.
    - Styled with Tailwind CSS.
*   **Status:** PENDING
*   **Estimated Effort:** 2 hours
*   **Dependencies:** HS-FE-004

*   **Task ID:** HS-FE-006
*   **Description:** Implement LeaveRequestController@create - Show new request form.
*   **Acceptance Criteria:**
    - Returns view with empty form.
    - Passes leave type options to view.
*   **Status:** PENDING
*   **Estimated Effort:** 0.5 hours
*   **Dependencies:** HS-FE-001

*   **Task ID:** HS-FE-007
*   **Description:** Create Blade view for new leave request form (create).
*   **Acceptance Criteria:**
    - Form with fields: leave_type (dropdown), start_date (date picker), end_date (date picker), employee_notes (textarea).
    - Date inputs use HTML5 date type or compatible date picker.
    - CSRF protection included.
    - Client-side validation hints (HTML5 required attributes).
    - Submit and Cancel buttons.
    - Styled with Tailwind CSS.
*   **Status:** PENDING
*   **Estimated Effort:** 2 hours
*   **Dependencies:** HS-FE-006

*   **Task ID:** HS-FE-008
*   **Description:** Implement LeaveRequestController@store - Process new request submission.
*   **Acceptance Criteria:**
    - Validates request using LeaveRequestFormRequest.
    - Automatically sets user_id to authenticated user.
    - Automatically sets manager_id from user's manager relationship.
    - Sets submitted_at to current timestamp.
    - Sets status to 'pending'.
    - Creates history record if history tracking is implemented.
    - Redirects to index with success message.
    - Handles validation failures with error messages.
*   **Status:** PENDING
*   **Estimated Effort:** 1.5 hours
*   **Dependencies:** HS-FE-007, HS-FE-003

*   **Task ID:** HS-FE-009
*   **Description:** Implement LeaveRequestController@show - View single request details.
*   **Acceptance Criteria:**
    - Retrieves leave request by ID.
    - Authorizes using policy (employee can only view own requests).
    - Loads relationships: manager, history (if implemented).
    - Returns detail view with all request information.
*   **Status:** PENDING
*   **Estimated Effort:** 0.75 hours
*   **Dependencies:** HS-FE-002

*   **Task ID:** HS-FE-010
*   **Description:** Create Blade view for leave request details (show).
*   **Acceptance Criteria:**
    - Displays all request details: dates, type, status, notes.
    - Shows manager name and manager notes (if any).
    - Shows history timeline (if implemented).
    - Action buttons based on status: Cancel (if pending), Back to list.
    - Styled with Tailwind CSS.
*   **Status:** PENDING
*   **Estimated Effort:** 1.5 hours
*   **Dependencies:** HS-FE-009

*   **Task ID:** HS-FE-011
*   **Description:** Implement leave request cancellation (custom method or update).
*   **Acceptance Criteria:**
    - Route: PATCH /leave-requests/{id}/cancel.
    - Controller method: cancel($id).
    - Authorization: Only employee who created it, only if status is 'pending'.
    - Updates status to 'cancelled'.
    - Creates history record.
    - Redirects with success message.
*   **Status:** PENDING
*   **Estimated Effort:** 1 hour
*   **Dependencies:** HS-FE-002, HS-BE-002

*   **Task ID:** HS-FE-012
*   **Description:** Add navigation menu items for employee interface.
*   **Acceptance Criteria:**
    - App layout includes navigation link to "My Leave Requests".
    - Link to "Request Time Off" or "New Request".
    - Navigation highlights active page.
    - Role-based: Only show employee links to employees.
*   **Status:** PENDING
*   **Estimated Effort:** 0.5 hours
*   **Dependencies:** HS-FE-005

### 6. Manager Interface (EXPANDED)

*   **Task ID:** HS-MGR-001
*   **Description:** Create ManagerController for dashboard and pending requests.
*   **Acceptance Criteria:**
    - Controller with methods: dashboard(), pendingRequests(), approveRequest(), denyRequest().
    - Middleware to restrict access to managers only.
    - Routes defined in web.php.
*   **Status:** PENDING
*   **Estimated Effort:** 1.5 hours
*   **Dependencies:** HS-BE-002

*   **Task ID:** HS-MGR-002
*   **Description:** Implement manager dashboard view with key metrics.
*   **Acceptance Criteria:**
    - Shows count of pending requests requiring action.
    - Display upcoming leaves for next 30 days.
    - Quick stats: team size, currently on leave, leave requests this month.
    - Links to detailed views (pending requests, team calendar, team status).
    - Styled with cards/widgets using Tailwind.
*   **Status:** PENDING
*   **Estimated Effort:** 3 hours
*   **Dependencies:** HS-MGR-001, HS-BE-010

*   **Task ID:** HS-MGR-003
*   **Description:** Implement pending requests review page with detailed info.
*   **Acceptance Criteria:**
    - List of pending requests from direct reports.
    - Display: employee name, leave type, dates, duration, employee notes, current balance.
    - Action buttons: Approve, Deny (opens modal for comments).
    - Show conflict warnings inline if approval would cause issues.
    - Pagination and filtering by leave type/date range.
*   **Status:** PENDING
*   **Estimated Effort:** 3 hours
*   **Dependencies:** HS-MGR-001, HS-BE-010

*   **Task ID:** HS-MGR-004
*   **Description:** Create approve/deny modals with validation.
*   **Acceptance Criteria:**
    - Approve modal shows conflict warning if detected, requires confirmation.
    - Deny modal requires manager_notes (reason for denial).
    - AJAX submission with loading states.
    - Success/error notifications after action.
    - Updates balance and sends notification to employee.
*   **Status:** PENDING
*   **Estimated Effort:** 2.5 hours
*   **Dependencies:** HS-MGR-003, HS-NOTIF-003

*   **Task ID:** HS-MGR-005
*   **Description:** Create team calendar view component.
*   **Acceptance Criteria:**
    - Full month calendar view showing all direct reports' approved leaves.
    - Color-coded by leave type.
    - Hover tooltips showing employee name, leave type, notes.
    - Visual indicators for company holidays.
    - Conflict highlights (yellow/red) for overlapping leaves.
    - Navigation: previous/next month, jump to date.
*   **Status:** PENDING
*   **Estimated Effort:** 5 hours
*   **Dependencies:** HS-MGR-001, HS-BE-009

*   **Task ID:** HS-MGR-006
*   **Description:** Implement bulk approve/deny functionality.
*   **Acceptance Criteria:**
    - Checkboxes on pending requests list.
    - "Bulk Approve" and "Bulk Deny" buttons.
    - Validation: all selected requests checked for conflicts together.
    - Warning if bulk approval would cause conflicts.
    - Progress indicator for batch processing.
*   **Status:** PENDING
*   **Estimated Effort:** 3 hours
*   **Dependencies:** HS-MGR-003

*   **Task ID:** HS-MGR-007
*   **Description:** Create team analytics page showing leave patterns.
*   **Acceptance Criteria:**
    - Charts: leave usage by employee, leave type distribution, monthly trends.
    - Metrics: average leave per employee, most popular leave months.
    - Absence rate calculation.
    - Downloadable as PDF report.
*   **Status:** PENDING (Post-MVP v1.1)
*   **Estimated Effort:** 4 hours
*   **Dependencies:** HS-MGR-002

*   **Task ID:** HS-MGR-008
*   **Description:** Implement manager delegation system.
*   **Acceptance Criteria:**
    - Create manager_delegations table migration.
    - UI to assign backup approver with date range.
    - Auto-routing of new requests to delegate during delegation period.
    - Notification to delegate when they receive delegated approvals.
*   **Status:** PENDING (Post-MVP v1.1)
*   **Estimated Effort:** 4 hours
*   **Dependencies:** HS-MGR-001

*   **Task ID:** HS-MGR-009
*   **Description:** Add calendar export functionality (PDF, CSV, iCal).
*   **Acceptance Criteria:**
    - Export buttons on team calendar page.
    - PDF: formatted calendar view with legend.
    - CSV: list of leaves with columns (employee, type, start, end, status).
    - iCal: importable to Google Calendar/Outlook.
*   **Status:** PENDING (Post-MVP v1.1)
*   **Estimated Effort:** 3 hours
*   **Dependencies:** HS-MGR-005

### 7. HR Administrator Interface (NEW - POST-MVP v1.1)

*   **Task ID:** HS-HR-001
*   **Description:** Create HRAdminController for company-wide operations.
*   **Acceptance Criteria:**
    - Controller methods: dashboard(), allLeaveRequests(), manageBalances(), managePolicies(), manageHolidays().
    - Middleware restricting access to hr_admin role.
    - Routes defined with /hr-admin prefix.
*   **Status:** PENDING (Post-MVP v1.1)
*   **Estimated Effort:** 2 hours
*   **Dependencies:** HS-AUTH-003

*   **Task ID:** HS-HR-002
*   **Description:** Create HR admin dashboard with company-wide metrics.
*   **Acceptance Criteria:**
    - Shows total pending requests across company.
    - Department-wise leave usage stats.
    - Policy compliance alerts (negative balances, overdue requests).
    - Quick links to manage holidays, policies, balances.
*   **Status:** PENDING (Post-MVP v1.1)
*   **Estimated Effort:** 3 hours
*   **Dependencies:** HS-HR-001

*   **Task ID:** HS-HR-003
*   **Description:** Implement company-wide leave request search and filter.
*   **Acceptance Criteria:**
    - Search by employee name, department, date range, status.
    - Advanced filters: leave type, manager, balance warnings.
    - Results table with sorting and pagination.
    - Ability to view any request details.
    - Override approval/denial buttons with justification modal.
*   **Status:** PENDING (Post-MVP v1.1)
*   **Estimated Effort:** 4 hours
*   **Dependencies:** HS-HR-001

*   **Task ID:** HS-HR-004
*   **Description:** Create leave balance management interface.
*   **Acceptance Criteria:**
    - List all employees with current balances by leave type.
    - "Adjust Balance" button opens modal for manual adjustment.
    - Adjustment requires: amount (±), reason, creates audit trail.
    - Bulk import balances via CSV upload.
    - Bulk export current balances to CSV.
*   **Status:** PENDING (Post-MVP v1.1)
*   **Estimated Effort:** 5 hours
*   **Dependencies:** HS-HR-001, HS-BE-010

*   **Task ID:** HS-HR-005
*   **Description:** Create holiday calendar management interface.
*   **Acceptance Criteria:**
    - List view of all company holidays.
    - Add/edit/delete holiday functionality.
    - Fields: name, date, is_recurring, region.
    - Bulk import holidays from CSV (year's calendar).
    - Preview showing which employees are affected by region.
*   **Status:** PENDING (Post-MVP v1.1)
*   **Estimated Effort:** 3 hours
*   **Dependencies:** HS-HR-001, HS-BE-009

*   **Task ID:** HS-HR-006
*   **Description:** Create policy configuration interface.
*   **Acceptance Criteria:**
    - Create leave_policies table migration.
    - UI to manage: blackout periods, minimum notice days, max consecutive days.
    - Policy builder with JSON configuration storage.
    - Test policy against existing requests feature.
    - Activation/deactivation toggle for policies.
*   **Status:** PENDING (Post-MVP v1.1)
*   **Estimated Effort:** 6 hours
*   **Dependencies:** HS-HR-001

*   **Task ID:** HS-HR-007
*   **Description:** Create compliance reporting module.
*   **Acceptance Criteria:**
    - Report types: leave usage by department, policy violations, balance audit.
    - Date range selection and department filters.
    - Export to PDF, Excel, CSV.
    - Charts and visualizations for executive summary.
    - Scheduled reports (email weekly/monthly summaries).
*   **Status:** PENDING (Post-MVP v1.2)
*   **Estimated Effort:** 8 hours
*   **Dependencies:** HS-HR-002

---

## Completed Tasks

*(Once tasks are completed, they will be moved here for historical tracking, or their status will be updated to 'DONE' in the active list.)*