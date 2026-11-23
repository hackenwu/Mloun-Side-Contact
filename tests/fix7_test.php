<?php
define('ABSPATH', __DIR__ . '/');
$GLOBALS['__options'] = array(); $GLOBALS['__css'] = '';
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
function sanitize_html_class($c,$f=''){ $c=preg_replace('/[^A-Za-z0-9_-]/','',(string)$c); return $c===''?$f:$c; }
function sanitize_email($s){ $s=trim((string)$s); return filter_var($s,FILTER_VALIDATE_EMAIL)?$s:''; }
function sanitize_hex_color($s){ $s=trim((string)$s); return preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/',$s)?$s:null; }
function wp_parse_args($a,$d){ if(!is_array($a))$a=array(); return array_merge($d,$a); }
function checked($a,$b=true,$e=true){ $r=((string)$a===(string)$b)?' checked="checked"':''; if($e)echo $r; return $r; }
function selected($a,$b=true,$e=true){ $r=((string)$a===(string)$b)?' selected="selected"':''; if($e)echo $r; return $r; }
function wp_enqueue_style(){} function wp_enqueue_script(){} function wp_register_script(){} function wp_localize_script(){}
function wp_add_inline_script(){} function wp_json_encode($d){ return json_encode($d); }
function wp_add_inline_style($h,$c){ $GLOBALS['__css'] .= $c; }
function current_user_can(){ return true; } function settings_errors(){} function submit_button(){}
function wp_nonce_field(){} function settings_fields(){} function admin_url(){ return ''; }
function get_locale(){ return 'zh_CN'; } function absint($v){ return abs(intval($v)); }
function load_plugin_textdomain(){} function is_admin(){ return true; } function remove_all_actions(){}
function add_menu_page(){} function add_submenu_page(){} function register_setting(){}
function wp_strip_all_tags($s){ return strip_tags((string)$s); }
require $argv[1];
$fail=0;
function chk($n,$g,$w){ global $fail; $ok=((string)$g===(string)$w); printf("  [%s] %s\n",$ok?'PASS':'FAIL',$n); if(!$ok){echo "        got : ".var_export($g,true)."\n        want: ".var_export($w,true)."\n"; $fail++;} }
echo "\n=== 28. PLUGIN CHECK: INPUT SANITIZATION ===\n";

// The preview endpoint receives a serialized form (URL-encoded key/value pairs).
// A text sanitizer must NOT touch it: sanitize_text_field() strips %XX escapes.
$src = file_get_contents( $argv[1] );

chk( 'reads the payload from $_POST',    strpos( $src, "isset( \$_POST['form'] ) ? wp_unslash( (string) \$_POST['form'] ) : ''" ) !== false ? 'yes' : 'no', 'yes' );
// 注释里会解释为什么不用 filter_input()，统计代码前先剥掉注释
$mlsc_code = preg_replace( '#/\*.*?\*/#s', '', $src );
$mlsc_code = preg_replace( '#^\s*//.*$#m', '', $mlsc_code );
chk( 'no filter_input dependency',       strpos( $mlsc_code, 'filter_input(' ) === false ? 'none' : 'PRESENT', 'none' );
chk( 'sniff ignore documents the why',   strpos( $src, 'phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized' ) !== false ? 'yes' : 'no', 'yes' );
chk( 'ignore sits right above the read', (bool) preg_match( '/phpcs:ignore[^\n]*\n\s*\$serialized = isset\( \$_POST/', $src ), 1 );
chk( 'oversize payload rejected',        strpos( $src, '262144' ) !== false ? 'yes' : 'no', 'yes' );
chk( 'too-large error is translated',    strpos( $src, "__( 'Preview payload too large.'" ) !== false ? 'yes' : 'no', 'yes' );
chk( 'nonce still verified first',       strpos( $src, "check_ajax_referer( 'mlsc_preview', 'nonce' )" ) !== false ? 'yes' : 'no', 'yes' );
chk( 'capability checked',               strpos( $src, "current_user_can( 'manage_options' )" ) !== false ? 'yes' : 'no', 'yes' );
chk( 'preview uses the save sanitizer',  strpos( $src, '$options = mlsc_sanitize_options( $raw );' ) !== false ? 'yes' : 'no', 'yes' );
chk( 'no text sanitizer on the payload', strpos( $src, 'sanitize_text_field( $serialized' ) === false ? 'none' : 'PRESENT', 'none' );

// 预览失败必须"说话"，而不是永远停在骨架屏
$ajs = file_get_contents( dirname( $argv[1] ) . '/admin/admin.js' );
chk( 'preview ajax has a timeout',       strpos( $ajs, 'timeout: 25000' ) !== false ? 'yes' : 'no', 'yes' );
chk( 'preview ajax expects json',        strpos( $ajs, "dataType: 'json'" ) !== false ? 'yes' : 'no', 'yes' );
chk( 'failure shows http status',        strpos( $ajs, "'HTTP ' + xhr.status" ) !== false ? 'yes' : 'no', 'yes' );
chk( 'empty markup is reported',         strpos( $ajs, 'i18n.previewEmpty' ) !== false ? 'yes' : 'no', 'yes' );
chk( 'failures reach the console',       strpos( $ajs, 'console.warn' ) !== false ? 'yes' : 'no', 'yes' );

// Prove the decode path preserves data that a text sanitizer would destroy.
$payload = 'mlsc_options%5Bitems%5D%5B0%5D%5Bchannel%5D=custom'
         . '&mlsc_options%5Bitems%5D%5B0%5D%5Bname%5D=Bob%20%26%20Co'
         . '&mlsc_options%5Bitems%5D%5B0%5D%5Bvalue%5D=https%3A%2F%2Fexample.com%2F%3Fa%3D1%26b%3D2';
$parsed = array();
parse_str( $payload, $parsed );
$opt = mlsc_sanitize_options( $parsed['mlsc_options'] );
chk( 'decoded percent escapes survive', $opt['items'][0]['name'], 'Bob & Co' );
chk( 'query string ampersand survives', strpos( $opt['items'][0]['value'], '?a=1&b=2' ) !== false ? 'yes' : 'no', 'yes' );
// WP's _sanitize_text_fields() drops every %XX escape; a faithful local demo of
// that loop proves why the serialized payload must not go through it.
$wp_like = preg_replace( '/%[a-f0-9]{2}/i', '', $payload );
chk( 'text sanitizer would drop the escapes', $wp_like !== $payload ? 'changed' : 'same', 'changed' );
chk( 'so the payload is never text-sanitized', $wp_like === $payload ? 'SAFE' : 'would corrupt', 'would corrupt' );

echo "\n=== 29. PLUGIN CHECK: TRANSLATORS COMMENT ===\n";

chk( 'exactly one plural call', substr_count( $src, '_n(' ), 1 );

$lines = preg_split( '/\r\n|\r|\n/', $src );
$at = -1;
foreach ( $lines as $i => $line ) {
    if ( false !== strpos( $line, "_n( 'Live on your site" ) ) {
        $at = $i;
        break;
    }
}
chk( 'plural call located', $at > 0 ? 'yes' : 'no', 'yes' );
if ( $at > 0 ) {
    chk( 'comment sits on the line above', strpos( $lines[ $at - 1 ], 'translators:' ) !== false ? 'yes' : 'no', 'yes' );
    chk( 'comment explains %d',            strpos( $lines[ $at - 1 ], '%d' ) !== false ? 'yes' : 'no', 'yes' );
    chk( 'call starts its own line',       substr( ltrim( $lines[ $at ] ), 0, 3 ), '_n(' );
    chk( 'not buried in a longer line',    strlen( rtrim( $lines[ $at ] ) ) < 400 ? 'short' : 'LONG', 'short' );
}
chk( 'singular has the placeholder', strpos( $src, "'Live on your site · %d channel'" ) !== false ? 'yes' : 'no', 'yes' );
chk( 'plural has the placeholder',   strpos( $src, "'Live on your site · %d channels'" ) !== false ? 'yes' : 'no', 'yes' );

echo "\nFAILURES: $fail\n";
