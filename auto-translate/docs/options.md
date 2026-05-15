# Options Reference

All options are prefixed with `wpat_`.

## Language Settings
- `wpat_base_language` (string)
  - Base/original site language.
  - Sanitizer: `sanitize_base_language()`
- `wpat_supported_languages` (array<string>)
  - Selected languages or `['all']`.
  - Sanitizer: `sanitize_supported_languages()`

## Visual Settings (Classic)
- `wpat_widget_type` (`classic|minimalist`)
- `wpat_button_icon` (dashicon class)
- `wpat_show_icon` (`on|''`)
- `wpat_color_1`, `wpat_color_2` (hex colors)
- `wpat_widget_size` (`small|large`)
- `wpat_border_radius` (int)
- `wpat_border_thickness` (int)
- `wpat_border_color` (hex color)
- `wpat_font_color` (hex color)
- `wpat_font_family` (string)
- `wpat_dropdown_shadow` (`on|''`)
- `wpat_dropdown_border_thickness` (int)
- `wpat_dropdown_border_color` (hex color)
- `wpat_dropdown_background_color` (hex color)
- `wpat_dropdown_hover_color` (hex color)
- `wpat_dropdown_font_hover_color` (hex color)
- `wpat_dropdown_font_selected_color` (hex color)
- `wpat_dropdown_font_color` (hex color)
- `wpat_dropdown_font_family` (string)

## Visual Settings (Minimalist)
- `wpat_min_style` (`flags|flat_flags|icon|clean`)
- `wpat_min_icon` (dashicon class)
- `wpat_min_txt_display` (`name|name_code|code`)
- `wpat_min_txt_underline` (`wpat_min_txt_underline|''`)
- `wpat_min_border_thickness` (int)
- `wpat_min_border_color` (hex color)
- `wpat_min_background_color` (hex color)
- `wpat_min_font_color` (hex color)
- `wpat_min_font_family` (string)
- `wpat_min_hover_color` (hex color)
- `wpat_min_font_hover_color` (hex color)
- `wpat_min_chevron` (`dashicons-arrow-down-alt2|dashicons-arrow-down|dashicons-arrow-down-none`)

## Advanced Settings
- `wpat_default_location` (bool)
- `wpat_auto_detect` (`enabled|disabled`)
- `wpat_show_in_menu` (classic menu ID, `menu:{id}`, `navigation:{id}`, or legacy menu location key until re-saved)
- `wpat_custom_css` (string)
- `wpat_delete_data_on_uninstall` (`on|''`)

## Versioning / Upgrade
- `wpat_auto_translate_version`
  - Written by `Auto_Translate_Activator::add_options()`.
  - Read by `Auto_Translate_Admin::check_version()` to run idempotent option backfill on upgrades.

## Legacy Option Keys
- `wpat_size`
  - legacy key read during defaults migration and removed on uninstall cleanup.
