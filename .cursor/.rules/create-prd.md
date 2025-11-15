# Product Requirements Document (PRD) - Horizon Sentinel

## 1. Introduction
Horizon Sentinel is a digital leave request and conflict avoidance system designed to formalize and centralize time off management for Horizon Dynamics. This system aims to eliminate the current manual process, which leads to staffing conflicts and operational bottlenecks due to disorganized time off management.

## 2. Goals & Objectives
*   **Primary Goal:** To provide a single, easy-to-use platform for managing employee time off requests and approvals.
*   **Key Objectives:**
    *   Formalize the leave request submission process, ensuring all requests are digitally recorded.
    *   Provide real-time visibility into scheduled absences across teams.
    *   Reduce staffing conflicts by enabling managers to make informed approval decisions.
    *   Streamline communication between employees and managers regarding leave status.

## 3. Target Audience
*   **Employees of Horizon Dynamics:** Users who need to submit and track their leave requests.
*   **Managers of Horizon Dynamics:** Users who need to review, approve/deny leave requests, and monitor team availability.

## 4. Problem Statement
Horizon Dynamics currently suffers from disorganized time off management characterized by:
*   **Submission Chaos:** Informal email/note-based requests are easily overlooked.
*   **Scheduling Conflicts:** Managers approve requests in isolation, leading to critical understaffing or overlapping key personnel absences due to inconsistent calendar updates.
*   **Lack of Central Visibility:** No single, comprehensive view of company-wide absences, hindering cross-departmental planning.

## 5. Solution Overview - Horizon Sentinel
Horizon Sentinel will be a concise, digital Information System built using Laravel and Tailwind CSS. It will function as a simple Digital Leave Request and Conflict Avoidance System by handling the routing and scheduling view, eliminating the need for email tracking.

## 6. Key Features

### 6.1. Employee Interface
*   **Leave Request Submission:**
    *   Allows employees to submit new leave requests with a specified date range.
    *   Option to select the type of leave (e.g., Paid Time Off, Unpaid Leave, Sick Leave, Vacation).
    *   Real-time validation against available leave balance.
    *   Display remaining balance before submission.
    *   Optional document attachment support (medical certificates, supporting docs).
*   **Request Status Tracking:**
    *   Employees can view the current status of their submitted requests (e.g., Pending, Approved, Denied).
    *   Email notifications for status changes (approved, denied, cancelled).
    *   In-app notification center.
*   **Leave Balance Dashboard:**
    *   View current balance by leave type (PTO, Sick, Vacation).
    *   View accrual history and upcoming accruals.
    *   See projected balance for future dates.
*   **Request Management:**
    *   Edit pending requests before manager review.
    *   Cancel pending or approved requests (with manager notification).

### 6.2. Manager Interface
*   **Pending Request Review:**
    *   View a list of all pending leave requests from their direct reports.
    *   Ability to approve or deny requests with mandatory comments for denials.
    *   Bulk approve/deny multiple requests.
    *   Email notifications for new pending requests.
*   **Team Availability Calendar:**
    *   Crucial calendar overlay showing existing approved time off for their team *before* making an approval decision.
    *   Highlighting potential conflicts or critical staffing levels.
    *   Visual indicators for company holidays.
    *   Export team calendar to PDF/CSV.
*   **Team Analytics:**
    *   Dashboard showing upcoming leaves, team coverage metrics.
    *   Historical leave patterns and trends.
    *   Absence rate calculations.
*   **Delegation System:**
    *   Assign backup approver when manager is on leave.
    *   Temporary delegation of approval authority.

### 6.3. HR Administrator Interface
*   **Company-Wide Visibility:**
    *   View all leave requests across all departments.
    *   Search and filter by employee, department, date range, status.
*   **Leave Balance Management:**
    *   Configure leave entitlements by employee or role.
    *   Manually adjust balances (with audit trail).
    *   Bulk import/export balance data.
    *   Set accrual rules (monthly, yearly, pro-rata).
*   **Policy Configuration:**
    *   Define company holidays (with regional variations).
    *   Set blackout periods (dates when leave is restricted).
    *   Configure minimum notice periods by leave type.
    *   Set maximum consecutive days allowed.
