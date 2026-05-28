# Next Steps — EXT:mai_member

Last audited: 2026-04-19.

---

## Done in the 2026-04-19 pass

- `composer.json` gained a proper `require` block (php, mai-base, mai-mail, cms-* packages).
- `ext_emconf.php` declares `mai_base` + `mai_mail` as hard dependencies, `mai_account` as a suggestion.
- `Configuration/Backend/Modules.php` registers the member backend module under `web`.
- `MemberBackendController::indexAction()` no longer passes a single argument to `addShortcutButton()` (which would crash at runtime against the current `AbstractBackendController` signature). It now passes `'mai_member'` + display name.
- `MemberBackendController` gained `approveAction(Application)` and `rejectAction(Application)`; the Fluid Index template exposes those buttons next to each pending application and a "Download CSV" action for the member list.
- New service `ApplicationService::approve()` creates a `Member` from an `Application`, links them, sets status / `joinDate`; `reject()` just flips status.
- New service `MemberMailer` queues HTML emails via `mai_mail::MailService` for: application received (auto-responder on submit), approved, rejected.
- `ApplicationController::submitAction()` persists synchronously and calls `MemberMailer::sendApplicationReceived()`.
- Email templates under `Resources/Private/Templates/Email/` (`ApplicationReceived`, `ApplicationApproved`, `ApplicationRejected`).
- Services.yaml exposes `ApplicationService` and `MemberMailer` as public so they can be injected into controllers and (later) scheduler tasks.

---

## 1. Schema application

After pulling, apply the schema changes via the TYPO3 Database Analyser or `ddev exec vendor/bin/typo3 database:updateschema` — no new columns were added in this pass, but existing installations built before the backend-module paths will need the `mai_member` key registered in the backend routes (nothing to do manually — TYPO3 picks up `Configuration/Backend/Modules.php` on next container rebuild).

---

## 2. FE-user linking when an application is approved

`ApplicationService::approve()` creates a bare `Member` but does not create a matching `fe_users` record. Wire that up once `mai_account` exposes a registration-without-password path, or:

1. Generate a random password, create the fe_user via `RegistrationService`, send a "set your password" email via `mai_account::AccountMailer`.
2. Link `Member::$feUser` to the new record; update `tx_maimember_member.fe_user`.
3. Optionally populate `tx_maiaccount_member_uid` on the new fe_user (reverse link).

This is a soft dependency — guard with `ExtensionManagementUtility::isLoaded('mai_account')`.

---

## 3. Member detail page

`MemberController::detailAction(Member)` exists but the current `Detail.html` template is minimal. Flesh out:

- Contact details, joined date, member-reference relationship to fe_user if set.
- Gate email/phone visibility by fe-user group so public listings don't leak private contact info.

---

## 4. Category grouping

`FEATURES.md` doesn't list categories, but most sibling extensions (`mai_team`, `mai_testimonials`) use `sys_category` for filtering. Consider adding:

1. `categories` column on `tx_maimember_member` (MM with `sys_category`).
2. `MemberRepository::findByCategories(array)`.
3. FlexForm selector for the `mai_member_view` CType to pick categories.

---

## 5. QA

```bash
composer lint:check
composer check:phpstan
composer test:unit
```

Priority targets for unit tests:

- `ApplicationService::approve()` — creates a member, copies fields, updates application status.
- `ApplicationService::reject()` — sets status without touching member records.
- `MemberMailer` — subject resolution falls back to default when locallang key is missing.
- `MemberRepository::findByStatus()` / `findActive()`.
