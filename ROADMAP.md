# Horizon Sentinel - Development Roadmap

**Project:** Digital Leave Request & Conflict Avoidance System
**Last Updated:** November 13, 2025
**Overall Progress:** 30% Complete

---

## 📊 Progress Overview

| Phase | Status | Progress | Duration | Phase File |
|-------|--------|----------|----------|------------|
| **Phase 1:** Foundation Setup | ✅ Complete | 100% | ~2 hours | [PHASE_1.md](./docs/phases/PHASE_1.md) |
| **Phase 2:** Authentication | ✅ Complete | 100% | ~2 hours | [PHASE_2.md](./docs/phases/PHASE_2.md) |
| **Phase 3:** Core Data Model | 🔄 In Progress | 0% | ~6 hours | [PHASE_3.md](./docs/phases/PHASE_3.md) |
| **Phase 4:** Employee Interface | ⏳ Not Started | 0% | ~12 hours | [PHASE_4.md](./docs/phases/PHASE_4.md) |
| **Phase 5:** Manager Interface | ⏳ Not Started | 0% | ~14 hours | [PHASE_5.md](./docs/phases/PHASE_5.md) |
| **Phase 6:** Calendar & Conflicts | ⏳ Not Started | 0% | ~8 hours | [PHASE_6.md](./docs/phases/PHASE_6.md) |
| **Phase 7:** Testing & Polish | ⏳ Not Started | 0% | ~6 hours | [PHASE_7.md](./docs/phases/PHASE_7.md) |

**Total Estimated Time:** ~50 hours
**Completed:** ~4 hours
**Remaining:** ~46 hours

---

## 🎯 Phase Summaries

### ✅ Phase 1: Foundation Setup (COMPLETE)

**Goal:** Set up Laravel development environment with database
**Status:** ✅ Complete
**Duration:** 2 hours

**Key Deliverables:**
- Laravel 12 installed and configured
- PostgreSQL database connected
- Environment variables configured
- Initial migrations run

**Epics:**
1. Development Environment Setup
2. Database Configuration

[→ View Full Phase 1 Details](./docs/phases/PHASE_1.md)

---

### ✅ Phase 2: User Authentication (COMPLETE)

**Goal:** Implement complete user authentication system
**Status:** ✅ Complete
**Duration:** 2 hours

**Key Deliverables:**
- Laravel Breeze installed
- User registration & login
- Password reset functionality
- Email verification
- Profile management
- Tailwind CSS + Alpine.js configured

**Epics:**
1. Authentication System
2. Frontend Framework Setup

[→ View Full Phase 2 Details](./docs/phases/PHASE_2.md)

---

### 🔄 Phase 3: Core Data Model (IN PROGRESS)

**Goal:** Build database schema for users, roles, and leave requests
**Status:** 🔄 In Progress (0%)
**Current Task:** HS-DB-003 - Add role & manager_id to users
**Duration:** ~6 hours

**Key Deliverables:**
- User roles (employee/manager)
- Manager-employee relationships
- Leave request tables
- Leave request history tracking
- Eloquent models with relationships
- Test data seeders

**Epics:**
1. User Role Management
2. Leave Request Data Structure
3. Data Relationships & Models
4. Test Data Generation

[→ View Full Phase 3 Details](./docs/phases/PHASE_3.md) ← **START HERE**

---

### ⏳ Phase 4: Employee Interface (NOT STARTED)

**Goal:** Build UI for employees to manage leave requests
**Status:** ⏳ Not Started
**Duration:** ~12 hours

**Key Deliverables:**
- Leave request submission form
- View all leave requests (own)
- Leave request details page
- Cancel pending requests
- Status notifications
- Navigation menu items

**Epics:**
1. Leave Request Submission
2. Leave Request Management
3. Authorization & Security
4. User Interface Components

[→ View Full Phase 4 Details](./docs/phases/PHASE_4.md)

---

### ⏳ Phase 5: Manager Interface (NOT STARTED)

**Goal:** Build UI for managers to review and approve requests
**Status:** ⏳ Not Started
**Duration:** ~14 hours

**Key Deliverables:**
- Manager dashboard
- Pending requests list
- Approve/deny functionality
- Team calendar view
- Conflict detection alerts
- Manager notes/feedback

**Epics:**
1. Manager Dashboard
2. Request Review System
3. Team Availability Calendar
4. Conflict Detection Logic

[→ View Full Phase 5 Details](./docs/phases/PHASE_5.md)

---

### ⏳ Phase 6: Calendar & Conflict Detection (NOT STARTED)

**Goal:** Visual calendar with conflict detection
**Status:** ⏳ Not Started
**Duration:** ~8 hours

**Key Deliverables:**
- Interactive team calendar
- Color-coded conflict indicators
- Team availability percentages
- Conflict resolution workflows
- Calendar filtering options

**Epics:**
1. Calendar Component
2. Conflict Detection Engine
3. Visual Indicators & Alerts

[→ View Full Phase 6 Details](./docs/phases/PHASE_6.md)

---

### ⏳ Phase 7: Testing & Polish (NOT STARTED)

**Goal:** Comprehensive testing and UI refinement
**Status:** ⏳ Not Started
**Duration:** ~6 hours

