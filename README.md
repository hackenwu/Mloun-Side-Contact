# Mloun Side Contact

A floating contact bar for WordPress — WhatsApp, WeChat, Telegram, phone, email and 20+ channels, docked to the side or the bottom edge of the page.

This repository is the **free version** source (also published on [WordPress.org](https://wordpress.org/plugins/mloun-side-contact-floating-contact-buttons-for-online-chat-wechat-phone/)). The commercial edition, **Mloun Side Contact PRO**, adds business hours, per-page display rules, pre-filled inquiry messages, a leave-a-message form and click analytics — see [mloun.com](https://mloun.com/product/mloun-side-contact-pro/).

## Features

- **24 channels** — WhatsApp, WeChat, Telegram, Messenger, Zalo, Viber, LINE, KakaoTalk, email, phone, SMS, custom links, back-to-top
- **Two layouts** — vertical side rail (folds open) or full-width bottom bar; desktop and mobile are configured separately
- **QR panels** — for channels that cannot take a text URL (WeChat, and anything with a scan code)
- **Full styling** — icon or text buttons, three bar sizes, brand colours, per-channel colours, label expand direction
- **Translatable** — every label is a real text node, so TranslatePress, WPML and Polylang can pick it up; Chinese language pack included
- **No jQuery, no build step** — vanilla JS and CSS, loads on the front end only

## Installation

### From WordPress.org

1. In wp-admin go to **Plugins → Add New**
2. Search for *Mloun Side Contact*
3. Install and activate, then open **Side Contact** in the admin menu

### From this repository

```bash
git clone https://github.com/hackenwu/Mloun-Side-Contact.git
cp -r Mloun-Side-Contact /path/to/wp-content/plugins/mloun-side-contact
```

Or zip the folder and upload it under **Plugins → Add New → Upload Plugin**:

```bash
zip -r mloun-side-contact.zip . -x "tests/*" -x "*.py" -x ".git/*"
```

## Requirements

| | |
|---|---|
| WordPress | 5.0 or newer |
| PHP | 7.4 or newer |
| WooCommerce | optional — only adds product/cart/checkout page types |

## Development

The plugin is plain PHP, CSS and JavaScript — there is no transpiler and no `node_modules`. `mloun-side-contact.php` is the single entry point and holds the channel definitions, the settings screen and the front-end renderer.

```
mloun-side-contact.php    plugin bootstrap, settings, channel registry, renderer
admin/admin.css|js        settings screen assets
assets/css/frontend.css   the widget itself
assets/js/frontend.js     fold/unfold, QR panels, click tracking hooks
languages/                .pot, zh_CN .po/.mo
tests/                    PHP regression suites (one script per fix group)
build-i18n.py             rebuilds the .pot/.po/.mo from the source
build-zip.py              builds the distributable zip
run-tests.py              runs every suite + php -l + version consistency
```

### Running the tests

```bash
python run-tests.py          # all suites, lint and version checks
python run-tests.py --quiet  # summary only
```

Each file in `tests/` is a standalone PHP script that takes the main plugin file as `argv[1]`; the runner finds PHP through `$MLSC_PHP`, then the usual install locations.

### Rebuilding the language pack

```bash
python build-i18n.py         # regenerates .pot, zh_CN.po and zh_CN.mo
```

## Notes on the renderer

- Channel links are built per channel type (`wa.me`, `mailto:`, `tel:`, `weixin://`, custom), and every channel element carries `data-mlsc-channel` so scripts and analytics never have to guess the channel from the URL.
- Labels are printed as text nodes (`.mlsc-tip`) instead of CSS `content: attr()`, which is what makes them visible to page translators.
- Styles are namespaced under `.mlsc` and use CSS custom properties (`--mlsc-blue`, `--mlsc-orange`, `--mlsc-size`, …) so a theme can retheme the bar without touching the plugin.

## License

GPLv2 or later — see [LICENSE](LICENSE).
