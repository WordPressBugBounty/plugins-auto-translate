# Development

## Prerequisites
- Node.js 20 LTS
- Docker / Docker Compose

Host PHP is intentionally not required for this repository.

## Install
```bash
npm install
```

## Build Assets
```bash
npm run build
```

Watch mode:
```bash
npx gulp watch
```

## Run Local WordPress
```bash
docker-compose up -d
```

WordPress: [http://localhost:8080](http://localhost:8080)

Bootstrap WordPress install + plugin activation in one step (safe to rerun):
```bash
npm run wp:bootstrap
```

The script:
- starts `db` + `wordpress` containers
- installs WordPress only when missing
- activates `auto-translate`

Defaults can be overridden with env vars:
- `SITE_URL`
- `SITE_TITLE`
- `ADMIN_USER`
- `ADMIN_PASSWORD`
- `ADMIN_EMAIL`

## Validation (Docker-Only for PHP)
PHP lint:
```bash
docker run --rm -v "$PWD":/app -w /app php:8.2-cli sh -lc "find auto-translate -name '*.php' -print0 | xargs -0 -n1 php -l"
```

JS lint and coverage thresholds:
```bash
npm run lint:js
npm run test:js:coverage
npm run test:js:changed
```

Current JS global thresholds (`c8`):
- `lines >= 35%`
- `branches >= 25%`
- `functions >= 35%`
- `statements >= 35%`

Changed JS files under `src/**` (instrumented set) must keep:
- `line coverage >= 80%`

PHP quality and coverage thresholds:
```bash
npm run lint:php
npm run test:php
```

Current PHP threshold (`phpunit` + clover gate):
- `line coverage >= 90%` for covered core include files

Plugin Check:
```bash
docker-compose run --rm wpcli plugin install plugin-check --activate
docker-compose run --rm wpcli plugin check auto-translate
```

JS/PHP test scripts:
```bash
npm test
```

## Editing Rules
- Edit source assets under `src/**`.
- Do not hand-edit minified outputs under:
  - `auto-translate/admin/js|css`
  - `auto-translate/public/js|css`
  - `auto-translate/global/js|css`
- Rebuild after source asset changes.