**Key Deliverables:**
- Automated tests (Feature & Unit)
- UI/UX polish
- Performance optimization
- Security audit
- Documentation updates

**Epics:**
1. Automated Testing
2. UI/UX Refinement
3. Performance & Security
4. Documentation

[→ View Full Phase 7 Details](./docs/phases/PHASE_7.md)

---

## 🏗️ Task Hierarchy Structure

Each phase is organized in a three-level hierarchy:

```
📦 EPIC - Major feature area or capability
  └─ 📖 STORY - User-facing functionality or technical requirement
      └─ ⚛️ ATOMIC - Individual implementable task
```

**Example:**

```
📦 EPIC: User Role Management
  └─ 📖 STORY: Implement Employee/Manager Role System
      ├─ ⚛️ ATOMIC: Create migration for role column
      ├─ ⚛️ ATOMIC: Add manager_id foreign key
      └─ ⚛️ ATOMIC: Update User model with role methods
```

---

## 🎯 Current Focus

**You are here:** Phase 3 - Core Data Model

**Next Task:** HS-DB-003
**Description:** Create migration for extending users table with role and manager_id
**Estimated Time:** 30 minutes
**File:** [PHASE_3.md](./docs/phases/PHASE_3.md)

---

## 📅 Milestone Timeline

### ✅ Milestone 1: Foundation Ready (COMPLETE)
- Phase 1 & 2 complete
- Basic Laravel app with authentication
- **Achieved:** November 13, 2025

### 🎯 Milestone 2: Data Model Complete (Target: Week 1)
- Phase 3 complete
- All database tables created
- Models with relationships
- Test data available

### 🎯 Milestone 3: Employee Features (Target: Week 2)
- Phase 4 complete
- Employees can submit requests
- Employees can view request status

### 🎯 Milestone 4: Manager Features (Target: Week 3)
- Phase 5 complete
- Managers can review requests
- Basic conflict detection working

### 🎯 Milestone 5: MVP Complete (Target: Week 4)
- Phases 6 & 7 complete
- Full calendar view
- Comprehensive testing
- Ready for production

---

## 📚 Documentation Structure

```
horizon-sentinel/
├── ROADMAP.md                    # This file - Overview of all phases
├── docs/
│   └── phases/
│       ├── PHASE_1.md           # Foundation Setup
│       ├── PHASE_2.md           # Authentication
│       ├── PHASE_3.md           # Core Data Model (current)
│       ├── PHASE_4.md           # Employee Interface
│       ├── PHASE_5.md           # Manager Interface
│       ├── PHASE_6.md           # Calendar & Conflicts
│       └── PHASE_7.md           # Testing & Polish
│
├── PROJECT_STATUS.md            # Current status snapshot
├── QUICK_START.md               # Quick reference guide
├── NEXT_STEPS.md                # Detailed implementation steps
└── README_HORIZON.md            # Project overview
```

---

## 🚀 How to Use This Roadmap

### For Planning
1. **See the big picture:** Review this ROADMAP.md
2. **Understand the phase:** Open the relevant PHASE_X.md file
3. **Identify dependencies:** Check Epic > Story > Atomic structure

### For Development
1. **Find current phase:** Look for 🔄 In Progress status
2. **Open phase file:** Read the detailed Epic/Story/Atomic breakdown
3. **Follow atomic tasks:** Complete tasks in order
4. **Track progress:** Update task status as you complete them

### For Status Updates
1. **Check progress:** See completion percentages
2. **Identify blockers:** Note any "blocked" tasks
3. **Update estimates:** Adjust time estimates based on actual work

---

## 🔗 Quick Links

- **Current Work:** [Phase 3 - Core Data Model](./docs/phases/PHASE_3.md)
- **Getting Started:** [PROJECT_STATUS.md](./PROJECT_STATUS.md)
- **Quick Commands:** [QUICK_START.md](./QUICK_START.md)
- **Step-by-Step Guide:** [NEXT_STEPS.md](./NEXT_STEPS.md)
- **Product Requirements:** [.cursor/.rules/create-prd.md](./.cursor/.rules/create-prd.md)
- **All Tasks:** [.cursor/.rules/process-task-list.md](./.cursor/.rules/process-task-list.md)

---

## 📊 Epic Distribution Across Phases

| Phase | Epics | Stories | Atomic Tasks |
|-------|-------|---------|--------------|
| Phase 1 | 2 | 3 | 5 |
| Phase 2 | 2 | 3 | 4 |
| Phase 3 | 4 | 7 | 21 |
| Phase 4 | 4 | 8 | 24 |
| Phase 5 | 4 | 10 | 28 |
| Phase 6 | 3 | 6 | 16 |
| Phase 7 | 4 | 8 | 18 |
| **Total** | **23** | **45** | **116** |

---

## 🎓 Legend

**Status Icons:**
- ✅ Complete
- 🔄 In Progress
- ⏳ Not Started
- ⚠️ Blocked
- 🔴 At Risk

**Task Types:**
- 📦 EPIC - Major feature area
- 📖 STORY - User functionality
- ⚛️ ATOMIC - Individual task

**Priority Levels:**
- 🔥 Critical
- ⭐ High
- 📌 Medium
- 💡 Low / Nice-to-have

---

**Last Updated:** November 13, 2025
**Next Review:** After Phase 3 completion
