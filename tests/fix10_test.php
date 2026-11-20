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
require $argv[1];
$fail=0;
function chk($n,$g,$w){ global $fail; $ok=((string)$g===(string)$w); printf("  [%s] %s\n",$ok?'PASS':'FAIL',$n); if(!$ok){echo "        got : ".var_export($g,true)."\n        want: ".var_export($w,true)."\n"; $fail++;} }
echo "\n=== 32. PREVIEW MUST NEUTRALISE THE DYNAMIC SHEET ===\n";

$ajs = file_get_contents( dirname( $argv[1] ) . '/admin/admin.js' );

chk( 'breakpoint helper exists',        strpos( $ajs, 'function neutraliseBreakpoint(' ) !== false ? 'yes' : 'no', 'yes' );
chk( 'helper rewrites 768px',           strpos( $ajs, 'max-width:\\s*768px' ) !== false ? 'yes' : 'no', 'yes' );
chk( 'helper keeps mobile mode intact', strpos( $ajs, "if ( 'desktop' !== mode ) {" ) !== false ? 'yes' : 'no', 'yes' );
chk( 'frontend.css goes through it',    strpos( $ajs, "'<style>' + neutraliseBreakpoint( baseCss, mode ) + '</style>'" ) !== false ? 'yes' : 'no', 'yes' );
// 这条就是本次事故的核心：动态 CSS 曾经直出，没被中和
chk( 'dynamic css goes through it',     strpos( $ajs, "'<style>' + neutraliseBreakpoint( css, mode ) + '</style>'" ) !== false ? 'yes' : 'no', 'yes' );
chk( 'no raw dynamic css injection',    strpos( $ajs, "'<style>' + ( css || '' ) + '</style>'" ) === false ? 'none' : 'PRESENT', 'none' );
chk( 'phones hint exists',              strpos( $ajs, 'i18n.hiddenOnMobile' ) !== false ? 'yes' : 'no', 'yes' );

// 触发条件：开了"手机端隐藏"时，后端动态 CSS 里确实带 768px 断点
$opts = mlsc_get_defaults();
$opts['items'] = array( array( 'channel' => 'whatsapp', 'value' => '+1 4132529311', 'name' => 'WA', 'color' => '#25D366' ) );
$opts['mobile_hide'] = 1;
$css_on = mlsc_build_inline_css( $opts );
chk( 'mobile_hide emits a 768px rule', (bool) preg_match( '/@media[^{]*max-width:\s*768px/i', $css_on ), 1 );
chk( 'mobile_hide hides the widget',   (bool) preg_match( '/@media[^{]*max-width:\s*768px[^{]*\{[^}]*display:\s*none/i', $css_on ), 1 );

$opts['mobile_hide'] = 0;
$css_off = mlsc_build_inline_css( $opts );
chk( 'off => no hiding rule',          preg_match( '/max-width:\s*768px/i', $css_off ), 0 );

// 真正跑一遍中和正则，确认能把那条规则废掉
$neutralised = preg_replace( '/@media[^{]*max-width:\s*768px[^{]*\{/i', '@media (max-width:0px){', $css_on );
chk( 'neutralised sheet no longer matches 768px', preg_match( '/max-width:\s*768px/i', $neutralised ), 0 );
chk( 'neutralised sheet keeps the rule',           strpos( $neutralised, '@media (max-width:0px){' ) !== false ? 'yes' : 'no', 'yes' );

// 文案：i18n 与中文翻译都要在
$php = file_get_contents( $argv[1] );
chk( 'php exposes the hint string',    strpos( $php, "'hiddenOnMobile' =>" ) !== false ? 'yes' : 'no', 'yes' );
$i18n = file_get_contents( dirname( $argv[1] ) . '/build-i18n.py' );
chk( 'zh translation present',         strpos( $i18n, '手机端已隐藏' ) !== false ? 'yes' : 'no', 'yes' );

$main = file_get_contents( dirname( __DIR__ ) . '/mloun-side-contact/mloun-side-contact.php' );
$fcss = file_get_contents( dirname( __DIR__ ) . '/mloun-side-contact/assets/css/frontend.css' );
chk( '折叠提示是真实文本节点', ( false !== strpos( $main, 'class="mlsc-tip mlsc-tip-closed"' ) && false !== strpos( $main, 'class="mlsc-tip mlsc-tip-open"' ) ) ? '1' : '0', '1' );
chk( '文本节点输出的是标签文字', false !== strpos( $main, 'esc_html( $label_closed )' ) ? '1' : '0', '1' );
chk( '伪元素气泡已关闭', false !== strpos( $fcss, 'content: none !important' ) ? '1' : '0', '1' );
chk( '展开时隐藏 closed 标签', false !== strpos( $fcss, '.mlsc.mlsc-open li.mlsc-toggle-item .mlsc-tip-closed' ) ? '1' : '0', '1' );

echo "\nFAILURES: $fail\n";
