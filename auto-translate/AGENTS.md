# AGENTS.md

## Purpose
This repository contains a WordPress plugin named **Automatic Translator** (`auto-translate/`) plus front-end source assets (`src/`) compiled with Gulp into minified files consumed by WordPress.

Use this guide to make safe, consistent changes.

## Project Layout
- `auto-translate/`: WordPress plugin code that is loaded at runtime.
- `auto-translate/auto-translate.php`: Plugin entrypoint, metadata, activation/deactivation hooks, bootstrap.
- `auto-translate/includes/`: Core plugin wiring (`Auto_Translate`, loader, activator, language data helpers).
- `auto-translate/admin/`: WP admin settings page, options registration, admin assets, widget registration.
- `auto-translate/public/`: Frontend hooks, widget render, Google Translate integration.
- `auto-translate/public/partials/`: HTML/PHP templates injected in `wp_head`, `wp_footer`, menus, shortcode output.
- `src/`: Source JS/SCSS (admin/public/global) to edit.
- `gulpfile.js`: Build pipeline from `src/**` to `auto-translate/**/(css|js)/*.min.*`.
- `docker-compose.yml`: Local WordPress + MySQL environment.
- `docs/`: Maintainer documentation (`architecture.md`, `options.md`, `development.md`).

## Runtime Flow
1. WP loads `auto-translate/auto-translate.php`.
2. `Auto_Translate` registers hooks through `Auto_Translate_Loader`.
3. Admin hooks:
- enqueue admin/global assets
- register settings
- render admin menu and tabs
4. Public hooks:
- enqueue public/global assets
- inject Google Translate setup script in `wp_head`
- render button/widget in footer, menu, or shortcode

## Architecture Direction
- Current architecture is a hybrid bootstrap + OOP plugin-boilerplate pattern:
- procedural bootstrap in `auto-translate/auto-translate.php`
- orchestration in `auto-translate/includes/class-auto-translate.php`
- centralized hook registration in `auto-translate/includes/class-auto-translate-loader.php`
- Prefer incremental modernization over full rewrites:
- keep bootstrap thin and stable
- preserve public hooks/options behavior for backward compatibility
- improve in small steps (namespacing/prefixing consistency, stricter typing where safe, tests, and loader hardening)
- Do not introduce a full DI container / heavy framework unless explicitly requested.

## Build And Dev Commands
- Install deps: `npm install`
- Build assets: `npm run build` (same as `gulp build`)
- Watch assets: `npx gulp watch`
- Start local WP stack: `docker-compose up`
- Start local WP stack (detached): `docker-compose up -d`
- Run WP-CLI inside Docker (no host PHP needed): `docker-compose run --rm wpcli --info`
- Install and run Plugin Check in Docker:
  `docker-compose run --rm wpcli plugin install plugin-check --activate`
  `docker-compose run --rm wpcli plugin check auto-translate`

WordPress runs on `http://localhost:8080` and mounts the local plugin folder into `wp-content/plugins/auto-translate`.

## Recommended Skill
- Prefer using `$wordpress-plugin-developer` (path: `/Users/juan/.codex/skills/wordpress-plugin-developer`) for plugin PHP/security/i18n/release-compliance tasks.
- This skill maps implementation and review work to official Plugin Handbook best practices.

## Editing Rules
- Edit source files in `src/**` for JS/SCSS changes.
- Do **not** hand-edit compiled minified assets under:
- `auto-translate/admin/js|css`
- `auto-translate/public/js|css`
- `auto-translate/global/js|css`
- After asset changes, run `npm run build` and include generated outputs in commit.
- Keep WordPress option keys prefixed with `wpat_`.
- Preserve backward compatibility for saved options; this plugin has upgrade-sensitive defaults in `Auto_Translate_Activator::add_options()`.
- Keep docs in sync:
  - Update `docs/architecture.md` when hook flow or component responsibilities change.
  - Update `docs/options.md` when adding/removing/changing `wpat_*` options or sanitization behavior.
  - Update `docs/development.md` when build/test/validation commands change.
  - Update changelog entries in both `CHANGELOG.md` and `auto-translate/README.txt` whenever behavior changes in a way maintainers/users should track.
- Prefer concise inline comments/docblocks in core runtime files (`includes/**`, `admin/class-auto-translate-admin.php`, `public/class-auto-translate-public.php`) for non-obvious behavior.

## Settings And Data Model
- Options are registered in `auto-translate/admin/class-auto-translate-admin.php`.
- Activation defaults and version migration behavior live in `auto-translate/includes/class-auto-translate-activator.php`.
- Language catalog and country mapping are defined in `auto-translate/includes/config.php`.
- Public language metadata helper: `Auto_Translate_Languages`.

## Release Checklist
- Never create a git tag, WordPress.org SVN tag, or any release tag unless Juan explicitly asks you to create that tag.
When bumping versions, update all of:
- `auto-translate/README.txt` (`Stable tag`, changelog)
- `auto-translate/auto-translate.php` (`Version` header and `AUTO_TRANSLATE_VERSION`)
- `package.json` (`version`)
- `CHANGELOG.md`

## Known Pitfalls
- `Auto_Translate_Loader::add_shortcode()` is sensitive plumbing. If touching loader internals, verify shortcode registration behavior carefully.
- `src/admin/scripts/admin.js` uses switch fall-through in some cases (no `break`), which may be intentional for chained style updates. Avoid inserting breaks without checking preview behavior.
- Plugin depends on external Google Translate script injection in `wp_head`; avoid moving that unless you test full frontend translation flow.

## Validation Before Finishing
- For PHP changes: load plugin settings page and frontend in local WP, check for PHP notices/warnings.
- Do not rely on host PHP binaries for validation in this repo. Run PHP lint/tests/checks in Docker (via `docker-compose` or Docker images) only.
- For asset/UI changes: run build, then test both classic and minimalist widget modes.
- For settings changes: verify save/load in admin tabs and default/upgrade behavior.
- Before opening or updating a PR, run WordPress Plugin Check locally and fix findings:
  - `docker-compose run --rm wpcli plugin install plugin-check --activate`
  - `docker-compose run --rm wpcli plugin check auto-translate`
