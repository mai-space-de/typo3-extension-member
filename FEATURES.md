# mai_member — Feature Reference

> Canonical reference for the `mai_member` TYPO3 extension.
> Layer: Feature. Status: 🔨 In Progress.
> Key dependency: `mai_mail` (mail queue), `mai_base` (shared icons, base controller).
> Optional: `mai_account` (FE-user link, see §4).

---

## 1. Member Record

Custom domain record in `tx_maimember_member` representing an approved, active member.

| Field | Type | Required | Notes |
| --- | --- | --- | --- |
| `first_name` | `varchar(255)` | ✅ | Trimmed on save; shown in backend label |
| `last_name` | `varchar(255)` | ✅ | Primary backend label field; sorted A→Z by default |
| `email` | `varchar(255)` | ✅ | Validated as email address |
| `phone` | `varchar(100)` | — | Optional contact number |
| `status` | `varchar(20)` | ✅ | `'active'` (default) or `'inactive'` |
| `join_date` | `int` (UNIX timestamp) | — | Date picker, format `date`; `0` = not set |
| `image` | FAL relation | — | Single image; max 1 file; common image types |
| `fe_user` | FK → `fe_users.uid` | — | Optional link to a TYPO3 FE user (see §4) |

`Member::isActive()` returns `true` only when `status === 'active'`.
`Member::getFullName()` returns `trim(firstName . ' ' . lastName)`.

`MemberRepository::findActive()` filters by `status = 'active'` and sorts by `lastName ASC, firstName ASC`.
`MemberRepository::findByStatus(string $status)` is the underlying query method.

---

## 2. Application Record

Transient domain record in `tx_maimember_application` capturing a membership request before approval.

| Field | Type | Required | Notes |
| --- | --- | --- | --- |
| `first_name` | `varchar(255)` | ✅ | Trimmed on save |
| `last_name` | `varchar(255)` | ✅ | Primary label; alternative label includes `first_name` and `email` |
| `email` | `varchar(255)` | ✅ | Validated as email address |
| `message` | `text` | — | Optional motivation message from the applicant |
| `status` | `varchar(20)` | ✅ | `'pending'` (default), `'approved'`, `'rejected'` |
| `submitted_at` | `int` (UNIX timestamp) | ✅ | Set automatically on submit; read-only in TCA |
| `member` | FK → `tx_maimember_member.uid` | — | Populated by `ApplicationService::approve()` |

Status-check convenience methods:

| Method | Returns `true` when |
| --- | --- |
| `Application::isPending()` | `status === 'pending'` |
| `Application::isApproved()` | `status === 'approved'` |
| `Application::isRejected()` | `status === 'rejected'` |

`ApplicationRepository::findPending()` filters by `status = 'pending'` and sorts by `submittedAt DESC`.

---

## 3. Application Workflow

Applications follow a linear state machine managed by `ApplicationService`.

```
submit (frontend)
       │
       ▼
   [ pending ]  ── approve() ──▶  [ approved ]
                                       │
                └── reject()  ──▶  [ rejected ]
```

### ApplicationService API

```php
// Approve: creates a Member record, links it to the application, persists.
public function approve(Application $application, int $memberStoragePid = 0): Member

// Reject: sets status to 'rejected', persists.
public function reject(Application $application): void
```

**`approve()` behaviour:**
1. Creates a new `Member` with `firstName`, `lastName`, `email` copied from the application.
2. Sets `Member::status = 'active'` and `joinDate = time()`.
3. Sets `Member::pid` to `$memberStoragePid` if `> 0`; otherwise falls back to `$application->getPid()`.
4. Calls `memberRepository->add($member)`.
5. Sets `Application::status = 'approved'` and `Application::member = $member`.
6. Calls `persistenceManager->persistAll()`.
7. Returns the created `Member`.

> `approve()` does **not** automatically create a `fe_users` record or populate
> `Member::feUser`. The FE-user link must be set manually in the backend after
> a FE-user account exists (see §4).

---

