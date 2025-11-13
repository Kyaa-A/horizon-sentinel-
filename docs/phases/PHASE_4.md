# Phase 4: Employee Interface

**Status:** ⏳ Not Started
**Estimated Duration:** 12 hours
**Tasks:** 0/24
**Prerequisites:** Phase 3 must be complete

---

## Overview

Build the complete user interface for employees to submit, view, and manage their leave requests.

---

## 📦 EPIC 4.1: Leave Request Submission

**Goal:** Enable employees to submit new leave requests
**Tasks:** 0/8

### 📖 STORY 4.1.1: Create Leave Request Form

**User Story:** As an employee, I can submit a leave request by filling out a form with dates and leave type.

**Atomic Tasks:**
- ⚛️ HS-FE-001 - Create LeaveRequestController
- ⚛️ HS-FE-006 - Implement create() method
- ⚛️ HS-FE-007 - Create request submission view
- ⚛️ HS-FE-003 - Create LeaveRequestFormRequest validation
- ⚛️ HS-FE-008 - Implement store() method

---

## 📦 EPIC 4.2: Leave Request Management

**Goal:** Enable employees to view and manage their requests
**Tasks:** 0/8

### 📖 STORY 4.2.1: View All My Leave Requests

**User Story:** As an employee, I can see all my leave requests in one place with their current status.

**Atomic Tasks:**
- ⚛️ HS-FE-004 - Implement index() method
- ⚛️ HS-FE-005 - Create leave requests list view

### 📖 STORY 4.2.2: View Leave Request Details

**User Story:** As an employee, I can view details of a specific leave request.

**Atomic Tasks:**
- ⚛️ HS-FE-009 - Implement show() method
- ⚛️ HS-FE-010 - Create request details view

### 📖 STORY 4.2.3: Cancel Pending Requests

**User Story:** As an employee, I can cancel my pending leave requests before they're approved.

**Atomic Tasks:**
- ⚛️ HS-FE-011 - Implement cancel() method
- ⚛️ Add cancel button to views

---

## 📦 EPIC 4.3: Authorization & Security

**Goal:** Ensure employees can only access their own requests
**Tasks:** 0/4

### 📖 STORY 4.3.1: Implement Request Authorization

**User Story:** As the system, I must ensure employees can only view and edit their own requests.

**Atomic Tasks:**
- ⚛️ HS-FE-002 - Create LeaveRequestPolicy
- ⚛️ Register policy in AuthServiceProvider
- ⚛️ Apply policy to controller methods

---

## 📦 EPIC 4.4: User Interface Components

**Goal:** Create consistent, user-friendly UI elements
**Tasks:** 0/4

### 📖 STORY 4.4.1: Add Navigation Menu Items

**User Story:** As an employee, I can easily navigate to leave request pages from the main menu.

**Atomic Tasks:**
- ⚛️ HS-FE-012 - Add navigation links
- ⚛️ Add active state indicators
- ⚛️ Add role-based menu visibility

[→ Full task details in .cursor/.rules/process-task-list.md]

---

## ➡️ Next Phase
[Phase 5: Manager Interface](./PHASE_5.md)
