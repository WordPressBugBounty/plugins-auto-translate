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

## Build And Dev Commands
- Install deps: `npm install`
- Build assets: `npm run build` (same as `gulp build`)
- Watch assets: `npx gulp watch`
- Start local WP stack: `docker-compose up`
- Start local WP stack (detached): `docker-compose up -d`
- If local Node/npm is incompatible with legacy `node-sass`, build in Docker:
  `docker run --rm -u "$(id -u):$(id -g)" -e npm_config_cache=/tmp/.npm -v "$PWD":/app -w /app node:14-buster bash -lc "npm install && npm run build"`
- Run WP-CLI inside Docker (no host PHP needed): `docker-compose run --rm wpcli --info`
- Install and run Plugin Check in Docker:
  `docker-compose run --rm wpcli plugin install plugin-check --activate`
  `docker-compose run --rm wpcli plugin check auto-translate`

WordPress runs on `http://localhost:8080` and mounts the local plugin folder into `wp-content/plugins/auto-translate`.

## Editing Rules
- Edit source files in `src/**` for JS/SCSS changes.
- Do **not** hand-edit compiled minified assets under:
- `auto-translate/admin/js|css`
- `auto-translate/public/js|css`
- `auto-translate/global/js|css`
- After asset changes, run `npm run build` and include generated outputs in commit.
- Keep WordPress option keys prefixed with `wpat_`.
- Preserve backward compatibility for saved options; this plugin has upgrade-sensitive defaults in `Auto_Translate_Activator::add_options()`.

## Settings And Data Model
- Options are registered in `auto-translate/admin/class-auto-translate-admin.php`.
- Activation defaults and version migration behavior live in `auto-translate/includes/class-auto-translate-activator.php`.
- Language catalog and country mapping are defined in `auto-translate/includes/config.php`.
- Public language metadata helper: `Auto_Translate_Languages`.

## Release Checklist
When bumping versions, update all of:
- `auto-translate/README.txt` (`Stable tag`, changelog)
- `auto-translate/auto-translate.php` (`Version` header and `AUTO_TRANSLATE_VERSION`)
- `package.json` (`version`)
- `CHANGELOG.md`

## Known Pitfalls
- `Auto_Translate_Loader::add_shortcode()` currently builds shortcode storage from `$this->filters` instead of `$this->shortcodes`. If touching loader internals, verify shortcode registration behavior carefully.
- `src/admin/scripts/admin.js` uses switch fall-through in some cases (no `break`), which may be intentional for chained style updates. Avoid inserting breaks without checking preview behavior.
- Plugin depends on external Google Translate script injection in `wp_head`; avoid moving that unless you test full frontend translation flow.

## Validation Before Finishing
- For PHP changes: load plugin settings page and frontend in local WP, check for PHP notices/warnings.
- For asset/UI changes: run build, then test both classic and minimalist widget modes.
- For settings changes: verify save/load in admin tabs and default/upgrade behavior.
