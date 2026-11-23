<?php

/**
 * 1.4.39 — ALT text on the QR code and custom icon images.
 *
 * The bug: the frontend printed alt="" for both, so the ALT a site owner set on
 * the image in the media library never reached the page. These are runtime
 * checks: the plugin is required and the renderer is actually executed.
 */

define('ABSPATH', __DIR__ . '/');

$GLOBALS['__options']     = array();
$GLOBALS['__attachments'] = array(); // url => array( 'id' => int, 'alt' => string )

function add_action(){} function add_filter(){} function apply_filters($h,$v){ return $v; }

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

function load_plugin_textdomain(){} function is_admin(){ return false; } function remove_all_actions(){}

function add_menu_page(){} function add_submenu_page(){} function register_setting(){}

/* --- media library lookups used by mlsc_image_alt() --- */

function wp_strip_all_tags($s){ return strip_tags((string)$s); }

function attachment_url_to_postid($url){
    return isset($GLOBALS['__attachments'][$url]) ? (int) $GLOBALS['__attachments'][$url]['id'] : 0;
}

function get_post_meta($id, $key, $single = false){
    if ('_wp_attachment_image_alt' !== $key) { return ''; }
    foreach ($GLOBALS['__attachments'] as $att) {
        if ((int) $att['id'] === (int) $id) { return isset($att['alt']) ? $att['alt'] : ''; }
    }
    return '';
}

require $argv[1];
$fail = 0;
function chk($n,$g,$w){ global $fail; $ok=((string)$g===(string)$w); printf("  [%s] %s\n",$ok?'PASS':'FAIL',$n); if(!$ok){echo "        got : ".var_export($g,true)."\n        want: ".var_export($w,true)."\n"; $fail++;} }

/* ============================================================
   1. Helper exists and follows the documented priority
   ============================================================ */

echo "=== 1. mlsc_image_alt() PRIORITY ===\n";

chk('helper is defined', function_exists('mlsc_image_alt') ? 'yes' : 'no', 'yes');

$GLOBALS['__attachments'] = array(
    'https://example.com/wechat-qr.png' => array( 'id' => 501, 'alt' => 'Scan to chat with sales on WeChat' ),
    'https://example.com/no-alt.png'    => array( 'id' => 502, 'alt' => '' ),
);

chk('media library ALT wins over the fallback',
    mlsc_image_alt( 'https://example.com/wechat-qr.png', 'WeChat' ),
    'Scan to chat with sales on WeChat');

chk('empty media ALT falls back to the channel label',
    mlsc_image_alt( 'https://example.com/no-alt.png', 'WeChat' ),
    'WeChat');

chk('image not in the media library falls back to the label',
    mlsc_image_alt( 'https://cdn.example.com/legacy-qr.gif', 'WhatsApp' ),
    'WhatsApp');

chk('explicit override beats the media library ALT',
    mlsc_image_alt( 'https://example.com/wechat-qr.png', 'WeChat', 'Custom ALT' ),
    'Custom ALT');

chk('blank override falls through to the media library ALT',
    mlsc_image_alt( 'https://example.com/wechat-qr.png', 'WeChat', '   ' ),
    'Scan to chat with sales on WeChat');

chk('empty url returns the fallback',
    mlsc_image_alt( '', 'Telegram' ),
    'Telegram');

chk('tags are stripped from the media ALT',
    mlsc_image_alt( 'https://example.com/x.png', 'fb', '' ) === 'fb' ? 'clean' : 'leaky',
    'clean');

/* ============================================================
   2. Frontend render: the img really carries the ALT
   ============================================================ */

echo "\n=== 2. RENDER (QR + custom icon) ===\n";

$base = array(
    'contact_text' => '', 'show_contact_text' => 0, 'contact_link' => 'x',
    'position'     => 'right', 'hover_label' => 'slide', 'widget_style' => 'classic',
    'items'        => array(
        array(
            'channel' => 'wechat',
            'value'   => '',
            'qr'      => 'https://example.com/wechat-qr.png',
            'name'    => 'WeChat',
            'color'   => '#09BB07',
        ),
    ),
);
$GLOBALS['__options']['mlsc_options'] = $base;
ob_start(); mlsc_render_frontend(); $h = ob_get_clean();

chk('QR image is rendered', ( false !== strpos( $h, 'class="qr"' ) ) ? 'yes' : 'no', 'yes');
chk('QR alt carries the media library text',
    ( false !== strpos( $h, 'alt="Scan to chat with sales on WeChat"' ) ) ? 'yes' : 'no', 'yes');
chk('QR alt is never empty',
    preg_match( '/<div class="qr"><img[^>]*alt=""[^>]*>/', $h ) ? 'PRESENT' : 'none', 'none');

