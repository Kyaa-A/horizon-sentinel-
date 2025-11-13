# Phase 3: Core Data Model

**Status:** 🔄 In Progress (0%)
**Estimated Duration:** 6 hours
**Tasks Completed:** 0/21
**Current Task:** HS-DB-003

---

## 📦 EPIC 3.1: User Role Management

**Goal:** Implement employee and manager role system
**Status:** 🔄 In Progress (0%)
**Priority:** 🔥 Critical
**Tasks:** 0/5

### 📖 STORY 3.1.1: Implement Employee/Manager Role System

**User Story:** As a system administrator, I need to distinguish between employees and managers so that each user has appropriate permissions.

**Status:** 🔄 In Progress
**Business Value:** Enables role-based access control throughout the application

**Atomic Tasks:**

#### ⚛️ TASK: HS-DB-003 - Create Migration for User Roles ← **START HERE**
- **Status:** ⏳ Not Started
- **Priority:** 🔥 Critical
- **Description:** Add role column and manager_id foreign key to users table
- **Estimated:** 30 min
- **Dependencies:** HS-DB-002 (Complete)
- **File:** `database/migrations/YYYY_MM_DD_add_role_and_manager_to_users_table.php`
- **Command:** `php artisan make:migration add_role_and_manager_to_users_table`
- **Acceptance Criteria:**
  - [ ] Migration file created
  - [ ] `role` enum column added (values: 'employee', 'manager', default: 'employee')
  - [ ] `manager_id` foreign key column added (nullable, references users.id)
  - [ ] Foreign key constraint includes `onDelete('set null')`
  - [ ] Migration can be run successfully
  - [ ] Migration can be rolled back successfully
- **Implementation Code:**
  ```php
  public function up(): void
  {
      Schema::table('users', function (Blueprint $table) {
          $table->enum('role', ['employee', 'manager'])
                ->default('employee')
                ->after('email');

          $table->foreignId('manager_id')
                ->nullable()
                ->after('role')
                ->constrained('users')
                ->nullOnDelete();
      });
  }

  public function down(): void
  {
      Schema::table('users', function (Blueprint $table) {
          $table->dropForeign(['manager_id']);
          $table->dropColumn(['role', 'manager_id']);
      });
  }
  ```

#### ⚛️ TASK: HS-DB-003-RUN - Run User Role Migration
- **Status:** ⏳ Not Started (blocked by HS-DB-003)
- **Priority:** 🔥 Critical
- **Description:** Execute the migration to update users table
- **Estimated:** 2 min
- **Dependencies:** HS-DB-003
- **Command:** `php artisan migrate`
- **Acceptance Criteria:**
  - [ ] Migration runs without errors
  - [ ] `role` column exists in users table
  - [ ] `manager_id` column exists in users table
  - [ ] Foreign key constraint created
- **Verification:**
  ```bash
  php artisan tinker
  > \DB::getSchemaBuilder()->getColumnListing('users')
  # Should include 'role' and 'manager_id'
  ```

### 📖 STORY 3.1.2: Add Role Helper Methods to User Model

**User Story:** As a developer, I need convenient methods to check user roles so that I can implement role-based logic easily.

**Status:** ⏳ Not Started
**Business Value:** Simplifies role checking throughout codebase

**Atomic Tasks:**

#### ⚛️ TASK: HS-BE-001 - Update User Model with Relationships and Methods
- **Status:** ⏳ Not Started
- **Priority:** 🔥 Critical
- **Description:** Add relationships and helper methods to User model
- **Estimated:** 1 hour
- **Dependencies:** HS-DB-003-RUN
- **File:** `app/Models/User.php`
- **Acceptance Criteria:**
  - [ ] `manager()` belongsTo relationship defined
  - [ ] `directReports()` hasMany relationship defined
  - [ ] `leaveRequests()` hasMany relationship defined
  - [ ] `isManager()` helper method returns boolean
  - [ ] `isEmployee()` helper method returns boolean
  - [ ] `hasManager()` helper method returns boolean
  - [ ] `role` and `manager_id` added to $fillable array
