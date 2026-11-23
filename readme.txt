=== Mloun Side Contact – Floating Contact Buttons for online chat, WeChat & Phone ===
Contributors: hackenwu
Tags: floating contact, side contact, sticky contact, whatsapp chat, wechat contact
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.4.39
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add a floating side contact bar to your website with WhatsApp, Telegram, WeChat, phone, email and 15 more channels.

== Description ==

**Mloun Side Contact** is a lightweight WordPress plugin that adds a floating side contact panel to your website. It allows visitors to quickly reach your business through multiple communication channels such as WhatsApp, WeChat, phone call, email, and custom links.

This plugin is perfect for businesses, online stores, service companies, and support teams who want to increase customer engagement and make contacting them easier.

The floating contact bar stays visible while users scroll, ensuring your contact options are always accessible.

== Key Features ==

✔ Floating side contact bar, left or right
✔ 20 channels: WhatsApp, Telegram, Phone, SMS, Email, Messenger, Instagram, Facebook, LinkedIn, X, YouTube, TikTok, Line, Viber, Microsoft Teams, Zalo, QQ, WeChat, Location and Custom Link
✔ Pick a channel and type only your number or username — the plugin builds the correct link for you
✔ Country code selector, so phone numbers are never mistyped
✔ Prefilled chat message for WhatsApp and Telegram
✔ Entrance animations and hover effects
✔ Tooltips with your own hover text
✔ QR code image support (required for WeChat)
✔ Mobile friendly responsive design
✔ Lightweight and fast loading, no external requests
✔ Works with any WordPress theme

== Why Use a Floating Contact Button? ==

A floating contact button improves user experience and helps visitors reach your business instantly. Many websites use floating contact bars to increase conversion rates and customer support efficiency.