*   **Exception Handling:**
    *   Override approvals or denials with justification.
    *   Handle retroactive leave requests.
    *   Manage negative balance approvals.
*   **Reporting & Analytics:**
    *   Generate compliance reports for audits.
    *   Department comparison analytics.
    *   Leave trend analysis and forecasting.
    *   Export reports in multiple formats.

### 6.4. System Functionality (Core)
*   **Digital Record Keeping:** Ensures a digital, timestamped, and visible record of all intended absences.
*   **Request Routing:** Automated routing of requests to the appropriate manager (with delegation support).
*   **Conflict Detection (Manager View):** Visual cues in the calendar to alert managers of potential understaffing or critical overlaps.
*   **Notification System:** Email and in-app notifications for all state changes, escalations, and reminders.
*   **Holiday Calendar Integration:** Automatic exclusion of company holidays from leave calculations.
*   **Audit Trail:** Comprehensive logging of all actions (submissions, approvals, modifications, overrides).

### 6.5. Conflict Detection Logic (Detailed)
The conflict detection system should identify and warn managers about:

*   **Overlapping Leave Requests:**
    *   When multiple team members request leave on the same dates.
    *   Threshold: Alert when more than X% of team (configurable, default 30%) is scheduled off simultaneously.
*   **Critical Role Coverage:**
    *   Flag when employees with critical roles/skills are scheduled off together.
    *   Initially implemented as simple overlap detection; future: role-based rules.
*   **Sequential Leave Patterns:**
    *   Highlight potential coverage gaps where approved leaves occur back-to-back, leaving minimal coverage.
*   **Visual Indicators:**
    *   Calendar color-coding: Green (safe), Yellow (approaching threshold), Red (critical conflict).
    *   Tooltip/popover showing which team members are affected.
*   **Warning on Approval:**
    *   When a manager attempts to approve a request that would create a conflict, display a modal warning with:
        - List of other approved/pending leaves during that period.
        - Calculated team availability percentage.
        - Option to proceed with approval or deny with reason.

### 6.6. User Stories

**As an Employee:**
*   I want to submit a leave request for specific dates so that my manager knows when I'll be absent.
*   I want to see my remaining leave balance before submitting a request so I know if I have enough days available.
*   I want to see all my past, pending, and approved leave requests in one place.
*   I want to receive email notifications when my leave request is approved or denied.
*   I want to optionally add notes/reasons to my leave request.
*   I want to be able to cancel a pending leave request before it's approved.
*   I want to edit a pending leave request if I made a mistake before my manager reviews it.
*   I want to attach supporting documents (like medical certificates) to sick leave requests.
*   I want to see company holidays on the calendar so I don't request leave on those dates.
*   I want to see a projected balance showing what my balance will be after approved/pending requests.

**As a Manager:**
*   I want to see all pending leave requests from my direct reports so I can review them promptly.
*   I want to receive email notifications when a new leave request is submitted.
*   I want to view my team's calendar showing all approved leaves before approving new requests.
*   I want to see company holidays displayed on the team calendar.
*   I want to be warned when approving a leave would create a staffing conflict.
*   I want to add comments/reasons when denying a leave request so employees understand the decision.
*   I want to see a dashboard summarizing my team's leave status (upcoming leaves, pending requests).
*   I want to bulk approve multiple non-conflicting requests at once to save time.
*   I want to delegate my approval authority to another manager when I'm on leave.
*   I want to see historical leave patterns for my team to identify trends or potential abuse.
*   I want to export my team's leave calendar to share with stakeholders.
*   I want to see each employee's remaining leave balance when reviewing their request.

**As an HR Administrator:**
*   I want to view all leave requests across the entire company so I can monitor trends.
*   I want to configure company holidays and blackout periods so the system enforces policies automatically.
*   I want to set leave entitlements for employees based on their role or tenure.
*   I want to manually adjust an employee's leave balance when there are exceptions (with audit trail).
*   I want to override a manager's approval/denial decision in exceptional circumstances.
*   I want to generate compliance reports showing leave usage by department for audits.
*   I want to set accrual rules so leave balances update automatically each month.
*   I want to configure minimum notice periods for different leave types.
*   I want to handle retroactive leave requests (e.g., emergency sick leave).
*   I want to export all leave data for integration with payroll systems.