- **Implementation Code:**
  ```php
  // Add to $fillable
  protected $fillable = [
      'name',
      'email',
      'password',
      'role',
      'manager_id',
  ];

  // Relationships
  public function manager(): BelongsTo
  {
      return $this->belongsTo(User::class, 'manager_id');
  }

  public function directReports(): HasMany
  {
      return $this->hasMany(User::class, 'manager_id');
  }

  public function leaveRequests(): HasMany
  {
      return $this->hasMany(LeaveRequest::class);
  }

  // Helper Methods
  public function isManager(): bool
  {
      return $this->role === 'manager';
  }

  public function isEmployee(): bool
  {
      return $this->role === 'employee';
  }

  public function hasManager(): bool
  {
      return $this->manager_id !== null;
  }
  ```

---

## 📦 EPIC 3.2: Leave Request Data Structure

**Goal:** Create database tables for storing leave requests
**Status:** ⏳ Not Started
**Priority:** 🔥 Critical
**Tasks:** 0/6

### 📖 STORY 3.2.1: Create Leave Request Storage

**User Story:** As the system, I need a database table to store leave requests so that all requests are permanently recorded.

**Status:** ⏳ Not Started
**Business Value:** Core data storage for the entire leave management system

**Atomic Tasks:**

#### ⚛️ TASK: HS-DB-004 - Create Leave Requests Table Migration
- **Status:** ⏳ Not Started
- **Priority:** 🔥 Critical
- **Description:** Create migration for leave_requests table with all required columns
- **Estimated:** 1.5 hours
- **Dependencies:** HS-DB-003-RUN
- **File:** `database/migrations/YYYY_MM_DD_create_leave_requests_table.php`
- **Command:** `php artisan make:migration create_leave_requests_table`
- **Acceptance Criteria:**
  - [ ] Migration file created
  - [ ] All columns defined per PRD
  - [ ] Foreign keys to users table created
  - [ ] Indexes added for performance
  - [ ] Enum values match PRD specifications
  - [ ] Migration runs successfully
- **Table Structure:**
  - `id` - Primary key
  - `user_id` - FK to users (employee who submitted)
  - `manager_id` - FK to users (manager who approves)
  - `leave_type` - Enum: paid_time_off, unpaid_leave, sick_leave, vacation
  - `start_date` - Date
  - `end_date` - Date
  - `status` - Enum: pending, approved, denied, cancelled (default: pending)
  - `employee_notes` - Text, nullable
  - `manager_notes` - Text, nullable
  - `submitted_at` - Timestamp
  - `reviewed_at` - Timestamp, nullable
  - `created_at`, `updated_at` - Timestamps
- **Implementation Code:**
  ```php
  public function up(): void
  {
      Schema::create('leave_requests', function (Blueprint $table) {
          $table->id();
          $table->foreignId('user_id')->constrained()->cascadeOnDelete();
          $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
          $table->enum('leave_type', [
              'paid_time_off',
              'unpaid_leave',
              'sick_leave',
              'vacation'
          ]);
          $table->date('start_date');
          $table->date('end_date');
          $table->enum('status', [
              'pending',
              'approved',
              'denied',
              'cancelled'
          ])->default('pending');
          $table->text('employee_notes')->nullable();
          $table->text('manager_notes')->nullable();
          $table->timestamp('submitted_at')->useCurrent();
          $table->timestamp('reviewed_at')->nullable();
          $table->timestamps();

          // Indexes for performance
          $table->index('user_id');
          $table->index('manager_id');
          $table->index('status');
          $table->index(['start_date', 'end_date']);
      });
  }
  ```

#### ⚛️ TASK: HS-DB-004-RUN - Run Leave Requests Migration
- **Status:** ⏳ Not Started
- **Priority:** 🔥 Critical
- **Description:** Execute the leave_requests table migration
- **Estimated:** 2 min
- **Dependencies:** HS-DB-004
- **Command:** `php artisan migrate`
- **Acceptance Criteria:**
  - [ ] Migration runs without errors
  - [ ] leave_requests table exists
  - [ ] All columns created correctly
  - [ ] Foreign keys working
  - [ ] Indexes created

### 📖 STORY 3.2.2: Create Leave Request History Tracking

