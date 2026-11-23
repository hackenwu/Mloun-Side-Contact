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
echo "\n=== 30. BRAND PALETTE (admin) ===\n";

$acss = file_get_contents( dirname( $argv[1] ) . '/admin/admin.css' );

chk( 'root palette block present', strpos( $acss, ':root {' ) !== false ? 'yes' : 'no', 'yes' );
chk( 'blue token is the brand blue', preg_match( '/--mlsc-blue:\s*#0F91E3/i', $acss ) ? 'yes' : 'no', 'yes' );
chk( 'orange token is the brand orange', preg_match( '/--mlsc-orange:\s*#F06B04/i', $acss ) ? 'yes' : 'no', 'yes' );
// 注释里也会提到品牌色，统计前先剥掉 /* */ 与 // 行注释
$mlsc_nc = preg_replace( '#/\*.*?\*/#s', '', $acss );
$mlsc_nc = preg_replace( '#^\s*//.*$#m', '', $mlsc_nc );
chk( 'each brand hex defined once', substr_count( strtoupper( $mlsc_nc ), '#0F91E3' ) . '/' . substr_count( strtoupper( $mlsc_nc ), '#F06B04' ), '1/1' );

// 旧配色一个都不许留
foreach ( array( '#667eea', '#764ba2', '#48bb78', '#38a169', '#1e7e45', '#7b5cd6' ) as $legacy ) {
    chk( 'legacy ' . $legacy . ' gone', stripos( $acss, $legacy ) === false ? 'none' : 'PRESENT', 'none' );
}
chk( 'no legacy purple shadow', preg_match( '/rgba\(\s*102,\s*126,\s*234/', $acss ) ? 'PRESENT' : 'none', 'none' );
chk( 'no legacy green shadow', preg_match( '/rgba\(\s*72,\s*187,\s*120/', $acss ) ? 'PRESENT' : 'none', 'none' );

// 角色分配
chk( 'header gradient is blue', preg_match( '/\.mlsc-header \{[^}]*var\(--mlsc-blue\)[^}]*var\(--mlsc-blue-strong\)/', $acss ) ? 'yes' : 'no', 'yes' );
chk( 'save button is orange', preg_match( '/\.mlsc-save-btn \{[^}]*var\(--mlsc-orange\)[^}]*var\(--mlsc-orange-strong\)/', $acss ) ? 'yes' : 'no', 'yes' );
chk( 'added tile uses blue', preg_match( '/\.mlsc-channel-tile\.is-added \{[^}]*border-color:\s*var\(--mlsc-blue\)/', $acss ) ? 'yes' : 'no', 'yes' );
chk( 'xN badge is deep orange', preg_match( '/\.mlsc-tile-times \{[^}]*background:\s*var\(--mlsc-orange-strong\)/', $acss ) ? 'yes' : 'no', 'yes' );
chk( 'hint text uses the readable orange', preg_match( '/\.mlsc-repeat-hint \{[^}]*color:\s*var\(--mlsc-orange-text\)/', $acss ) ? 'yes' : 'no', 'yes' );

// 复用度：令牌真的在跑
$vars = preg_match_all( '/var\(--mlsc-[a-z-]+\)/', $acss );
chk( 'tokens drive the UI (>60 uses)', $vars > 60 ? 'yes' : 'no (' . $vars . ')', 'yes' );
chk( 'css braces balanced', substr_count( $acss, '{' ) - substr_count( $acss, '}' ), 0 );

// 对比度体检：小字白底/彩底必须 >= 4.5
function mlsc_lum( $hex ) {
    $hex = ltrim( $hex, '#' );
    $out = 0.0;
    foreach ( array( 0, 2, 4 ) as $i => $p ) {
        $c = hexdec( substr( $hex, $p, 2 ) ) / 255;
        $c = $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
        $out += ( 0 === $i ? 0.2126 : ( 2 === $i ? 0.0722 : 0.7152 ) ) * $c;
    }
    return $out;
}
function mlsc_cr( $a, $b ) {
    $la = mlsc_lum( $a ); $lb = mlsc_lum( $b );
    return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
}
chk( 'hint text contrast >= 4.5', mlsc_cr( '#9A3F02', '#FFF4EC' ) >= 4.5 ? 'pass' : 'FAIL ' . round( mlsc_cr( '#9A3F02', '#FFF4EC' ), 2 ), 'pass' );
chk( 'xN badge white-on-orange >= 4.5', mlsc_cr( '#FFFFFF', '#C25403' ) >= 4.5 ? 'pass' : 'FAIL', 'pass' );
chk( 'inactive step grey >= 4.5', mlsc_cr( '#6B7280', '#FFFFFF' ) >= 4.5 ? 'pass' : 'FAIL', 'pass' );

echo "\nFAILURES: $fail\n";