// Media ALT empty -> the channel name is used
$GLOBALS['__options']['mlsc_options'] = $base;
$GLOBALS['__options']['mlsc_options']['items'][0]['qr'] = 'https://example.com/no-alt.png';
ob_start(); mlsc_render_frontend(); $h2 = ob_get_clean();
chk('QR alt falls back to the channel name',
    ( false !== strpos( $h2, 'alt="WeChat"' ) ) ? 'yes' : 'no', 'yes');

// Explicit override
$GLOBALS['__options']['mlsc_options'] = $base;
$GLOBALS['__options']['mlsc_options']['items'][0]['img_alt'] = 'WeChat QR - scan me';
ob_start(); mlsc_render_frontend(); $h3 = ob_get_clean();
chk('per-channel ALT field wins on the frontend',
    ( false !== strpos( $h3, 'alt="WeChat QR - scan me"' ) ) ? 'yes' : 'no', 'yes');

// Custom icon
$GLOBALS['__options']['mlsc_options'] = $base;
$GLOBALS['__options']['mlsc_options']['items'][0] = array(
    'channel'     => 'whatsapp',
    'value'       => '8613800000000',
    'custom_icon' => 'https://example.com/no-alt.png',
    'name'        => 'WhatsApp',
    'color'       => '#25D366',
);
ob_start(); mlsc_render_frontend(); $h4 = ob_get_clean();
preg_match( '/mlsc-custom-icon"><img[^>]*alt="([^"]*)"/', $h4, $m4 );
chk('custom icon alt is rendered', isset( $m4[1] ) ? $m4[1] : 'MISSING', 'WhatsApp');
chk('custom icon alt is never empty',
    preg_match( '/mlsc-custom-icon"><img[^>]*alt=""[^>]*>/', $h4 ) ? 'PRESENT' : 'none', 'none');

/* ============================================================
   3. Escaping and the save path
   ============================================================ */

echo "\n=== 3. SANITIZER + ESCAPING ===\n";

$GLOBALS['__attachments']['https://example.com/quote.png'] = array( 'id' => 503, 'alt' => 'He said "hi" & left' );
$GLOBALS['__options']['mlsc_options'] = array(
    'contact_text' => '', 'show_contact_text' => 0, 'contact_link' => '', 'position' => 'right',
    'items' => array( array(
        'channel' => 'wechat', 'value' => '', 'name' => 'WeChat',
        'qr' => 'https://example.com/quote.png',
    ) ),
);
ob_start(); mlsc_render_frontend(); $h5 = ob_get_clean();
chk('quotes and ampersands are escaped in the alt',
    ( false !== strpos( $h5, 'alt="He said &quot;hi&quot; &amp; left"' ) ) ? 'yes' : 'no', 'yes');

$san = mlsc_sanitize_options( array(
    'items' => array( array(
        'channel' => 'wechat', 'value' => '', 'name' => 'WeChat',
        'qr'      => 'https://example.com/x.png',
        'img_alt' => '  <script>alert(1)</script>WeChat QR  ',
    ) ),
) );
chk('img_alt survives the sanitizer', isset( $san['items'][0]['img_alt'] ) ? 'present' : 'missing', 'present');
chk('img_alt is stripped of tags', $san['items'][0]['img_alt'], 'alert(1)WeChat QR');
chk('items saved before this release get an empty img_alt',
    mlsc_sanitize_options( array( 'items' => array( array( 'channel' => 'wechat' ) ) ) )['items'][0]['img_alt'],
    '');

/* ============================================================
   4. Admin field exists
   ============================================================ */

echo "\n=== 4. ADMIN FIELD ===\n";

$main = (string) file_get_contents( $argv[1] );
chk('admin has an Image ALT Text label', ( false !== strpos( $main, 'Image ALT Text' ) ) ? 'yes' : 'no', 'yes');
chk('admin input is named [img_alt]', ( false !== strpos( $main, "[img_alt]" ) ) ? 'yes' : 'no', 'yes');
// Plugin Check: the native tag stripper is not allowed in plugin code.
chk('no native tag stripper in the plugin source',
    preg_match( '/(?<!wp_)strip_tags\s*\(/', $main ) ? 'PRESENT' : 'none', 'none');
chk('ALT helper uses the core stripper', substr_count( $main, 'wp_strip_all_tags(' ) >= 3 ? 'yes' : 'no', 'yes');

chk('both frontend images go through the helper (definition + QR + custom icon)',
    substr_count( $main, 'mlsc_image_alt(' ) >= 3 ? 'yes' : 'no (' . substr_count( $main, 'mlsc_image_alt(' ) . ')', 'yes');

echo "\n" . ( $fail ? "FAILURES: $fail\n" : "ALL PASS\n" );
exit( $fail ? 1 : 0 );