**As the System:**
*   The system must route leave requests to the correct manager based on employee-manager relationships.
*   The system must prevent unauthorized users from viewing other teams' leave data.
*   The system must maintain an audit trail of all leave request actions (submitted, approved, denied, cancelled).
*   The system must validate leave requests against available balance before allowing submission.
*   The system must automatically exclude company holidays from leave day calculations.
*   The system must send email notifications for all state changes (new request, approval, denial, cancellation).
*   The system must enforce configured policies (minimum notice, blackout periods, maximum consecutive days).
*   The system must route requests to backup approver when primary manager is unavailable.

## 7. Data Model Overview

### 7.1. Core Entities

**Users Table (Extended):**
*   id (primary key)
*   name
*   email
*   password
*   role (enum: 'employee', 'manager', 'hr_admin')
*   manager_id (foreign key to users.id, nullable for managers)
*   department (string, nullable)
*   created_at, updated_at

**Leave Requests Table:**
*   id (primary key)
*   user_id (foreign key to users.id) - Employee who submitted
*   manager_id (foreign key to users.id) - Manager who needs to approve
*   leave_type (enum: 'paid_time_off', 'unpaid_leave', 'sick_leave', 'vacation')
*   start_date (date)
*   end_date (date)
*   total_days (integer) - Calculated, excluding holidays
*   status (enum: 'pending', 'approved', 'denied', 'cancelled')
*   employee_notes (text, nullable)
*   manager_notes (text, nullable) - Reason for denial or comments
*   submitted_at (timestamp)
*   reviewed_at (timestamp, nullable)
*   attachment_path (string, nullable) - For supporting documents
*   created_at, updated_at

**Leave Balances Table:**
*   id (primary key)
*   user_id (foreign key to users.id)
*   leave_type (enum: 'paid_time_off', 'unpaid_leave', 'sick_leave', 'vacation')
*   total_allocated (decimal) - Annual entitlement
*   used (decimal) - Days consumed
*   pending (decimal) - Days in pending requests
*   available (decimal) - Calculated: total_allocated - used - pending
*   year (integer) - Fiscal year
*   created_at, updated_at
*   unique(user_id, leave_type, year)

**Leave Balance History Table:**
*   id (primary key)
*   leave_balance_id (foreign key)
*   change_amount (decimal) - Positive for accrual, negative for consumption
*   change_type (enum: 'accrual', 'consumption', 'adjustment', 'carryover')
*   leave_request_id (foreign key, nullable) - If related to a request
*   performed_by_user_id (foreign key to users.id)
*   notes (text, nullable)
*   created_at

**Company Holidays Table:**
*   id (primary key)
*   name (string) - Holiday name
*   date (date)
*   is_recurring (boolean) - If it repeats annually
*   region (string, nullable) - For location-specific holidays
*   created_at, updated_at

**Leave Policies Table:**
*   id (primary key)
*   policy_type (enum: 'blackout_period', 'minimum_notice', 'max_consecutive_days')
*   leave_type (enum, nullable) - Specific leave type or null for all
*   config_json (json) - Flexible configuration storage
*   is_active (boolean)
*   created_at, updated_at

**Manager Delegations Table:**
*   id (primary key)
*   manager_id (foreign key to users.id)
*   delegate_manager_id (foreign key to users.id)
*   start_date (date)
*   end_date (date)
*   is_active (boolean)
*   created_at, updated_at

**Leave Request History Table (Audit Trail):**
*   id (primary key)
*   leave_request_id (foreign key)
*   action (enum: 'submitted', 'approved', 'denied', 'cancelled', 'edited', 'overridden')
*   performed_by_user_id (foreign key to users.id)
*   old_values (json, nullable) - For edit tracking
*   new_values (json, nullable)
*   notes (text, nullable)
*   created_at

### 7.2. Business Rules

**Leave Request Validation:**
*   An employee cannot submit overlapping leave requests (date range validation).
*   Start date must be before or equal to end date.
*   Leave requests cannot exceed available balance for the leave type (enforced before submission).
*   Requests cannot include company holidays in the day count calculation.
*   Half-day leave must be clearly specified with start/end times (future enhancement).

