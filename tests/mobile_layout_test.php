<?php

define('ABSPATH', __DIR__ . '/');

$GLOBALS['__options'] = array();

function add_action(){} function add_filter(){} function apply_filters($h,$v){ return 'mlsc_allow_javascript_links' === $h && ! empty( $GLOBALS['__allow_js'] ) ? true : $v; }

function plugin_dir_path($f){ return dirname($f).'/'; } function plugin_dir_url($f){ return ''; }

function plugins_url($p='',$f=''){ return ''; } function plugin_basename($f){ return ''; }

function get_option($k,$d=false){ return array_key_exists($k,$GLOBALS['__options'])?$GLOBALS['__options'][$k]:$d; }

function update_option(){ return true; }

function esc_html($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }

function esc_attr($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }

function esc_textarea($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }

function esc_js($s){ return (string)$s; } function esc_url($s){ return $s; } function esc_url_raw($s){ return trim((string)$s); }

function esc_html__($s,$d=null){ return esc_html($s); } function esc_html_e($s,$d=null){ echo esc_html($s); }

function esc_attr__($s,$d=null){ return esc_attr($s); } function esc_attr_e($s,$d=null){ echo esc_attr($s); }

function __($s,$d=null){ return $s; }

function _n($s,$p,$n,$d=null){ return 1==$n?$s:$p; } function _x($s,$c,$d=null){ return $s; }

 function _e($s,$d=null){ echo $s; }

function wp_kses($s,$a){ return $s; } function sanitize_text_field($s){ return is_string($s)?trim(strip_tags($s)):''; }

function sanitize_textarea_field($s){ return sanitize_text_field($s); }

function sanitize_html_class($c, $fallback=''){ $c = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$c); return $c === '' ? $fallback : $c; }

function sanitize_email($s){ $s=trim((string)$s); return filter_var($s,FILTER_VALIDATE_EMAIL)?$s:''; }

