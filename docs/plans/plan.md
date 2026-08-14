# Plan: 6-Digit OTP Verification System

## TL;DR
Replace Laravel's default email verification link with a 6-digit OTP sent via the existing SMTP configuration. Users will receive an OTP code via email instead of a verification link, and they'll enter this code to verify their email address.

## Overview
- **Current system**: Laravel's built-in email verification sends a verification link
- **New system**: Send 6-digit OTP via email, user enters OTP to verify
- **Key change**: Replace `sendEmailVerificationNotification()` with custom OTP generation and sending
- **Keep**: Existing SMTP configuration, user model structure, verification flow structure

## Phase 1: Database & Model Changes
**Dependencies**: None

**Tasks**:
1. Add `otp` and `otp_expires_at` columns to the `users` migration
   - `otp`: string, nullable
   - `otp_expires_at`: timestamp, nullable
2. Update `UserFactory` to optionally generate OTP for testing
3. Add accessor/mutator methods in `User.php` for OTP handling
4. Add method to check if OTP is valid (`hasValidOtp()`)

**Files to modify**:
- `database/migrations/0001_01_01_000000_create_users_table.php` - Add OTP columns
- `database/factories/UserFactory.php` - Add OTP generation
- `app/Models/User.php` - Add OTP methods and accessors

**Verification**: 
- Run migration: `php artisan migrate:refresh --seed`
- Verify OTP columns exist in users table

## Phase 2: OTP Generation & Sending
**Dependencies**: Phase 1

**Tasks**:
1. Create `OtpsController` or add methods to existing auth controller
2. Generate 6-digit random OTP when user requests verification
3. Send OTP via email using existing SMTP (MailMessage)
4. Store OTP and expiration in user model
5. Invalidate any existing OTP when new one is generated

**Files to create/modify**:
- `app/Http/Controllers/Auth/OtpsController.php` - New controller for OTP
- `app/Http/Controllers/Auth/EmailVerificationNotificationController.php` - Modify to send OTP instead of link
- Routes in `routes/auth.php`

**Verification**:
- Test OTP generation
- Test OTP email sending
- Test OTP invalidation on new request

## Phase 3: OTP Verification Flow
**Dependencies**: Phase 1 & 2

**Tasks**:
1. Create OTP verification endpoint
2. Validate OTP entered by user
3. Mark email as verified when OTP is valid
4. Clear OTP after successful verification
5. Handle expired OTP cases

**Files to create/modify**:
- `app/Http/Controllers/Auth/VerifyOtpController.php` - New controller for OTP verification
- `routes/auth.php` - Add OTP verification routes
- `resources/views/auth/verify-email.blade.php` - Modify view for OTP input
- Update `EmailVerificationPromptController` to show OTP form

**Verification**:
- Test OTP verification with correct code
- Test OTP verification with incorrect code
- Test OTP expiration handling

## Phase 4: Views & User Experience
**Dependencies**: Phase 1 & 2 & 3

**Tasks**:
1. Modify `resources/views/auth/verify-email.blade.php` to show OTP form
2. Modify user profile forms to show OTP verification status
3. Add OTP resend functionality
4. Update error messages for OTP flow

**Files to modify**:
- `resources/views/auth/verify-email.blade.php` - Main OTP verification view
- `resources/views/user/partials/update-profile-information-form.blade.php` - OTP status display
- `resources/views/user/profile/partials/update-profile-information-form.blade.php` - OTP status display

**Verification**:
- Render OTP view correctly
- Test OTP resend functionality
- Verify status messages display correctly

## Phase 5: Tests & QA
**Dependencies**: All previous phases

**Tasks**:
1. Update existing EmailVerificationTest to test OTP flow
2. Add new tests for OTP generation, sending, and verification
3. Test edge cases (expired OTP, max attempts, etc.)
4. Manual testing of complete flow

**Files to modify/create**:
- `tests/Feature/Auth/EmailVerificationTest.php` - Update or replace with OTP tests
- Manual testing of complete user registration → OTP receipt → verification flow

**Verification**:
- Run all auth-related tests
- Complete manual end-to-end test
- Verify no regressions in other features

## Phase 6: Cleanup & Documentation
**Dependencies**: Phase 5

**Tasks**:
1. Remove unused email verification code
2. Update documentation comments
3. Ensure all references to old verification flow are updated
4. Final code review

**Files to modify**:
- `app/Http/Controllers/Auth/EmailVerificationNotificationController.php` - Clean up
- `routes/auth.php` - Remove old verification routes if needed
- Any other references to old verification system

---

## Key Design Decisions

### OTP Generation
- 6-digit numeric code (e.g., 482917)
- Generated using `random_int(100000, 999999)`
- Stored hashed in database for security
- Expires after 10 minutes (configurable)

### Email Sending
- Uses existing SMTP configuration (no SMS)
- MailMessage with OTP code in body
- Subject: "Your Alcatt Portal 6-digit verification code"
- Includes clear instructions for user

### User Flow
1. User registers → receives OTP email immediately
2. User enters OTP on verification page
3. System validates OTP → marks email as verified → logs in user
4. If OTP expires or is wrong, user can request new OTP

### Backward Compatibility
- Keep `MustVerifyEmail` contract implementation
- Maintain `hasVerifiedEmail()` method functionality
- Old verification routes can remain for fallback

## Success Criteria
- [ ] New users receive 6-digit OTP via email upon registration
- [ ] Users can enter OTP to verify their email
- [ ] OTP expires after configurable time (10 minutes)
- [ ] Users can request new OTP if needed
- [ ] System marks email as verified upon successful OTP entry
- [ ] All existing tests pass or are updated
- [ ] No broken links or references to old verification system