With Mloun Side Contact, you can display important communication channels directly on your website.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/mloun-side-contact` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to the plugin settings page to configure your contact buttons.
4. Add your WhatsApp, WeChat, phone or email contact information.

== Frequently Asked Questions ==

= Does this plugin work with any theme? =

Yes. Mloun Side Contact works with most WordPress themes.

= Is the plugin mobile friendly? =

Yes. The floating contact bar automatically adapts to mobile devices.

= Can I add custom contact links? =

Yes. Choose the "Custom Link" channel and paste any URL.

= Do I need to know the exact link format for each channel? =

No. Pick a channel, enter your phone number or username, and the plugin builds the correct link. A live preview of the generated link is shown while you type, and an optional override field is available if you ever need a different URL.

= Does WeChat work with a link? =

No. WeChat cannot be opened through a link, so the WeChat channel shows your uploaded QR code image for visitors to scan.

= Can I show a prefilled message in WhatsApp or Telegram? =

Yes. Each WhatsApp and Telegram channel has its own prefilled message field. Visitors open the chat with that text already typed.

== Screenshots ==

1. Plugin settings page in WordPress admin
2. Floating side contact bar on desktop
3. Floating contact bar on mobile devices
4. Set up contact items
5. Set a custom icon
6. Custom QR code/image on desktop

== Changelog ==

= 1.4.39 =
* Fixed: the QR code image and the custom channel icon were printed with an empty alt attribute, so the ALT text set on the image in the media library never reached the page. Images now carry it, and both images are announced properly by screen readers and indexed by image search.
* Added: an optional "Image ALT Text" field per channel (under Advanced), which overrides the value from the media library for that channel's QR code and custom icon. Leave it blank to keep using the media library ALT; if that is empty too, the channel name is used.
* Note: no existing setting changes. Sites that never set an ALT anywhere keep a meaningful description instead of an empty attribute.

= 1.4.38 =
* Added: a "Mobile Layout" option. Phones keep the full-width bottom bar by default; choose "Same as desktop side rail" to keep the widget on the side and follow the desktop position, offset, size and style settings unchanged.
* Fixed: the Fold / Unfold setting had no effect on phones, because the mobile bar was always fully expanded. The two phone layouts are now handled separately: with "Same as desktop side rail" the setting works exactly like the desktop rail, while "Bottom bar (default)" ignores it completely and always lays every channel flat along the bottom with no fold button — the same look as before.
* Fixed: on phones using the desktop side rail, the fold button's "Text While Folded" / "Text While Expanded" bubble was forced above the button and got clipped by the screen edge. It now opens towards the middle of the screen like on desktop: a rail on the right shows the text on the icon's left, a rail on the left shows it on the icon's right.
* Fixed: Back to Top was permanently visible on phones, so it never faded in after scrolling down nor faded out again at the top the way it does on the desktop rail. Phones now use the same scroll threshold.
* Note: this release also re-versioned the frontend assets, so browsers pick up the fixed stylesheet and script immediately instead of serving a cached copy.
* Fixed: removed the "Tested up to" line from the plugin header. WordPress.org requires that value to live only in readme.txt, and Plugin Check reports it as an error when both places declare it.
* Fixed: on the phone bottom bar the Back to Top label was cut off at the right edge of the screen. Its bubble now anchors to the right edge and grows inward, so the whole label stays visible. No other button in the bar changed.
* Fixed: Back to Top in the "Bottom bar" mobile layout no longer follows the desktop scroll threshold - it is always visible there, as it was before. The "Same as desktop side rail" layout keeps the reveal past 20% of the page.
* Removed: the "Default Background Color" field in Step 2. It was saved but never applied — every button takes its colour from its own Icon Color field or from the channel's brand colour — so the control only served to mislead. Nothing changes visually, and a stale saved value is dropped automatically the next time the settings are saved.

= 1.4.31 =
* Changed: the bundled translation is now registered through the text-domain registry instead of the classic loader that Plugin Check flags as discouraged. Translations keep working exactly as before, and a language pack from wordpress.org still takes precedence.
* Note: bundled translations need WordPress 6.1 or newer. Older sites fall back to English.

= 1.4.30 =
Security and compatibility pass.
* Fixed: the bundled Chinese translation never loaded. The plugin shipped its .po/.mo but never registered the languages folder, so WordPress only looked at downloaded language packs.
* Fixed: the "Position" setting was not validated and was written straight into the inline stylesheet, which allowed CSS injection. Position, entrance animation, hover effect and tooltip position are now whitelisted.
* Fixed: a javascript: URL in "Link Override" reached the href and would run on click. It is now refused; add_filter( 'mlsc_allow_javascript_links', '__return_true' ) restores the old behaviour if you really need it.
* Fixed: the drag-to-reorder script was not declared as a dependency of the admin script, so print order was not guaranteed.
* Changed: Requires PHP is now 7.4, matching what WordPress itself requires.

= 1.4.29 =
* The fold / unfold label ("Contact Us" / close) is now real text instead of a CSS-generated bubble, so page translators such as TranslatePress and WPML can find and translate it.


= 1.4.28 =
* Saving the settings now shows a top-centre toast again (it silently disappeared because every admin notice was being removed on our page).
* The plugins list has a Settings link next to Deactivate.


= 1.4.27 =
* The fold button now turns into a cross while the bar is open, so it is obvious that clicking it closes the bar. The icon you picked is still used when collapsed.


= 1.4.26 =
Fixed: the live preview went blank on any site with "Hide on mobile" switched on. The preview canvas is narrower than the frontend's 768px phone breakpoint, so the per-instance CSS rule that hides the widget on phones was firing inside the preview. Both stylesheets are now neutralised the same way in desktop mode, and the live preview says "Hidden on phones" when the phone canvas legitimately hides it.

= 1.4.25 =
Fixed: the live preview could stay stuck on its placeholder skeleton. The preview request now reads the posted form from $_POST again (filter_input() returns null on some PHP-FPM/LSAPI setups, which silently emptied the preview on 1.4.23/1.4.24), and a failed request now reports the reason under the preview instead of failing silently - HTTP status, timeout or empty response - with details in the browser console.

= 1.4.24 =
Improved: the admin screens now use the Mloun brand palette. #0F91E3 blue carries structure, focus and selected states; #F06B04 orange carries the primary action (Save), counts and emphasis. Colours live in CSS custom properties at the top of admin.css, so the whole UI follows two values. Also raised text contrast on the repeat hint, the channel-count badge and inactive step labels.

= 1.4.23 =
Fixed: the live preview no longer touches the posted form with a text sanitizer, which could strip percent-escaped characters out of long URLs and custom labels. Oversized preview payloads are now rejected. Added the missing translators comment on the channel-count string, so the plugin passes WordPress.org Plugin Check with no warnings.

= 1.4.22 =
Improved: repeatable channels are now obvious. Their tile carries a permanent + and shows x2, x3... once used more than once, a hint line explains the +, and every such card has an "Add another" button at the bottom.

= 1.4.21 =
New: Custom Link can be added more than once - click the tile again for another button. The tile shows how many are in use and new cards are numbered automatically (Custom Link 2, Custom Link 3...).

= 1.4.20 =
Fixed: uploaded custom icons sat 2px right of centre and were 2px too large. They now use the same wrapper and size variable as the built-in icons.

= 1.4.19 =
Fixed: the preview was scaled down (84%) so the widget looked smaller than on the live site. The preview now renders at scale 1 - the widget matches the frontend exactly. The phone breakpoint is neutralised inside the preview instead of widening the canvas.

= 1.4.18 =
Changed: the preview column now takes 40% of the settings screen (60/40 split) so the settings get more room.
Changed: the desktop canvas is pinned just above the frontend breakpoint (780px) and scaled uniformly, so the widget keeps its real proportions and stays close to actual size.

= 1.4.17 =
Fixed: the preview no longer shrinks the widget. It renders at the panel width (1:1) with a full-height canvas, and only scales when the panel is narrower than the 800px canvas.
Fixed: on phones the floating bar covered the end of the page. The widget now reserves its own height at the bottom of the page so the footer stays readable.
New: the preview shows a light page skeleton so you can judge the widget's position.

= 1.4.16 =
Fixed: the preview rendered the mobile layout in both modes. It now renders at a real 900px (desktop) or 390px (mobile) canvas and scales it down, so the frontend breakpoints apply correctly.
Fixed: the settings screen was capped at 1200px; it now uses the full admin width.
Fixed: the wp_footer callback no longer accepts an argument, so a theme firing do_action( 'wp_footer', $value ) can no longer make the widget render nothing.
New: a status pill in the header shows whether the widget is live and how many channels it has.

= 1.4.15 =
New: icon and QR images are picked from the WordPress media library instead of pasting a URL.
New: live preview panel beside the settings that re-renders from your unsaved form, with a desktop/mobile switch.
Changed: QR code width and height moved to Widget customization, and the white QR panel is sized to the image plus 10px.
Changed: the side button follows the Buttons & Style radius.

= 1.4.14 =
Fixed: every translatable string now passes its text domain as a string literal, clearing 163 WordPress.WP.I18n.NonSingularStringLiteralDomain errors from Plugin Check.
Fixed: readme tags trimmed to the 5 allowed and the trailing comma removed.

= 1.4.13 =
Changed: the Group chat icon was replaced with higher-quality two-bubble artwork.

= 1.4.12 =
Changed: button size is now three fixed steps (Small 40/24, Medium 46/28, Large 52/34) instead of independent pixel fields, so the button and its icon always stay in proportion.
Changed: Back to Top appears after 20% of the page instead of 50%.
Changed: the Chat with dots, Translate chat and Smiley chat icons were replaced with higher-quality artwork; the Close icon was removed.

= 1.4.11 =
Changed: the icon size now follows the button size automatically (48%), so resizing a button scales its icon too. Enter 14-40 to override.
Changed: every fold-toggle icon is redrawn as a solid white glyph instead of a thin outline, so it stays legible at small sizes and never changes colour on hover.
Changed: the admin icon picker uses larger tiles with a caption under each icon and a check mark on the selected one.

= 1.4.10 =
New: ten additional chat icons for the fold toggle, all redrawn in one consistent line style.
New: the toggle bubble takes its own background and text colours and can be set to stay visible instead of appearing on hover.
Fixed: the Back to Top slot is now always reserved, so the widget no longer jumps when it appears or disappears.
Changed: the fold control is hidden on phones and the list no longer folds there — every enabled channel is shown.

= 1.4.9 =
Changed: the fold toggle is now an icon picker with seven icons instead of four layout styles, and its label is drawn as a bubble beside the button (which follows the Hover Label setting) rather than inside it.
Changed: the vertical distance setting is measured from the bottom edge, so the bar grows upward when the list unfolds and its bottom button never shifts.
Fixed: missing default keys could raise PHP notices in the asset loader.

= 1.4.8 =
New: the fold toggle is now its own control with four selectable styles (arrow + text pill, round arrow, round chat/close, text-only pill) and two editable labels. It sits directly above Back to Top, which remains the last button.

= 1.4.7 =
New: optional fold/unfold mode. Start with a single button, click to reveal every channel, and a close button folds them away. The toggle follows the selected style preset.
Fixed: Back to Top could still flash on some sites. It is now hard-locked out of the layout for the whole scroll and is only restored once the page is back at the top.

= 1.4.6 =
Fixed: on mobile with Vertical Center enabled the Back to Top button was reserved but invisible until you scrolled. It is now permanently visible on touch devices and no longer reacts to scrolling.

= 1.4.5 =
Fixed: clicking Back to Top could flicker. It now stays hidden for the whole smooth scroll and only comes back once you reach the top, with a small dead band around the 50% line so it cannot flutter.

= 1.4.4 =
Fixed: with Vertical Center enabled, the whole bar jumped whenever Back to Top appeared or disappeared.
Changed: on mobile the tooltip opens above the button, centred, instead of to the left or right.
Added: a Custom CSS Class field per channel, applied to the button link.
Changed: Link Override now accepts javascript:void(...) and #anchor targets, for third party chat widgets.

= 1.4.3 =
Changed: the tooltip now opens automatically on the opposite side of the bar, so the Tooltip Position setting is gone.
Changed: Classic has a clearly stronger shadow so it no longer looks the same as Minimal.
Changed: Back to Top is always visible on mobile instead of waiting for a scroll.
Added: per channel Show on Desktop / Mobile toggles in Advanced.

= 1.4.2 =
Fixed: the Link Override field was ignored whenever a number or username was also filled in. It now always takes priority.
Fixed: the tooltip bubble never appeared because the button's overflow:hidden clipped it.
Changed: Back to Top now sits at the bottom of the bar and follows the widget position and style settings, instead of being a separate corner button.
Back to Top no longer offers the Open in New Window and Link Override options.

= 1.4.1 =
Fixed: with the slide-out hover label, circular styles (Classic, Minimal, Outline) stretched into an ellipse instead of keeping round ends.
Fixed: the Outline style's 2px border made the button 4px taller than its set size.
Improved: the Back to Top tile is now marked as a page button rather than a chat channel.

= 1.4.0 =
Added 5 widget style presets: Classic, Minimal, Rounded, Square and Outline.
Added button size, icon size and spacing controls.
Outline style draws a white button with a brand-coloured border and icon, inverting on hover.
All sizes are driven by CSS variables, so custom CSS can override them.

= 1.3.2 =
Added a Back to Top channel: a round arrow in the bottom corner that appears after the visitor scrolls half the page, scrolls smoothly to the top and hides again.
Changed the tooltip toggle into a Hover Label mode (slide out / tooltip bubble / icon only). The old checkbox drew the label twice on top of the built-in slide-out label, which is why it looked broken.
Fixed the Custom Link channel having an empty icon.

= 1.3.1 =
Fixed: adding more than one channel only kept the last one after saving.
Changed: the two setup steps are now switchable tabs, and you can go back at any time.
Fixed: the Remove button was invisible on the channel card header.
Fixed: Line, Viber, Zalo and Messenger used incorrect icons; they now use the official brand marks.
Replaced the Skype channel with Microsoft Teams, because Skype for Consumer was retired in May 2025.

= 1.3.0 =
Rebuilt the settings screen as a two-step flow: choose your channels first, then customize the widget.
Channels now use fixed formats: pick a channel and enter only your number or username, the link is generated for you.
Added a country code selector for phone based channels.
Added a live preview of the generated link while editing.
Moved the prefilled message into each WhatsApp and Telegram channel, instead of one global setting.
WeChat now shows a QR code image because it cannot be opened by a link.
Existing configurations keep working and are migrated automatically.
Fixed PHP notices on the settings screen when saved options were missing keys.

= 1.2.0 =
Added 8 new icons: LinkedIn, Instagram, YouTube, Messenger, Line, Viber, Skype, Zalo.
Added entrance animations: Fade In, Slide In, Bounce In, Zoom In.
Added hover effects: Scale, Shake, Pulse.
Added tooltip option with left/right position.
Added WhatsApp & Telegram prefilled message support.
Tested up to WordPress 7.1.

= 1.1.0 =
Added show/hide toggle for the vertical contact button text.
Hidden unrelated admin notices on the plugin settings page.
Added function_exists guards to prevent fatal errors when multiple copies are active.
Tested up to WordPress 7.1.

= 1.0 =
Initial release.

== Upgrade Notice ==

= 1.4.26 =
Live preview was blank when "Hide on mobile" was enabled - fixed.

= 1.4.25 =
Live preview fix: no more silent skeleton.

= 1.4.24 =
New brand palette for the admin screens.

= 1.4.23 =
Plugin Check clean: preview input handling and a missing translators comment.

= 1.4.22 =
Clearer "add more" affordances for Custom Link.

= 1.4.21 =
Multiple Custom Link buttons.

= 1.4.20 =
Custom icon centring fixed.

= 1.4.19 =
Preview now matches the frontend 1:1 (no scaling).

= 1.4.18 =
Preview column at 40% with proportional widget scaling.

= 1.4.17 =
1:1 full-height preview, page skeleton, mobile bar no longer covers the page end.

= 1.4.16 =
Fixed preview device modes, full-width settings, safer footer hook, live status pill.

= 1.4.15 =
Media library pickers, live preview panel, QR sizing, side button radius.

= 1.4.14 =
Plugin Check compliance: literal text domain, 5 tags.

= 1.4.13 =
Better Group chat icon.

= 1.4.12 =
Three size presets, Back to Top at 20%, better icons.

= 1.4.11 =
Solid icons, icon size follows the button, clearer icon picker.

= 1.4.10 =
New chat icon set, bubble styling, stable Back to Top slot, no folding on phones.

= 1.4.9 =
Icon picker for the fold toggle, bubble labels, bottom-anchored layout.

= 1.4.8 =
Adds selectable styles and custom labels for the fold toggle.

= 1.4.7 =
Adds a fold/unfold mode and hardens Back to Top against flicker.

= 1.4.6 =
Back to Top is permanently visible on mobile, independent of scrolling.

= 1.4.5 =
Fixes the Back to Top flicker after clicking.

= 1.4.4 =
Fixes the centred-layout jump, moves the mobile tooltip above the button, adds a per channel CSS class and allows javascript: link overrides.

= 1.4.3 =
Auto tooltip side, a clearer Classic preset, always-visible Back to Top on mobile, and per channel device toggles.

= 1.4.2 =
Fixes Link Override being ignored and the invisible tooltip bubble; Back to Top joins the bar.

= 1.4.1 =
Fixes the stretched (elliptical) button shape on hover for the circular styles.

= 1.4.0 =
Adds 5 visual style presets plus button size, icon size and spacing controls.

= 1.3.2 =
Adds a Back to Top button and turns the tooltip toggle into a proper Hover Label mode.

= 1.3.1 =
Fixes a bug where only the last added channel was saved, adds tabbed steps, and corrects five channel icons.

= 1.3.0 =
Major update: channel based setup with fixed link formats, country code selector, live link preview and per channel prefilled messages.

= 1.2.0 =
New version with 8 new icons, entrance animations, hover effects, tooltips, and chat prefill messages.

= 1.1.0 =
New version with contact text toggle, admin notice cleanup, duplicate plugin conflict protection, and WordPress 7.1 compatibility.

= 1.0 =
First release of Mloun Side Contact plugin.