**User Story:** As a compliance officer, I need an audit trail of all actions on leave requests so that I can track changes and ensure accountability.

**Status:** ⏳ Not Started
**Business Value:** Provides audit trail and history for compliance

**Atomic Tasks:**

#### ⚛️ TASK: HS-DB-005 - Create Leave Request History Table Migration
- **Status:** ⏳ Not Started
- **Priority:** ⭐ High (recommended but optional for MVP)
- **Description:** Create migration for leave_request_history table
- **Estimated:** 45 min
- **Dependencies:** HS-DB-004-RUN
- **File:** `database/migrations/YYYY_MM_DD_create_leave_request_history_table.php`
- **Command:** `php artisan make:migration create_leave_request_history_table`
- **Acceptance Criteria:**
  - [ ] Migration file created
  - [ ] All required columns defined
  - [ ] Foreign key to leave_requests table
  - [ ] Foreign key to users table (who performed action)
  - [ ] Index on leave_request_id
  - [ ] Migration runs successfully
- **Table Structure:**
  - `id` - Primary key
  - `leave_request_id` - FK to leave_requests
  - `action` - Enum: submitted, approved, denied, cancelled, updated
  - `performed_by_user_id` - FK to users
  - `notes` - Text, nullable
  - `created_at` - Timestamp
- **Implementation Code:**
  ```php
  public function up(): void
  {
      Schema::create('leave_request_history', function (Blueprint $table) {
          $table->id();
          $table->foreignId('leave_request_id')
                ->constrained()
                ->cascadeOnDelete();
          $table->enum('action', [
              'submitted',
              'approved',
              'denied',
              'cancelled',
              'updated'
          ]);
          $table->foreignId('performed_by_user_id')
                ->constrained('users')
                ->cascadeOnDelete();
          $table->text('notes')->nullable();
          $table->timestamp('created_at')->useCurrent();

          $table->index('leave_request_id');
      });
  }
  ```

#### ⚛️ TASK: HS-DB-005-RUN - Run Leave Request History Migration
- **Status:** ⏳ Not Started
- **Priority:** ⭐ High
- **Description:** Execute the history table migration
- **Estimated:** 2 min
- **Dependencies:** HS-DB-005
- **Command:** `php artisan migrate`
- **Acceptance Criteria:**
  - [ ] Migration runs without errors
  - [ ] leave_request_history table exists
  - [ ] All columns created
  - [ ] Foreign keys working

---

## 📦 EPIC 3.3: Data Relationships & Models

**Goal:** Create Eloquent models with proper relationships
**Status:** ⏳ Not Started
**Priority:** 🔥 Critical
**Tasks:** 0/6

### 📖 STORY 3.3.1: Create LeaveRequest Model

**User Story:** As a developer, I need an Eloquent model for leave requests so that I can work with leave data in PHP.

**Status:** ⏳ Not Started
**Business Value:** Enables programmatic access to leave request data

**Atomic Tasks:**

#### ⚛️ TASK: HS-BE-002-CREATE - Generate LeaveRequest Model File
- **Status:** ⏳ Not Started
- **Priority:** 🔥 Critical
- **Description:** Create LeaveRequest model file
- **Estimated:** 5 min
- **Dependencies:** HS-DB-004-RUN
- **Command:** `php artisan make:model LeaveRequest`
- **Acceptance Criteria:**
  - [ ] File created at app/Models/LeaveRequest.php
  - [ ] Extends Illuminate\Database\Eloquent\Model
  - [ ] Uses HasFactory trait

#### ⚛️ TASK: HS-BE-002 - Implement LeaveRequest Model
- **Status:** ⏳ Not Started
- **Priority:** 🔥 Critical
- **Description:** Add relationships, scopes, and methods to LeaveRequest model
- **Estimated:** 2 hours
- **Dependencies:** HS-BE-002-CREATE, HS-BE-001
- **File:** `app/Models/LeaveRequest.php`
- **Acceptance Criteria:**
  - [ ] Fillable attributes defined
  - [ ] Date casting configured
  - [ ] `user()` belongsTo relationship
  - [ ] `manager()` belongsTo relationship
  - [ ] `history()` hasMany relationship
  - [ ] Query scopes: pending(), approved(), denied(), forManager(), overlapping()
  - [ ] Helper methods: isPending(), isApproved(), isDenied()
  - [ ] recordHistory() method
