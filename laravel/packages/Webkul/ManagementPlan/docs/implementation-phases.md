# Management Plan Module Phases

## Phase 1 - Data Core
- Run migrations and verify tables `management_plans`, `management_plan_assignments`, and `management_plan_time_entries`.
- Confirm foreign keys and uniqueness constraints prevent duplicates.
- Acceptance: can create plans, assignments, and one monthly entry per user/plan/month.

## Phase 2 - Security
- Validate ACL keys appear in role permissions UI.
- Verify users with custom roles only access routes they have permission to.
- Verify non-global users get `403` on setup/global routes.
- Acceptance: route permission checks and global-only guard are enforced.

## Phase 3 - User Operations
- Validate user monthly flow: select month, submit hours, save draft/submit state.
- Confirm entries can be edited for the same month and assignment.
- Acceptance: users can consistently maintain monthly imputations for assigned plans.

## Phase 4 - Supervisor Visibility
- Validate global dashboard filters by month/user/plan.
- Confirm totals and grouped hours by user match raw rows.
- Acceptance: global users can audit monthly effort across the organization.

## Phase 5 - Optional TimeTracker Integration
- Evaluate mapping between TimeTracker records and `management_plan_time_entries`.
- If enabled, build sync job or import action and test idempotency.
- Acceptance: imported time does not duplicate existing monthly entries.
