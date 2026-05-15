# Architecture

## High-Level Runtime
1. WordPress loads [`auto-translate/auto-translate.php`](../auto-translate/auto-translate.php).
2. Bootstrap loads config/language helpers and instantiates `Auto_Translate`.
3. `Auto_Translate` wires hooks through `Auto_Translate_Loader`.
4. Admin hooks register settings, admin UI, widgets, and version migration checks.
5. Public hooks enqueue frontend assets, print translator bootstrap JS/CSS, and render UI in footer/menu/shortcode.

## Core Components
- `auto-translate/auto-translate.php`:
  - plugin metadata and lifecycle hooks
  - bootstrap glue code
- `auto-translate/includes/class-auto-translate.php`:
  - central orchestration
  - admin/public hook registration
- `auto-translate/includes/class-auto-translate-loader.php`:
  - delayed registration of actions/filters/shortcodes
- `auto-translate/admin/class-auto-translate-admin.php`:
  - Settings API registrations and sanitizers
  - admin menu and settings page renderer
- `auto-translate/public/class-auto-translate-public.php`:
  - frontend assets and translator setup
  - output locations (footer, menu, shortcode)
  - legacy-aware menu matching for classic menu IDs, block navigation IDs, and older location-key settings

## Public Rendering Paths
- Default floating button:
  - `wp_footer` -> `hook_content_translator()`
- Menu injection:
  - `wp_nav_menu_items` -> `hook_menu_item()`
  - `render_block_core/navigation` -> `hook_navigation_block()`
- Shortcode:
  - `[auto_translate_button]` -> `auto_translate_button_function()`

## Boot Gating
`Auto_Translate_Public::should_boot_translator()` gates frontend script/style bootstrap to pages that can actually render the translator UI:
- default location enabled
- a menu injection target is configured
- current singular content includes shortcode
- widget instance is active

This reduces unnecessary third-party script loading.