- **Implementation:** (See NEXT_STEPS.md Step 16 for full code)

### 📖 STORY 3.3.2: Create LeaveRequestHistory Model

**User Story:** As a developer, I need a model for leave request history so that I can record and retrieve audit trail data.

**Status:** ⏳ Not Started
**Business Value:** Enables audit trail functionality

**Atomic Tasks:**

#### ⚛️ TASK: HS-BE-003 - Create and Implement LeaveRequestHistory Model
- **Status:** ⏳ Not Started
- **Priority:** ⭐ High
- **Description:** Create LeaveRequestHistory model with relationships
- **Estimated:** 45 min
- **Dependencies:** HS-DB-005-RUN, HS-BE-002
- **File:** `app/Models/LeaveRequestHistory.php`
- **Command:** `php artisan make:model LeaveRequestHistory`
- **Acceptance Criteria:**
  - [ ] Model file created
  - [ ] Table name specified ('leave_request_history')
  - [ ] timestamps set to false
  - [ ] Fillable attributes defined
  - [ ] Date casting configured
  - [ ] `leaveRequest()` belongsTo relationship
  - [ ] `performedBy()` belongsTo relationship
- **Implementation:** (See NEXT_STEPS.md Step 17 for full code)

---

## 📦 EPIC 3.4: Test Data Generation

**Goal:** Create seeders to populate database with test data
**Status:** ⏳ Not Started
**Priority:** ⭐ High
**Tasks:** 0/4

### 📖 STORY 3.4.1: Generate User Test Data

**User Story:** As a developer, I need sample users (managers and employees) so that I can test the application.

**Status:** ⏳ Not Started
**Business Value:** Enables testing without manual data entry

**Atomic Tasks:**

#### ⚛️ TASK: HS-BE-004-USER - Create UserSeeder
- **Status:** ⏳ Not Started
- **Priority:** ⭐ High
- **Description:** Create seeder that generates managers and employees
- **Estimated:** 1 hour
- **Dependencies:** HS-BE-001
- **File:** `database/seeders/UserSeeder.php`
- **Command:** `php artisan make:seeder UserSeeder`
- **Acceptance Criteria:**
  - [ ] Seeder file created
  - [ ] Creates 2 managers (no manager_id)
  - [ ] Creates 6-10 employees (assigned to managers)
  - [ ] All users have same password ('password')
  - [ ] Password documented in comments
  - [ ] Realistic names and emails
- **Implementation:** (See NEXT_STEPS.md Step 18 for full code)

### 📖 STORY 3.4.2: Generate Leave Request Test Data

**User Story:** As a developer, I need sample leave requests so that I can test approval workflows and conflict detection.

**Status:** ⏳ Not Started
**Business Value:** Enables testing of core features

**Atomic Tasks:**

#### ⚛️ TASK: HS-BE-004-LEAVE - Create LeaveRequestSeeder
- **Status:** ⏳ Not Started
- **Priority:** ⭐ High
- **Description:** Create seeder for leave requests in various states
- **Estimated:** 1 hour
- **Dependencies:** HS-BE-002, HS-BE-004-USER
- **File:** `database/seeders/LeaveRequestSeeder.php`
- **Command:** `php artisan make:seeder LeaveRequestSeeder`
- **Acceptance Criteria:**
  - [ ] Seeder file created
  - [ ] Creates pending requests
  - [ ] Creates approved requests
  - [ ] Creates denied requests
  - [ ] Includes overlapping leaves for conflict testing
  - [ ] Creates history records
- **Implementation:** (See NEXT_STEPS.md Step 18 for full code)

#### ⚛️ TASK: HS-BE-004-CONFIG - Update DatabaseSeeder
- **Status:** ⏳ Not Started
- **Priority:** ⭐ High
- **Description:** Configure DatabaseSeeder to call UserSeeder and LeaveRequestSeeder
- **Estimated:** 5 min
- **Dependencies:** HS-BE-004-USER, HS-BE-004-LEAVE
- **File:** `database/seeders/DatabaseSeeder.php`
- **Acceptance Criteria:**
  - [ ] DatabaseSeeder updated
  - [ ] Calls UserSeeder
  - [ ] Calls LeaveRequestSeeder
  - [ ] Seeds run in correct order