**Approval & Authorization:**
*   A leave request can only be approved/denied by the designated manager or their delegate.
*   HR administrators can override any approval/denial with justification.
*   Manager denials must include a reason in manager_notes.
*   Requests pending for more than X days trigger escalation notifications.

**Cancellation Rules:**
*   Pending requests can be cancelled by the employee anytime.
*   Approved requests can be cancelled by the employee (with manager notification).
*   Managers can cancel approved requests with employee notification.
*   Consumed leave (past end_date) cannot be cancelled but can be adjusted by HR.

**Balance Management:**
*   Leave balance is automatically deducted when a request is approved.
*   Pending requests reduce "available" balance but not "used" balance.
*   Cancelling a request restores the balance immediately.
*   Negative balances are not allowed unless explicitly approved by HR admin.
*   Leave balances carry over based on policy configuration (max carryover days).

**Policy Enforcement:**
*   Minimum notice periods enforced by leave type (configurable, warning only for MVP).
*   Blackout periods block leave requests for specified date ranges (enforced).
*   Maximum consecutive days limit (configurable, enforced with override option).
*   Multi-level approval required for extended leaves (>X days, configurable).

**Delegation & Workflow:**
*   When a manager is on approved leave, requests route to their designated delegate.
*   If no delegate is set, requests route to the manager's manager.
*   HR admins can reassign pending requests to different approvers.

**Audit & History:**
*   All state changes are logged in leave_request_history table.
*   All balance adjustments are logged in leave_balance_history table.
*   Modifications to approved requests create new history entries with old/new values.
*   History records are immutable (no updates or deletes).

## 8. Non-Functional Requirements

### 8.1. Performance
*   **Response Time:** Page loads should complete within 2 seconds under normal load.
*   **API Response:** API endpoints should respond within 500ms for 95% of requests.
*   **Concurrent Users:** System should support at least 500 concurrent users.
*   **Database Optimization:** All queries should use proper indexes; N+1 query prevention required.
*   **Caching:** Implement caching for frequently accessed data (holidays, policies, team structures).
*   **Background Jobs:** Long-running tasks (notifications, reports) should use queued jobs.

### 8.2. Security
*   **Authentication:** Secure password hashing (bcrypt), session management with timeout (2 hours).
*   **Authorization:** Role-based access control enforced at controller and policy levels.
*   **Vulnerability Protection:** CSRF tokens, XSS prevention, SQL injection protection (via Eloquent ORM).
*   **Password Policy:** Minimum 8 characters, complexity requirements (uppercase, number, special char).
*   **Session Security:** Secure cookies, HTTP-only flags, same-site protection.
*   **Data Encryption:** Sensitive data encrypted at rest (database encryption enabled).
*   **Audit Logging:** All critical actions logged with user ID, IP address, timestamp.
*   **File Upload Security:** Validate file types, scan for malware, size limits (5MB).
*   **Rate Limiting:** API endpoints protected against brute force (max 60 requests/minute).
*   **GDPR Compliance:** Data export capability, right to be forgotten, consent tracking.

### 8.3. Usability
*   **Intuitive Navigation:** Clear menu structure, breadcrumbs, contextual help.
*   **Responsive Design:** Mobile-friendly interface (tablet/phone support).
*   **Accessibility:** WCAG 2.1 Level AA compliance, screen reader compatible, keyboard navigation.
*   **Error Handling:** Clear, actionable error messages; no technical jargon for users.
*   **Loading Indicators:** Visual feedback for async operations.
*   **Consistency:** Uniform UI patterns, color scheme, typography throughout.

### 8.4. Scalability
*   **Database Design:** Normalized schema with proper indexing for growth.
*   **Horizontal Scaling:** Stateless application design for load balancer compatibility.
*   **Archive Strategy:** Automatic archival of leave data older than 5 years.
*   **Multi-tenancy Ready:** Database structure supports future multi-company expansion.

### 8.5. Maintainability
*   **Code Quality:** Follow Laravel best practices, PSR-12 coding standards.
*   **Documentation:** Inline code comments, API documentation, README files.
*   **Testing:** Minimum 80% test coverage, unit and feature tests required.
*   **Version Control:** Git with descriptive commit messages, feature branch workflow.
*   **Error Logging:** Centralized logging with log levels (error, warning, info, debug).
*   **Dependency Management:** Keep packages up-to-date, document version requirements.