function sanitize_hex_color($s){ $s=trim((string)$s); return preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/',$s)?$s:null; }

function wp_parse_args($a,$d){ if(!is_array($a))$a=array(); return array_merge($d,$a); }

function checked($a,$b=true,$e=true){ $r=((string)$a===(string)$b)?' checked="checked"':''; if($e)echo $r; return $r; }

function selected($a,$b=true,$e=true){ $r=((string)$a===(string)$b)?' selected="selected"':''; if($e)echo $r; return $r; }

function wp_enqueue_style(){} function wp_enqueue_script(){} function wp_register_script(){} function wp_localize_script(){}

function wp_add_inline_script(){} function wp_add_inline_style(){} function wp_json_encode($d){ return json_encode($d); }

function current_user_can(){ return true; } function settings_errors(){} function submit_button(){}

function wp_nonce_field(){} function settings_fields(){} function admin_url(){ return ''; }

function get_locale(){ return 'zh_CN'; } function absint($v){ return abs(intval($v)); }

function load_plugin_textdomain(){} function is_admin(){ return true; } function remove_all_actions(){}

function add_menu_page(){} function add_submenu_page(){} function register_setting(){}

function wp_strip_all_tags($s){ return strip_tags((string)$s); }
require $argv[1];
$fail = 0;
function chk($n,$g,$w){ global $fail; $ok=((string)$g===(string)$w); printf("  [%s] %s\n",$ok?'PASS':'FAIL',$n); if(!$ok){echo "        got : ".var_export($g,true)."\n        want: ".var_export($w,true)."\n"; $fail++;} }



/* ============================================================
   Mobile layout option: bottom bar (default) vs desktop side rail
   ============================================================ */

echo "=== 1. DEFAULTS ===\n";
$def = mlsc_get_defaults();
chk('mobile_layout defaults to bottom', $def['mobile_layout'], 'bottom');

echo "\n=== 2. SANITIZER ===\n";
$s = mlsc_sanitize_options( array( 'mobile_layout' => 'side' ) );
chk('side is kept', $s['mobile_layout'], 'side');
$s = mlsc_sanitize_options( array( 'mobile_layout' => 'bottom' ) );
chk('bottom is kept', $s['mobile_layout'], 'bottom');
$s = mlsc_sanitize_options( array( 'mobile_layout' => 'top' ) );
chk('unknown value falls back to bottom', $s['mobile_layout'], 'bottom');
$s = mlsc_sanitize_options( array( 'mobile_layout' => 'side";} .evil{ x:' ) );
chk('injection attempt falls back to bottom', $s['mobile_layout'], 'bottom');
$s = mlsc_sanitize_options( array() );
chk('missing value falls back to bottom', $s['mobile_layout'], 'bottom');

echo "\n=== 3. RENDER ===\n";
$base = array(
    'items'         => array( array( 'channel' => 'custom', 'value' => 'https://example.com', 'name' => 'Chat', 'color' => '#25D366' ) ),
    'contact_text'  => '', 'show_contact_text' => 0, 'contact_link' => 'x',
    'position'      => 'right', 'hover_label' => 'slide', 'widget_style' => 'classic',
);

$GLOBALS['__options']['mlsc_options'] = $base + array( 'mobile_layout' => 'side' );
ob_start(); mlsc_render_frontend(); $h_side = ob_get_clean();
chk('side adds mlsc-mobile-side', ( false !== strpos( $h_side, 'mlsc-mobile-side' ) ) ? 'yes' : 'no', 'yes');

$GLOBALS['__options']['mlsc_options'] = $base + array( 'mobile_layout' => 'bottom' );
ob_start(); mlsc_render_frontend(); $h_bottom = ob_get_clean();
chk('bottom omits mlsc-mobile-side', ( false === strpos( $h_bottom, 'mlsc-mobile-side' ) ) ? 'none' : 'PRESENT', 'none');

$GLOBALS['__options']['mlsc_options'] = $base; // no key at all (older install)
ob_start(); mlsc_render_frontend(); $h_legacy = ob_get_clean();
chk('legacy options without the key stay on the bottom bar', ( false === strpos( $h_legacy, 'mlsc-mobile-side' ) ) ? 'none' : 'PRESENT', 'none');

echo "\n=== 4. HIDE-ON-MOBILE STILL WORKS IN BOTH LAYOUTS ===\n";
$GLOBALS['__options']['mlsc_options'] = $base + array( 'mobile_layout' => 'side', 'mobile_hide' => 1 );
ob_start(); mlsc_render_frontend(); $h_hide = ob_get_clean();
chk('mobile_hide class emitted alongside side', ( false !== strpos( $h_hide, 'mlsc-mobile-hide' ) ) ? 'yes' : 'no', 'yes');

echo "\n=== 5. CSS IS ACTUALLY SCOPED ===\n";
$css_path = __DIR__ . '/../mloun-side-contact/assets/css/frontend.css';
chk('frontend.css is readable', is_readable( $css_path ) ? 'yes' : 'no', 'yes');
$css = (string) file_get_contents( $css_path );

// The bottom-bar block must exclude the side layout on every one of its rules.
$bar_rules = substr_count( $css, '.mlsc:not(.mlsc-mobile-hide):not(.mlsc-mobile-side)' );
chk('bottom-bar rules all exclude the side layout', $bar_rules, 9);

// Not a single bottom-bar rule may still match a naked :not(.mlsc-mobile-hide).
chk('no unguarded bottom-bar rule left',
    substr_count( $css, '.mlsc:not(.mlsc-mobile-hide) ' ) + substr_count( $css, '.mlsc:not(.mlsc-mobile-hide) {' ) + substr_count( $css, '.mlsc:not(.mlsc-mobile-hide) li' ), 0);

// The mobile-only tooltip placement must not leak into the side layout.
chk('mobile tooltip rules are scoped too',
    substr_count( $css, '.mlsc:not(.mlsc-mobile-side) [data-mlsc-tooltip]' ), 6);

// Desktop tooltip rules must stay untouched (still plain .mlsc, single selector, no comma).
chk('desktop tooltip rules untouched',
    ( false !== strpos( $css, '.mlsc [data-mlsc-tooltip][data-mlsc-tooltip-pos="left"]::after {' ) ) ? 'yes' : 'no', 'yes');

echo "\n=== 6. ADMIN FIELD ===\n";
$GLOBALS['__options']['mlsc_options'] = $base + array( 'mobile_layout' => 'side' );
ob_start(); mlsc_settings_page(); $pg = ob_get_clean();
chk('Mobile Layout field present', ( false !== strpos( $pg, 'Mobile Layout' ) ) ? 'yes' : 'no', 'yes');
chk('field name wired', ( false !== strpos( $pg, 'mlsc_options[mobile_layout]' ) ) ? 'yes' : 'no', 'yes');
chk('bottom option rendered', ( false !== strpos( $pg, 'value="bottom"' ) ) ? 'yes' : 'no', 'yes');
chk('side option rendered', ( false !== strpos( $pg, 'value="side"' ) ) ? 'yes' : 'no', 'yes');
chk('side is the selected option when saved',
    ( 1 === preg_match( '/value="side"\s+selected=/', $pg ) ) ? 'yes' : 'no', 'yes');
chk('bottom is not selected when side is saved',
    ( 1 === preg_match( '/value="bottom"\s+selected=/', $pg ) ) ? 'PRESENT' : 'no', 'no');

echo "\n=== 7. FOLD: SIDE RAIL HONOURS IT, BOTTOM BAR IGNORES IT ===\n";
// The markup stays layout-independent (it always carries the saved fold state);
// the split between the two phone layouts lives in CSS.
foreach ( array( 'bottom', 'side' ) as $layout ) {
    $GLOBALS['__options']['mlsc_options'] = array_merge( $base, array( 'mobile_layout' => $layout, 'collapse_mode' => 'closed' ) );
    ob_start(); mlsc_render_frontend(); $hf = ob_get_clean();
    chk( $layout . ': wrapper carries the folded state', ( false !== strpos( $hf, 'mlsc-collapsed' ) ) ? 'yes' : 'no', 'yes' );
    chk( $layout . ': open face is not also set', ( false === strpos( $hf, 'mlsc-open' ) ) ? 'none' : 'PRESENT', 'none' );

    $GLOBALS['__options']['mlsc_options'] = array_merge( $base, array( 'mobile_layout' => $layout, 'collapse_mode' => 'off' ) );
    ob_start(); mlsc_render_frontend(); $hx = ob_get_clean();
    chk( $layout . ': off renders no toggle', ( false === strpos( $hx, 'mlsc-toggle-item' ) ) ? 'none' : 'PRESENT', 'none' );
}

// The split itself.
chk('bottom bar hides the fold toggle',
    preg_match( '/@media \(max-width: 768px\) \{\s*\.mlsc:not\(\.mlsc-mobile-side\) li\.mlsc-toggle-item \{[^}]*display:\s*none !important/', $css ) ? 'yes' : 'no', 'yes' );
chk('bottom bar forces every channel flat',
    preg_match( '/\.mlsc:not\(\.mlsc-mobile-side\)\.mlsc-collapsed li:not\(\.mlsc-toggle-item\):not\(\.mlsc-totop-item\)[^{]*\{[^}]*display:\s*block !important/', $css ) ? 'yes' : 'no', 'yes');
chk('flat override keeps per-channel mobile hiding',
    ( false !== strpos( $css, ':not(.mlsc-hide-mobile):not(.contact-item)' ) ) ? 'yes' : 'no', 'yes');
chk('side rail is not force-flattened',
    ( false === strpos( $css, '.mlsc-mobile-side.mlsc-collapsed li:not' ) ) ? 'none' : 'PRESENT', 'none');
chk('side rail still falls back to the generic fold rule',
    preg_match( '/^\.mlsc\.mlsc-collapsed li:not\(\.mlsc-toggle-item\):not\(\.mlsc-totop-item\) \{/m', $css ) ? 'yes' : 'no', 'yes');
chk('admin help explains the split',
    ( false !== strpos( $pg, 'only applies to the side rail' ) ) ? 'yes' : 'no', 'yes');

echo "\n=== 8. FOLD BUBBLE KEEPS DESKTOP SIDE PLACEMENT ON THE PHONE SIDE RAIL (v1.4.32) ===\n";
// The bubble must open towards the middle of the screen: icon on the right ->
// text on its left, icon on the left -> text on its right. The phone override
// that forces it above+centred must therefore skip the side-rail layout.
chk('phone bubble override skips the side rail',
    preg_match('/@media \(max-width: 768px\) \{\s*\.mlsc:not\(\.mlsc-mobile-side\) li\.mlsc-toggle-item \.mlsc-tip/', $css) ? 'yes' : 'no', 'yes');
// Every selector line of that phone bubble block must carry the scope, otherwise
// the rail layout silently loses its desktop placement again.
$bubble_marker = strpos( $css, 'the bubble belongs above the button' );
$bubble_seg    = ( false !== $bubble_marker ) ? substr( $css, $bubble_marker ) : '';
$unscoped      = 0;
foreach ( explode( "\n", $bubble_seg ) as $ln ) {
    if ( false !== strpos( $ln, 'mlsc-tip' ) && false !== strpos( $ln, '{' ) && false === strpos( $ln, ':not(.mlsc-mobile-side)' ) ) {
        $unscoped++;
    }
}
chk('bubble override block found', ( '' !== $bubble_seg ) ? 'yes' : 'no', 'yes');
chk('every phone bubble override is scoped', $unscoped, 0);
chk('desktop left-handed bubble rule untouched',
    ( false !== strpos( $css, '.mlsc li.mlsc-toggle-item .item-inner[data-mlsc-tooltip-pos="left"] .mlsc-tip {' ) ) ? 'yes' : 'no', 'yes');
chk('desktop right-handed bubble rule untouched',
    ( false !== strpos( $css, '.mlsc li.mlsc-toggle-item .mlsc-tip-closed,' ) ) ? 'yes' : 'no', 'yes');

// Right-hand rail -> bubble opens to the LEFT; left-hand rail -> opens RIGHT.
// array_merge, not `+`: $base already carries position=right and `+` keeps the
// left-hand operand, which silently cancelled the override.
$GLOBALS['__options']['mlsc_options'] = array_merge( $base, array( 'mobile_layout' => 'side', 'collapse_mode' => 'open', 'position' => 'right' ) );
ob_start(); mlsc_render_frontend(); $hr = ob_get_clean();
chk('rail on the right opens the bubble left', ( false !== strpos( $hr, 'data-mlsc-tooltip-pos="left"' ) ) ? 'yes' : 'no', 'yes');

$GLOBALS['__options']['mlsc_options'] = array_merge( $base, array( 'mobile_layout' => 'side', 'collapse_mode' => 'open', 'position' => 'left' ) );
ob_start(); mlsc_render_frontend(); $hl = ob_get_clean();
chk('rail on the left opens the bubble right', ( false !== strpos( $hl, 'data-mlsc-tooltip-pos="right"' ) ) ? 'yes' : 'no', 'yes');

echo "\n=== 9. BACK TO TOP USES THE SAME SCROLL THRESHOLD ON PHONES (v1.4.32) ===\n";
// Phones used to force it permanently visible with !important, which defeated the
// script's scroll threshold — it never faded out at the top nor in on scroll.
chk('no rule forces Back to Top permanently visible',
    preg_match('/mlsc-totop-item[^{]*\{[^}]*visibility:\s*visible\s*!important/', $css) ? 'PRESENT' : 'none', 'none');
chk('base rule keeps the slot reserved',
    preg_match('/\.mlsc li\.mlsc-totop-item \{[^}]*visibility:\s*hidden/', $css) ? 'yes' : 'no', 'yes');
chk('.is-visible is the only reveal path',
    preg_match('/\.mlsc li\.mlsc-totop-item\.is-visible \{[^}]*visibility:\s*visible/', $css) ? 'yes' : 'no', 'yes');

$fjs = dirname( $argv[1] ) . '/assets/js/frontend.js';
chk('script still drives .is-visible',
    ( false !== strpos( (string) file_get_contents( $fjs ), "classList.add('is-visible')" ) ) ? 'yes' : 'no', 'yes');

// The first phone block must not special-case Back to Top any more.
$phone = '';
if ( preg_match( '/@media \(max-width: 768px\) \{(.*?)\n\}/s', $css, $pm ) ) {
    $phone = $pm[1];
}
chk('phone block no longer special-cases Back to Top',
    ( false === strpos( $phone, 'mlsc-totop-item' ) ) ? 'none' : 'PRESENT', 'none');

echo "\n=== 10. BACK TO TOP LOGIC IS VIEWPORT-AGNOSTIC IN JS ===\n";
// The CSS fix alone was not enough: update() used to pin the button visible on
// phones and the click handler skipped its sticky hide. Both are gone.
$fjsrc = (string) file_get_contents( $fjs );
chk('script is readable', ( '' !== $fjsrc ) ? 'yes' : 'no', 'yes');
chk('no mobile short-circuit in the scroll update',
    preg_match('/if \(isMobile\(\)\) \{\s*lastY = y;\s*if \(!hiding\) \{\s*setVisible\(true\);/', $fjsrc) ? 'PRESENT' : 'none', 'none');
chk('click handler is not guarded by isMobile',
    ( false === strpos( $fjsrc, 'if (!isMobile()) {' ) ) ? 'none' : 'PRESENT', 'none');
chk('click always locks the button out',
    ( false !== strpos( $fjsrc, 'setLocked(true);' ) ) ? 'yes' : 'no', 'yes');
chk('threshold constants still drive the reveal',
    ( false !== strpos( $fjsrc, 'SCROLL_THRESHOLD = 0.2' ) && false !== strpos( $fjsrc, 'HYSTERESIS = 0.03' ) ) ? 'yes' : 'no', 'yes');
chk('one helper owns the bottom-bar test',
    preg_match( '/function isPhoneBottomBar\(bar\)\s*\{[^}]*isMobile\(\)[^}]*mlsc-mobile-side[^}]*\}/s', $fjsrc ) ? 'yes' : 'no', 'yes');
chk('body padding skips the side rail',
    ( false !== strpos( $fjsrc, 'isPhoneBottomBar( bar )' ) || false !== strpos( $fjsrc, 'isPhoneBottomBar(bar)' ) ) ? 'yes' : 'no', 'yes');

echo "\n=== 9b. BACK TO TOP: ALWAYS ON IN THE BOTTOM BAR, THRESHOLD ON THE RAIL ===\n";
// The bottom bar is a flat row: its Back to Top is permanent. The side rail keeps
// the desktop 20%-scroll reveal. Both are decided by isPhoneBottomBar().
// Patterns are whitespace-tolerant on purpose: the script is written compact-C
// style (setLocked(false)), not WordPress style (setLocked( false )).
chk('update() asks the layout helper first',
    preg_match( '/function update\(\).*?if \(\s*phoneBottomBar\(\)\s*\)/s', $fjsrc ) ? 'yes' : 'no', 'yes');
chk('pinned branch forces the button visible',
    preg_match( '/if \(\s*phoneBottomBar\(\)\s*\)\s*\{\s*hiding\s*=\s*false;\s*setLocked\(\s*false\s*\);\s*setVisible\(\s*true\s*\);/s', $fjsrc ) ? 'yes' : 'no', 'yes');
chk('pinned branch returns before the threshold maths',
    ( preg_match( '/if \(\s*phoneBottomBar\(\)\s*\)\s*\{[^}]*return;/s', $fjsrc ) === 1
      && strpos( $fjsrc, 'phoneBottomBar()' ) < strpos( $fjsrc, 'var ratio = max > 0' ) ) ? 'yes' : 'no', 'yes');
chk('pinned branch clears a stale lock',
    preg_match( '/if \(\s*phoneBottomBar\(\)\s*\)\s*\{[^}]*setLocked\(\s*false\s*\)/s', $fjsrc ) ? 'yes' : 'no', 'yes');
chk('click on the bottom bar does not hide it',
    preg_match( '/addEventListener\(\s*.click.,\s*function \(\s*event\s*\)\s*\{\s*event\.preventDefault\(\);.*?if \(\s*phoneBottomBar\(\)\s*\)\s*\{\s*scrollToTop\(\);\s*return;/s', $fjsrc ) ? 'yes' : 'no', 'yes');
chk('side rail still locks itself out after a click',
    preg_match( '/if \(\s*phoneBottomBar\(\)\s*\)\s*\{\s*scrollToTop\(\);\s*return;\s*\}.*?hiding\s*=\s*true;/s', $fjsrc ) ? 'yes' : 'no', 'yes');
chk('side rail still runs the ratio reveal',
    ( false !== strpos( $fjsrc, 'var want = shown' ) && false !== strpos( $fjsrc, 'setVisible(want)' ) ) ? 'yes' : 'no', 'yes');
chk('no leftover viewport-wide short-circuit',
    strpos( $fjsrc, 'isMobile() && bar && !bar.classList.contains' ) === false ? 'none' : 'PRESENT', 'none');

echo "\n=== 10. PHONE BOTTOM BAR: BACK TO TOP BUBBLE STAYS ON SCREEN ===\n";
// The bar is a full-width row and Back to Top is its last button, so a bubble
// centred on it hung past the right edge and got clipped. Only that one bubble
// is re-anchored; every other button in the bar keeps the centred bubble.
$totop_pos   = strpos( $css, 'v1.4.38' );
$totop_block = ( false !== $totop_pos ) ? substr( $css, $totop_pos ) : '';
chk('back-to-top bubble rule exists', '' !== $totop_block ? 'yes' : 'no', 'yes');
chk('bubble anchored to the right edge',
    ( false !== strpos( $totop_block, 'right: 0;' ) && false !== strpos( $totop_block, 'left: auto;' ) ) ? 'yes' : 'no', 'yes');
chk('rule scoped to the back-to-top item',
    ( false !== strpos( $totop_block, 'li.mlsc-totop-item [data-mlsc-tooltip]::after' ) ) ? 'yes' : 'no', 'yes');
chk('rule scoped to the bottom-bar layout only',
    ( false !== strpos( $totop_block, ':not(.mlsc-mobile-side)' ) ) ? 'yes' : 'no', 'yes');
chk('caret follows the bubble', ( false !== strpos( $totop_block, 'right: 12px;' ) ) ? 'yes' : 'no', 'yes');

echo "\nFAILURES: $fail\n";