#### ⚛️ TASK: HS-BE-004-RUN - Execute Database Seeders
- **Status:** ⏳ Not Started
- **Priority:** ⭐ High
- **Description:** Run seeders to populate database with test data
- **Estimated:** 2 min
- **Dependencies:** HS-BE-004-CONFIG
- **Command:** `php artisan db:seed`
- **Acceptance Criteria:**
  - [ ] Seeders run without errors
  - [ ] Users created in database
  - [ ] Leave requests created
  - [ ] History records created
- **Verification:**
  ```bash
  php artisan tinker
  > User::count()  # Should be 8-12
  > LeaveRequest::count()  # Should be 20+
  > LeaveRequestHistory::count()  # Should be 20+
  ```

---

## 📋 Phase 3 Task Summary

### Critical Path Tasks (Must Complete)
1. ✅ HS-DB-003 - User role migration
2. ✅ HS-DB-003-RUN - Run migration
3. ✅ HS-BE-001 - Update User model
4. ✅ HS-DB-004 - Leave requests migration
5. ✅ HS-DB-004-RUN - Run migration
6. ✅ HS-BE-002-CREATE - Generate model
7. ✅ HS-BE-002 - Implement model

### Recommended Tasks (Should Complete)
8. ✅ HS-DB-005 - History table migration
9. ✅ HS-DB-005-RUN - Run migration
10. ✅ HS-BE-003 - History model
11. ✅ HS-BE-004-USER - User seeder
12. ✅ HS-BE-004-LEAVE - Leave request seeder
13. ✅ HS-BE-004-CONFIG - Configure seeder
14. ✅ HS-BE-004-RUN - Run seeders

### Task Dependencies
```
HS-DB-003 (User migration)
  └─> HS-DB-003-RUN
      └─> HS-BE-001 (User model)
          └─> HS-DB-004 (Leave migration)
              └─> HS-DB-004-RUN
                  ├─> HS-BE-002-CREATE
                  │   └─> HS-BE-002 (Leave model)
                  │       └─> HS-BE-004-LEAVE (Seeder)
                  └─> HS-DB-005 (History migration)
                      └─> HS-DB-005-RUN
                          └─> HS-BE-003 (History model)

HS-BE-001 (User model)
  └─> HS-BE-004-USER (User seeder)

HS-BE-004-USER + HS-BE-004-LEAVE
  └─> HS-BE-004-CONFIG
      └─> HS-BE-004-RUN
```

---

## ✅ Phase 3 Expected Deliverables

### Database Tables
- [ ] `users` table extended with role and manager_id
- [ ] `leave_requests` table created
- [ ] `leave_request_history` table created

### Models
- [ ] User model updated with relationships and helpers
- [ ] LeaveRequest model created with full functionality
- [ ] LeaveRequestHistory model created

### Test Data
- [ ] 2 managers in database
- [ ] 6-10 employees in database
- [ ] 20+ leave requests in various states
- [ ] History records for all requests

### Verification Checklist
- [ ] All migrations run successfully
- [ ] Can create users with roles
- [ ] Can assign managers to employees
- [ ] Can create leave requests
- [ ] Can query leave requests by status
- [ ] Can retrieve user's leave requests
- [ ] Can retrieve manager's team requests
- [ ] History tracking works

---

## 🔗 Related Documentation
- [ROADMAP.md](../../ROADMAP.md) - Overall project roadmap
- [PHASE_2.md](./PHASE_2.md) - Previous phase
- [NEXT_STEPS.md](../../NEXT_STEPS.md) - Steps 10-20
- [PRD Section 7](../../.cursor/.rules/create-prd.md#7-data-model-overview) - Data model specifications

---

## ➡️ Next Phase
[Phase 4: Employee Interface](./PHASE_4.md)

**Status:** ⏳ Not Started
**Prerequisites:** All Phase 3 critical path tasks must be complete
**First Epic:** Leave Request Submission
