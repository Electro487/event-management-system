# Event Management System API Migration Blueprint

Date: 2026-04-22
Project: EventManagementSystem
Goal: Migrate from current PHP MVC monolith to separated frontend + backend API architecture while preserving exact functional behavior and current UI/CSS.

## 1. Executive Summary

This blueprint defines a low-risk migration path from the existing server-rendered MVC architecture to an API-first architecture.

Non-negotiable constraints:
- No CSS redesign.
- No new feature scope.
- No behavior regression.
- Existing flows must continue to work for client, organizer, and admin roles.

Recommended strategy:
- Use a strangler migration pattern.
- Keep existing pages and styles initially.
- Introduce backend API endpoints first.
- Switch UI calls incrementally from MVC endpoints to API endpoints.
- Preserve redirects and route aliases until full cutover.

## 2. Current System Baseline (As Implemented)

## 2.1 Core Architecture
- Custom front-controller entrypoint with route dispatch: public/index.php
- Route map: app/config/routes.php
- Controllers directly render views, redirect, and return JSON in mixed style.
- Session-based auth with role checks and role re-sync from DB on protected requests.
- DB access via PDO models.
- External integrations: Stripe checkout + PHPMailer OTP.

## 2.2 Domain Modules
- Auth and OTP: AuthController + User model + MailHelper.
- Events: AdminController/OrganizerController + Event model.
- Bookings: ClientController/AdminController/OrganizerController + Booking model.
- Payments: PaymentController + Payment model + Stripe.
- Notifications: NotificationController + Notification model + polling JS.
- Profile pictures: role-specific profile update/delete endpoints with file upload.

## 2.3 Data Model
Tables in database/schema.sql:
- users
- events
- bookings
- payments
- notifications

Important business states already in use:
- users.role: admin | organizer | client
- bookings.status: pending | confirmed | cancelled
- bookings.payment_status: unpaid | partially_paid | paid
- events.status: active | inactive | draft

## 2.4 Frontend Coupling Snapshot
Hardcoded base path is deeply used:
- /EventManagementSystem/public/... in many views and JS.

AJAX currently used for:
- notifications APIs
- profile image upload/delete

Everything else is mostly form submit + server redirect.

## 3. Migration Principles

1. Behavior parity over refactor purity.
2. API contracts defined before code rewrite.
3. Keep compatibility wrappers during transition.
4. Avoid big-bang cutover.
5. Add verification gates after each phase.

## 4. Target Architecture

## 4.1 High-Level Design
- Backend API service
  - Stateless HTTP API (JSON).
  - Auth middleware.
  - Role middleware.
  - Validation layer.
  - Domain services for business logic.
  - Repositories/data access layer.
- Frontend app
  - Can remain PHP-rendered initially or become separate SPA later.
  - Uses API endpoints for all data writes and reads.
  - Keeps existing UI and CSS assets unchanged.
- Shared assets
  - Keep current public/assets/css and current HTML structure as much as possible.

## 4.2 Suggested Backend Folder Structure

api/
  src/
    bootstrap/
      app.php
      routes.php
    config/
      app.php
      db.php
      cors.php
      stripe.php
      mail.php
    middleware/
      AuthMiddleware.php
      RoleMiddleware.php
      ValidationMiddleware.php
      ErrorHandlerMiddleware.php
    controllers/
      AuthApiController.php
      UserApiController.php
      EventApiController.php
      BookingApiController.php
      PaymentApiController.php
      NotificationApiController.php
      DashboardApiController.php
    services/
      AuthService.php
      EventService.php
      BookingService.php
      PaymentService.php
      NotificationService.php
      FileService.php
    repositories/
      UserRepository.php
      EventRepository.php
      BookingRepository.php
      PaymentRepository.php
      NotificationRepository.php
    dtos/
      requests/
      responses/
    policies/
      BookingPolicy.php
      EventPolicy.php
    utils/
      ApiResponse.php
      DateHelper.php
      MoneyHelper.php
  tests/
    integration/
    contract/
    parity/

## 4.3 Frontend Integration Modes
Mode A (recommended first):
- Keep current views and CSS.
- Replace form actions/fetch targets to API progressively.

Mode B (later):
- Separate frontend app (React/Vue/Next/etc.) consuming same API.

## 5. API Contract Blueprint (Parity-Oriented)

## 5.1 Auth
- POST /api/v1/auth/register
- POST /api/v1/auth/login
- POST /api/v1/auth/forgot-password
- POST /api/v1/auth/verify-otp
- POST /api/v1/auth/reset-password
- POST /api/v1/auth/logout
- GET  /api/v1/auth/me

