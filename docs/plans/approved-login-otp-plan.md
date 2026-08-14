# Plan: Send Email-Verification OTP on First Approved Login

## Goal
Do not send a verification code while an account is pending approval. Send a six-digit email OTP only after an approved user successfully logs in for the first time, then require OTP verification before profile completion or dashboard access.

## Target User Flow
1. A user registers, accepts the DPA on the registration form, and receives the existing pending-approval confirmation in the UI. No OTP or verification link is sent.
2. An administrator approves the account. The existing approval notification tells the user they can log in; it does not include an OTP.
3. The approved user successfully logs in. If their email is unverified and they do not already have an unexpired OTP, the system creates and emails one six-digit code, then redirects to the OTP page.
4. The user enters the code. A valid code verifies the email, emits Laravel's `Verified` event, and clears the stored OTP.
5. The verified user continues to profile completion when needed, then reaches the dashboard.
6. Later logins do not send another OTP. A code is sent again only through the explicit resend action, after expiry, or after an email-address change.

## Implementation Changes

### Registration and approval
- Stop OTP dispatch from public registration so newly pending users receive no verification code.
- Keep the account status as `pending`, the approval workflow unchanged, and the approval email limited to the login invitation.
- Ensure newly created pending users have no OTP or expiry value. Clear stale OTP values for pre-existing pending accounts during rollout.

### First approved login
- After credentials and active account status are validated, check whether the user has verified their email.
- For an unverified user with no current unexpired OTP, generate, hash, store, and send a new six-digit code through the existing OTP mailer.
- Redirect every unverified approved login to `verification.notice`; do not allow an intended destination or dashboard to bypass verification.
- If an unexpired code already exists, redirect to the OTP page without sending a duplicate email.

### OTP verification and user experience
- Preserve the existing ten-minute configurable expiry, hashed storage, resend throttling, and successful-code cleanup.
- Keep the OTP page and profile resend controls as the only user-facing verification flow. Resend must invalidate the previous code before sending a replacement.
- When a user changes their email address, clear verification and OTP state, then require the same OTP flow on their next authenticated request.
- Remove the legacy signed-link route, controller, views, messages, and tests once the OTP flow tests pass.

Completed:
- The legacy signed-link route, controller, and fallback OTP view have been removed.
- The OTP verification page now uses separate forms for verification and resend, and the README documents the current first-login OTP flow.

## DPA Decision
- Keep the current DPA behavior: acceptance is required and recorded during registration, so it is not shown a second time after first login.
- Post-approval DPA consent is out of scope for this plan.

## Test Plan
- Registration creates a pending, unverified user with no OTP and sends no OTP email.
- Admin approval activates the account and sends only the approval notification.
- Pending and inactive users remain unable to log in.
- An approved unverified user’s first successful login sends one OTP and redirects to the OTP page.
- A second login with an unexpired OTP does not send another email; resend replaces the prior code.
- Correct, incorrect, and expired codes produce the expected verification state and redirects.
- A verified user reaches profile completion or dashboard normally; legacy signed-link URLs are unavailable after cleanup.

## Acceptance Criteria
- No pending user receives a verification code.
- Every approved user can obtain a current OTP immediately after their first successful login.
- No verification link is sent or accepted.
- Dashboard and profile-completion access remain blocked until email verification succeeds.
- Existing approval, DPA-at-registration, profile completion, and inactive-account protections continue to work.