### 8.6. Availability & Reliability
*   **Uptime Target:** 99.5% availability (excluding planned maintenance).
*   **Backup Strategy:** Daily automated database backups, 30-day retention.
*   **Disaster Recovery:** Recovery Point Objective (RPO): 24 hours, Recovery Time Objective (RTO): 4 hours.
*   **Monitoring:** Application health checks, error rate tracking, performance monitoring.

### 8.7. Compatibility
*   **Browsers:** Support latest 2 versions of Chrome, Firefox, Safari, Edge.
*   **Mobile OS:** iOS 14+, Android 10+.
*   **Database:** PostgreSQL 13+ (via Supabase).
*   **PHP Version:** Laravel 12 requirements (PHP 8.2+).

## 9. Technology Stack
*   **Backend:** Laravel (PHP Framework)
*   **Frontend:** Tailwind CSS (for styling), Blade (templating engine), Alpine.js (for minor interactivity, if needed).
*   **Database:** MySQL (default, can be configured)
*   **Other:** Composer, npm, Git

## 10. Product Roadmap

### 10.1. MVP (Phase 1) - Core Features
**Goal:** Minimal viable system for leave request submission, approval, and conflict detection.

**Included:**
*   User authentication with Employee/Manager/HR Admin roles
*   Leave request submission with balance validation
*   Leave balance tracking and management
*   Manager approval/denial workflow with conflict detection
*   Company holiday calendar integration
*   Email notifications for state changes
*   Basic audit trail
*   Team availability calendar
*   Basic reporting dashboard

**Success Criteria:**
*   Employees can submit and track leave requests
*   Managers can approve/deny with conflict visibility
*   System prevents balance overruns
*   All requests are properly audited

### 10.2. Post-MVP v1.1 (Month 1-2) - Enhanced Usability
**Goal:** Improve efficiency and handle edge cases discovered in MVP.

**Features:**
*   HR Admin interface for policy configuration
*   Document attachment support (medical certificates)
*   Manager delegation system for vacation coverage
*   Bulk approve/deny operations
*   Edit pending requests functionality
*   Advanced conflict detection rules (skills-based, project-based)
*   Notification preferences and reminder system
*   Mobile-responsive UI optimization
*   Calendar export (PDF, CSV, iCal)

**Success Criteria:**
*   HR can configure policies without developer intervention
*   Managers save 50% time on bulk approvals
*   Zero approval bottlenecks during manager absences

### 10.3. Post-MVP v1.2 (Month 3-4) - Integration & Analytics
**Goal:** Connect with existing systems and provide actionable insights.

**Features:**
*   REST API for HRIS/Payroll integration
*   Google Calendar / Outlook calendar sync
*   Slack/Teams integration for quick approvals
*   Advanced analytics dashboard (trends, patterns, forecasts)
*   Automated accrual system (monthly/yearly balance updates)
*   Multi-level approval workflows for extended leaves
*   Blackout period enforcement
*   Retroactive leave request handling
*   Cost impact analysis

**Success Criteria:**
*   Integration with at least 2 external systems
*   Management has data-driven insights for staffing decisions
*   Automated accrual eliminates manual HR balance updates

### 10.4. Future (v2.0+) - Advanced Features
**Goal:** Transform into comprehensive workforce management platform.

**Features:**
*   AI-powered optimal leave scheduling suggestions
*   Predictive workforce planning
*   Multi-company/multi-location support
*   Regional compliance modules (labor law automation)
*   Leave swap/trade system between employees
*   Wellness day tracking and mental health support
*   Progressive Web App (PWA) with offline support
*   Advanced GDPR compliance tools
*   Integration marketplace
*   Self-service HR portal expansion (timesheets, expenses, benefits)

**Success Criteria:**
*   System scales to 10,000+ employees
*   AI recommendations reduce conflicts by 30%
*   Platform expansion beyond leave management

## 11. Success Metrics
*   Reduction in reported staffing conflicts due to leave.
*   Increased efficiency in time off approval process.
*   High user adoption rate among employees and managers.
*   Positive feedback from users on ease of use and visibility.