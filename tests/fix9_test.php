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
function wp_unslash( $v ) { return is_string( $v ) ? stripslashes( $v ) : $v; }
function check_ajax_referer( $a, $n = false ) { return true; }

// wp_send_json_* 在真实环境会 exit；桩里改成抛异常，否则 error 之后还会继续渲染把结果覆盖成 success。
class Mlsc_Json_Sent extends Exception {
    public $payload;
    public function __construct( $p ) { $this->payload = $p; parent::__construct( 'json sent' ); }
}
function wp_send_json_success( $d = null ) { throw new Mlsc_Json_Sent( array( 'success' => true, 'data' => $d ) ); }
function wp_send_json_error( $d = null ) { throw new Mlsc_Json_Sent( array( 'success' => false, 'data' => $d ) ); }

function wp_strip_all_tags($s){ return strip_tags((string)$s); }
require $argv[1];
$fail = 0;
function chk($n,$g,$w){ global $fail; $ok=((string)$g===(string)$w); printf("  [%s] %s\n",$ok?'PASS':'FAIL',$n); if(!$ok){echo "        got : ".var_export($g,true)."\n        want: ".var_export($w,true)."\n"; $fail++;} }

/** 用真实方式调一次端点，返回它想发出去的 JSON。 */
function preview( $form ) {
    $_POST = array( 'form' => $form );
    try { mlsc_ajax_preview(); } catch ( Mlsc_Json_Sent $e ) { return $e->payload; }
    return null;
}

echo "\n=== 31. PREVIEW ENDPOINT (real request path) ===\n";

// 和浏览器里 jQuery form.serialize() 产出一致：整串百分号转义
function mlsc_form_payload( $n = 2 ) {
    $parts = array(
        'mlsc_options%5Bposition%5D=right',
        'mlsc_options%5Bhover_label%5D=slide',
        'mlsc_options%5Bwidget_style%5D=classic',
        'mlsc_options%5Bsize_preset%5D=medium',
    );
    for ( $i = 0; $i < $n; $i++ ) {
        $parts[] = 'mlsc_options%5Bitems%5D%5B' . $i . '%5D%5Bchannel%5D=' . ( 0 === $i ? 'whatsapp' : 'custom' );
        $parts[] = 'mlsc_options%5Bitems%5D%5B' . $i . '%5D%5Bvalue%5D=' . ( 0 === $i ? '%2B1%204132529311' : 'https%3A%2F%2Fexample.com%2F%3Fa%3D1%26b%3D2' );
        $parts[] = 'mlsc_options%5Bitems%5D%5B' . $i . '%5D%5Bname%5D=Bob%20%26%20Co';
        $parts[] = 'mlsc_options%5Bitems%5D%5B' . $i . '%5D%5Bcolor%5D=%2325D366';
        $parts[] = 'mlsc_options%5Bitems%5D%5B' . $i . '%5D%5Bshow_desktop%5D=1';
        $parts[] = 'mlsc_options%5Bitems%5D%5B' . $i . '%5D%5Bshow_mobile%5D=1';
    }
    return implode( '&', $parts );
}

$res = preview( mlsc_form_payload( 2 ) );
chk( 'handler returns success',        $res['success'] ? 'yes' : 'no', 'yes' );
chk( 'counts both channels',           $res['data']['count'], 2 );
chk( 'returns real markup',            strlen( $res['data']['html'] ) > 300 ? 'yes' : 'no (' . strlen( $res['data']['html'] ) . ')', 'yes' );
chk( 'markup has the widget wrapper',  strpos( $res['data']['html'], 'class="mlsc ' ) !== false ? 'yes' : 'no', 'yes' );
chk( 'markup keeps both buttons',      substr_count( $res['data']['html'], '<li class=' ), 2 );
chk( 'returns scoped css',             strlen( $res['data']['css'] ) > 20 ? 'yes' : 'no', 'yes' );
chk( 'percent escapes survived',       ( strpos( $res['data']['html'], 'https://example.com/?a=1&amp;b=2' ) !== false || strpos( $res['data']['html'], 'https://example.com/?a=1&b=2' ) !== false ) ? 'yes' : 'no', 'yes' );
chk( 'no stale message',               $res['data']['message'], '' );

// 这条能抓住 filter_input 事故：拿不到 POST 时会静默返回 0 个频道、只剩骨架屏
chk( 'empty form -> 0 channels',       preview( '' )['data']['count'], 0 );

// 体积护栏
$big = preview( str_repeat( 'a', 262145 ) );
chk( 'oversize payload rejected',      $big['success'] ? 'accepted' : 'rejected', 'rejected' );
chk( 'rejection is translated',        strpos( (string) $big['data']['message'], 'too large' ) !== false ? 'yes' : 'no', 'yes' );

// 一个频道也要能渲染
$one = preview( mlsc_form_payload( 1 ) );
chk( 'single channel renders',         $one['data']['count'], 1 );
chk( 'single channel has markup',      strlen( $one['data']['html'] ) > 100 ? 'yes' : 'no', 'yes' );

chk( 'capability gate present',        strpos( file_get_contents( $argv[1] ), "current_user_can( 'manage_options' )" ) !== false ? 'yes' : 'no', 'yes' );

echo "\nFAILURES: $fail\n";