## 4. FE-User Link — Relationship to `mai_account`

Both `mai_member` and `mai_account` extend the shared TYPO3 `fe_users` table.
Neither extension overwrites the other; the link is **optional and bi-directional**.

### How the link works

```
fe_users (TYPO3 core table)
│
│  tx_maimember_member
│  ┌──────────────────────────────────────┐
│  │  fe_user  (int) ─────────────────────┼──▶  fe_users.uid
│  └──────────────────────────────────────┘
│
│  mai_account extends fe_users with:
│  ┌──────────────────────────────────────────────────────────────────┐
│  │  tx_maiaccount_member_uid  (int) ──────────────────────────────┼──▶  tx_maimember_member.uid
│  └──────────────────────────────────────────────────────────────────┘
```

| Direction | Column | Table | Set by |
| --- | --- | --- | --- |
| Member → FE user | `fe_user` | `tx_maimember_member` | Backend editor (manual) |
| FE user → Member | `tx_maiaccount_member_uid` | `fe_users` | Backend editor (manual, requires `mai_account`) |

### Columns added by `mai_account` to `fe_users`

`mai_account` extends `fe_users` via TCA Overrides and `ext_tables.sql`.
None of these columns belong to `mai_member`:

| Column | Type | Purpose |
| --- | --- | --- |
| `tx_maiaccount_mfa_enabled` | `tinyint` | TOTP MFA toggle |
| `tx_maiaccount_mfa_secret` | `varchar(255)` | Base32 TOTP shared secret |
| `tx_maiaccount_interests` | `int` (MM count) | M:N → `tx_maiaccount_interest` (via `tx_maiaccount_feuser_interest_mm`) |
| `tx_maiaccount_newsletter_optin` | `tinyint` | Frontend newsletter opt-in flag (not canonical subscription record) |
| `tx_maiaccount_member_uid` | `int` | Reverse FK → `tx_maimember_member.uid` |
| `tx_maiaccount_confirm_token` | `varchar(128)` | Registration confirmation token |
| `tx_maiaccount_confirm_expires` | `int` | Token expiry timestamp |

### Linkage rules

- `mai_member` does **not** declare a hard dependency on `mai_account` in `composer.json`; the FE-user link
  field (`fe_user`) is defined in `mai_member`'s own TCA against the `fe_users` table directly.
- `mai_account` does **not** require `mai_member`; `tx_maiaccount_member_uid` is registered
  via `addToAllTCAtypes()` and points to `tx_maimember_member` but is optional (min 0 items).
- When both extensions are installed, the backend editor can link a Member record to a FE user
  using either direction; the application must keep both ends consistent.
- `ApplicationService::approve()` creates a `Member` record but **never** creates or modifies a
  `fe_users` row. Linking a new member to a FE account is a manual post-approval step.
- `Member::feUser` is typed `?AbstractEntity` (widened from the now-removed `FrontendUser` base class
  deleted in TYPO3 v14). At runtime this resolves to a `Maispace\MaiAccount\Domain\Model\FrontendUser`
  if `mai_account` is loaded and the property is mapped.

### Accessing the link in code

```php
// Retrieve the linked FE user from a Member record
$feUser = $member->getFeUser(); // ?AbstractEntity — null when not linked

// Retrieve the linked Member UID from a FE user (when mai_account is installed)
$memberUid = $feUser->getTxMaiaccountMemberUid(); // int, 0 when not linked
```

---

## 5. Content Element Plugins

| CType | Label key | Controller | Actions | Backend group |
| --- | --- | --- | --- | --- |
| `mai_member_view` | `tt_content.CType.mai_member_view` | `MemberController` | `list`, `detail` | `default` |
| `mai_member_application` | `tt_content.CType.mai_member_application` | `ApplicationController` | `form`, `submit` | `default` |

`mai_member_view` has a FlexForm attachment (`Members.xml`) with one field: `settings.listLimit`.
`mai_member_application` has no FlexForm.

Both CTypes use `generic_content.svg` from `mai_base` as their content-element icon.

