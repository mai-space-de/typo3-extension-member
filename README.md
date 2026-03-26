# maispace/member — TYPO3 Member Management

[![CI](https://github.com/mai-space-de/typo3-extension-member/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/mai-space-de/typo3-extension-member/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://www.php.net/)
[![TYPO3](https://img.shields.io/badge/TYPO3-13.4%20LTS-orange)](https://typo3.org/)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)

A TYPO3 extension for managing members and membership applications. Provides
backend record management, a frontend application form and Extbase-based
domain logic for member and application records.

**Requires:** TYPO3 13.4 LTS · PHP 8.2+

---

## Features at a glance

| Feature | Details |
|---|---|
| Member records | Extbase domain model with full TCA configuration |
| Member application records | Separate application domain model and workflow |
| Backend list module | Manage members and applications via standard TYPO3 backend |
| Frontend plugin | Extbase/Fluid plugin for application forms |
| Service layer | `ApplicationService` for application business logic |
| PSR-4 autoloading | `Maispace\MaiMember\` → `Classes/` |

---

## Installation

```bash
composer require maispace/mai-member
```

TYPO3 will automatically discover the extension. No manual activation is
required.

Include the TypoScript setup in your site package:

```typoscript
@import 'EXT:mai_member/Configuration/TypoScript/setup.typoscript'
```

Run the Database Analyser in **Admin Tools → Database Analyser** to create
the extension tables after installation.

---

## Development

### Running tests

```bash
composer install
composer test
```

Or verbose:

```bash
vendor/bin/phpunit --configuration phpunit.xml.dist --testdox
```

### Linting

```bash
composer lint:check   # run all linters
composer lint:fix     # auto-fix where possible
```

### CI

| Job | What it checks |
|---|---|
| `composer-validate` | `composer.json` is valid and well-formed |
| `unit-tests` | PHPUnit suite across PHP 8.2 / 8.3 × TYPO3 13.4 |
| `static-analysis` | PHPStan (`phpstan.neon`, level max) |
| `code-style` | EditorConfig + PHP-CS-Fixer |
| `typoscript-lint` | TypoScript style/structure |

---

## License

GPL-2.0-or-later