## 5.2 Users / Admin User Management
- POST   /api/v1/users/profile-picture
- DELETE /api/v1/users/profile-picture
- GET    /api/v1/admin/users
- PATCH  /api/v1/admin/users/{id}/role
- PATCH  /api/v1/admin/users/{id}/block

## 5.3 Events
- GET    /api/v1/events
- GET    /api/v1/events/{id}
- POST   /api/v1/events
- PUT    /api/v1/events/{id}
- DELETE /api/v1/events/{id}

## 5.4 Bookings
- GET    /api/v1/bookings
- GET    /api/v1/bookings/{id}
- POST   /api/v1/bookings
- PATCH  /api/v1/bookings/{id}/cancel
- PATCH  /api/v1/bookings/{id}/approve
- PATCH  /api/v1/bookings/{id}/mark-paid

## 5.5 Payments
- POST /api/v1/payments/checkout
- POST /api/v1/payments/confirm
- GET  /api/v1/payments/{bookingId}/summary
- GET  /api/v1/payments/{bookingId}/history

## 5.6 Notifications
- GET    /api/v1/notifications/latest
- GET    /api/v1/notifications
- GET    /api/v1/notifications/counts
- PATCH  /api/v1/notifications/{id}/read
- PATCH  /api/v1/notifications/{id}/unread
- PATCH  /api/v1/notifications/mark-all-read
- PATCH  /api/v1/notifications/mark-all-unread
- DELETE /api/v1/notifications/{id}
- DELETE /api/v1/notifications

## 5.7 Dashboards
- GET /api/v1/dashboard/admin
- GET /api/v1/dashboard/organizer
- GET /api/v1/dashboard/client

## 6. Functional Parity Rules (Must Preserve)

1. Role sync on each protected request (block or role changes take effect immediately).
2. Duplicate booking prevention for active bookings.
3. Booking cancellation lock:
   - confirmed + partially_paid/paid cannot be cancelled by client.
4. Booking approval rule:
   - organizer/admin can approve only if payment_status is partially_paid or paid.
5. Event visibility:
   - client browsing should show active events only.
6. Dynamic display status:
   - past confirmed bookings shown as completed in UI views where currently applied.
7. Payment advance logic:
   - 50% advance target with installment support and cap handling.
8. Notification side effects for booking/event/payment actions must remain equivalent.
9. Profile upload/delete behavior and stored path compatibility must remain equivalent.

## 7. Phased Migration Plan (Detailed)

## Phase 0: Preparation and Freeze
Deliverables:
- Feature freeze window for migration-only changes.
- Backup plan and rollback playbook.
- Baseline snapshot of DB and key flows.

Tasks:
- Freeze non-critical features.
- Export route map and flow matrix from current system.
- Record baseline behavior videos/screens for role journeys.

Exit criteria:
- Baseline approved.

## Phase 1: API Foundation
Deliverables:
- API skeleton and error handling standard.
- Middleware stack.
- Unified JSON response format.

Tasks:
- Build app bootstrap, routing, and middleware order.
- Add central exception handler.
- Add request validation helpers.

Exit criteria:
- Health endpoint and protected test endpoint pass.

## Phase 2: Auth and Identity
Deliverables:
- Auth endpoints and token/session strategy.
- OTP and password reset parity.

Tasks:
- Implement register/login/forgot-password/verify-otp/reset-password/logout/me.
- Keep current password policy exactly.
- Keep blocked-user and role checks.

Exit criteria:
- Auth parity tests pass against current behavior.

## Phase 3: Core Domain APIs
Deliverables:
- Events, bookings, and dashboard endpoints.

Tasks:
- Implement event CRUD with ownership rules and package validation.
- Implement booking create/cancel/approve/mark-paid logic.
- Implement dashboard metrics by role.

Exit criteria:
- Side-effect parity (notifications + status transitions) verified.

## Phase 4: Payments and Notifications
Deliverables:
- Stripe payment API flow.
- Notification read/unread/delete/count endpoints.

Tasks:
- Implement checkout + success confirmation idempotency.
- Maintain progressive advance calculations.
- Keep notification filters and counts equivalent.

Exit criteria:
- End-to-end booking-to-payment-to-notification flow passes.

## Phase 5: Frontend Endpoint Switching
Deliverables:
- Existing views now calling API endpoints.
- CSS untouched.