---

## 6. Frontend Rendering

### MemberController

| Action | Cached | Variables assigned | Notes |
| --- | --- | --- | --- |
| `listAction` | yes | `members` (paginator), `pagination` | Queries `findActive()`; paginated via `PaginationTrait` |
| `detailAction` | yes | `member` | Redirects to `list` when `$member === null` |

`listAction` reads `settings.listLimit` (default `12`) from TypoScript/FlexForm.
Pagination uses `PaginationTrait::paginateQueryResult()` from `mai_base`; the paginator is
assigned as `members` and the pagination control as `pagination`.

### ApplicationController

| Action | Cached | Variables assigned | Notes |
| --- | --- | --- | --- |
| `formAction` | yes | `application` (empty model) | Renders the application form |
| `submitAction` | no | — | Saves, mails, adds flash message, redirects to `form` |

`submitAction` sets `submittedAt = $GLOBALS['EXEC_TIME']` and `status = 'pending'` before
persisting. On success it calls `MemberMailer::sendApplicationReceived()` and redirects to `form`.

---

## 7. Backend Module

Route: `mai_member`. Actions are handled by `MemberBackendController`.

| Action | Description |
| --- | --- |
| `indexAction` | Lists all members and all pending applications |
| `approveAction(Application)` | Calls `ApplicationService::approve()`, sends `ApplicationApproved` mail, flash success |
| `rejectAction(Application)` | Calls `ApplicationService::reject()`, sends `ApplicationRejected` mail, flash info |
| `exportCsvAction` | Streams `members.csv` with columns `first_name, last_name, email, phone, status, join_date` |

The `index` view renders two sections: the full member list and the pending-applications list.
`exportCsvAction` formats `join_date` as `Y-m-d` when > 0, empty string otherwise.

---

## 8. Mail Notifications

`MemberMailer` delegates to `MailService` from `mai_mail` (queue-based, never direct SMTP).
All three methods are no-ops when `Application::email === ''`.

| Method | Template | Subject key | Trigger |
| --- | --- | --- | --- |
| `sendApplicationReceived(Application)` | `Email/ApplicationReceived.html` | `email.applicationReceived.subject` | `ApplicationController::submitAction` |
| `sendApplicationApproved(Application)` | `Email/ApplicationApproved.html` | `email.applicationApproved.subject` | `MemberBackendController::approveAction` |
| `sendApplicationRejected(Application)` | `Email/ApplicationRejected.html` | `email.applicationRejected.subject` | `MemberBackendController::rejectAction` |

Fallback subjects (when the translation key is missing):

| Method | Fallback subject |
| --- | --- |
| `sendApplicationReceived` | `'We received your application'` |
| `sendApplicationApproved` | `'Your application has been approved'` |
| `sendApplicationRejected` | `'Update on your application'` |

Templates are rendered via TYPO3's `ViewFactoryInterface` + `ViewFactoryData` (v14 pattern).
Template root: `EXT:mai_member/Resources/Private/Templates/Email/`.

---

## 9. FlexForm Configuration

Only `mai_member_view` has a FlexForm (`Configuration/FlexForms/Members.xml`).

| Field | Type | Default | Notes |
| --- | --- | --- | --- |
| `settings.listLimit` | `number` | `12` | Overrides the TypoScript constant; controls page size for the paginator |

---

## 10. TypoScript Configuration

**Constants** (`plugin.tx_maimember`):

```typoscript
plugin {
    tx_maimember {
        view {
            templateRootPath = EXT:mai_member/Resources/Private/Templates/
            partialRootPath   = EXT:mai_member/Resources/Private/Partials/
            layoutRootPath    = EXT:mai_member/Resources/Private/Layouts/
        }
        settings {
            listLimit = 12
        }
    }
}
```

**Setup** registers `tx_maimember` Extbase plugin paths and `tt_content` FLUIDTEMPLATE objects for
`mai_member_view` and `mai_member_application`. Both `tt_content` objects reference the same view-path
constants. `mai_member_view` includes a `FlexFormProcessor` (target variable `flexform`).