Tasks:
- Replace hardcoded route actions progressively.
- Add frontend API helper for base URL and auth headers.
- Preserve existing UI structure and classes.

Exit criteria:
- No visual regressions and no broken links/forms.

## Phase 6: Compatibility and Cutover
Deliverables:
- Old MVC routes deprecated safely.
- Full switch to API backend.

Tasks:
- Keep adapters/bridges where needed for transition.
- Run smoke suite and parity suite.
- Enable cutover flag.

Exit criteria:
- Production cutover checklist all green.

## 8. Compatibility Layer Plan

During migration, keep compatibility endpoints:
- Existing routes remain but call API internally or redirect to API-backed flows.
- This allows partial frontend migration without breaking user sessions.

Suggested compatibility sequence:
1. Notifications and profile APIs first (already JSON-like).
2. Booking actions next.
3. Event management actions.
4. Auth pages last (higher risk due to session/token changes).

## 9. Data and Security Hardening Before Cutover

Required corrections:
1. Seed password hygiene:
   - database/seed.sql currently uses plaintext values; replace with hashed values.
2. Date consistency:
   - standardize date vs datetime comparison rules in API layer.
3. Validation normalization:
   - strict request schemas for booking/event/payment payloads.
4. File upload constraints:
   - MIME + extension + size checks.
5. Auth hardening:
   - CSRF (if cookie auth), secure cookie flags, rate limiting on auth endpoints.
6. Secrets:
   - enforce .env presence for Stripe and mail credentials in non-dev environments.

## 10. Test Strategy (Parity-First)

## 10.1 Test Types
- Contract tests for API responses.
- Integration tests with DB.
- Parity tests comparing old flow vs new flow outcomes.
- Smoke tests for all role journeys.

## 10.2 Must-Pass Scenarios
- Registration + OTP + login.
- Role redirects and blocked user enforcement.
- Event create/update/delete with notification fanout.
- Booking create, duplicate guard, approval/cancel rules.
- Stripe payment success idempotency and payment status updates.
- Notification counters and read/unread actions.
- Profile upload/delete and avatar rendering.

## 10.3 Non-Functional Checks
- Response time under notification polling load.
- File upload reliability and cleanup.
- Logging and traceability for payment flow.

## 11. Risks and Mitigations

1. Auth migration risk (session to token)
- Mitigation: staged dual-mode auth during transition.

2. Behavior drift in booking/payment rules
- Mitigation: explicit policy classes + parity tests for every state combination.

3. URL hardcoding breakage
- Mitigation: central base URL helper and gradual replacement.

4. Notification duplication/missing events
- Mitigation: side-effect tests and idempotent notification guards for repeated callbacks.

5. Data inconsistencies
- Mitigation: pre-cutover data audit scripts and post-cutover reconciliation.

## 12. Cutover Checklist

Pre-cutover:
- All parity tests green.
- DB backup validated restore.
- Monitoring dashboards ready.
- Rollback switch documented.

Cutover day:
- Enable API mode flag.
- Run smoke tests across admin/organizer/client.
- Verify payment and notification live flows.

Post-cutover 48h:
- Error-rate monitoring.
- Payment reconciliation.
- Notification queue integrity check.

## 13. Suggested Implementation Order (Task Board Ready)

1. Establish API bootstrap + middleware + response contract.
2. Build auth endpoints + OTP/email parity.
3. Build event APIs + package validations.
4. Build booking APIs + state policy rules.
5. Build payment APIs + Stripe idempotency.
6. Build notification APIs + counts/filters.
7. Build profile upload APIs.
8. Wire frontend calls incrementally (no CSS/HTML redesign).
9. Run parity suite and fix drift.
10. Perform staged cutover and retire legacy handlers.

## 14. Decision Log Needed Before Coding

Decisions required from project owners:
1. Auth style: cookie session API vs JWT tokens.
2. Frontend phase: keep PHP-rendered views first or move to SPA immediately.
3. Backward compatibility duration for old routes.
4. Soft-delete vs hard-delete policy for events/bookings/notifications.

## 15. Success Criteria

Migration is successful only when:
- All existing user journeys work unchanged functionally.
- UI/CSS remains unchanged.
- API contracts are stable and versioned.
- Legacy route usage is zero in frontend runtime.
- No critical production incidents in post-cutover observation window.

---

Prepared from full project inspection including:
- Routing and entrypoint
- All controllers and models
- Schema and seed data
- Notification and frontend JS coupling
- Payment and OTP integrations