The Site Set identifier is `maispace/mai-member` (`Configuration/Sets/Member/config.yaml`).
It imports the main constants and setup files via `@import`.

---

## 11. Database Tables

### `tx_maimember_member`

| Column | Type | Default | Notes |
| --- | --- | --- | --- |
| `uid` | `int` auto_increment | — | Primary key |
| `pid` | `int` | `0` | Storage page |
| `tstamp` | `int` | `0` | Last modification timestamp (TYPO3 enableField) |
| `crdate` | `int` | `0` | Creation timestamp (TYPO3 enableField) |
| `hidden` | `tinyint` | `0` | Visibility flag (TYPO3 enableField) |
| `deleted` | `tinyint` | `0` | Soft-delete flag (TYPO3 enableField) |
| `sys_language_uid` | `int` | `0` | Translation overlay language |
| `l10n_parent` | `int` | `0` | UID of the default-language record |
| `l10n_diffsource` | `mediumblob` | — | Diff source for translations |
| `first_name` | `varchar(255)` | `''` | |
| `last_name` | `varchar(255)` | `''` | |
| `email` | `varchar(255)` | `''` | |
| `phone` | `varchar(100)` | `''` | |
| `status` | `varchar(20)` | `'active'` | `'active'` or `'inactive'` |
| `join_date` | `int unsigned` | `0` | UNIX timestamp; `0` = not set |
| `image` | `int unsigned` | `0` | FAL reference count |
| `fe_user` | `int unsigned` | `0` | FK → `fe_users.uid`; `0` = not linked |

### `tx_maimember_application`

| Column | Type | Default | Notes |
| --- | --- | --- | --- |
| `uid` | `int` auto_increment | — | Primary key |
| `pid` | `int` | `0` | Storage page |
| `tstamp` | `int` | `0` | Last modification timestamp |
| `crdate` | `int` | `0` | Creation timestamp |
| `hidden` | `tinyint` | `0` | Visibility flag |
| `deleted` | `tinyint` | `0` | Soft-delete flag |
| `first_name` | `varchar(255)` | `''` | |
| `last_name` | `varchar(255)` | `''` | |
| `email` | `varchar(255)` | `''` | |
| `message` | `text` | `NULL` | Optional motivation message |
| `status` | `varchar(20)` | `'pending'` | `'pending'`, `'approved'`, or `'rejected'` |
| `submitted_at` | `int unsigned` | `0` | UNIX timestamp set at form submission |
| `member` | `int unsigned` | `0` | FK → `tx_maimember_member.uid`; populated on approval |

---

## 12. Architecture Constraints

- **Mail** — `MemberMailer` must always call `MailService::queue()` from `mai_mail`. Never add
  direct `symfony/mailer` usage.
- **SCSS** — no stylesheets in this extension; all CSS lives in `mai_assets` / `mai_theme`.
- **FE-user creation** — `mai_member` never creates `fe_users` rows. FE account creation is
  the responsibility of `mai_account` (self-registration or manual admin creation).
- **FE-user link** — the `Member::feUser` / `fe_users.tx_maiaccount_member_uid` bi-directional
  link is manually maintained. Application approval (`ApplicationService::approve()`) only creates
  the `Member` record; it never touches `fe_users`.
- **`mai_account` is optional** — `mai_member` works without `mai_account` installed.
  The `Member::feUser` field references `fe_users` directly (a TYPO3 core table). The reverse
  field (`tx_maiaccount_member_uid`) is absent when `mai_account` is not loaded.
- **No custom category table** — `mai_member` has no category feature and must not introduce one.
- **Layer rule** — `mai_member` is in the Feature layer and may depend on Infrastructure
  (`mai_base`) and Theme & Mail (`mai_mail`), but must not depend on other Feature-layer
  extensions directly. The optional `mai_account` coupling uses `GeneralUtility::makeInstance()`
  behind an `ExtensionManagementUtility::isLoaded()` guard if needed at runtime.
