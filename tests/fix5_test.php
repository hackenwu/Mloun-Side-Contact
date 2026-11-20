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
require $argv[1];
$fail=0;
function chk($n,$g,$w){ global $fail; $ok=((string)$g===(string)$w); printf("  [%s] %s\n",$ok?'PASS':'FAIL',$n); if(!$ok){echo "        got : ".var_export($g,true)."\n        want: ".var_export($w,true)."\n"; $fail++;} }

echo "=== 1. LINK OVERRIDE ACCEPTS WIDGET SCRIPTS + ANCHORS ===\n";
chk('javascript: refused by default (click-to-run XSS hardening)',
    mlsc_sanitize_link_override('javascript:void(Tawk_API.toggle())'),
    '');
$GLOBALS['__allow_js'] = true;
chk('javascript: allowed once mlsc_allow_javascript_links opts in',
    mlsc_sanitize_link_override('javascript:void(Tawk_API.toggle())'),
    'javascript:void(Tawk_API.toggle())');
$GLOBALS['__allow_js'] = false;
chk('anchor #open-chat', mlsc_sanitize_link_override('#open-chat'), '#open-chat');
chk('https url', mlsc_sanitize_link_override('https://example.com/x'), 'https://example.com/x');
chk('site-relative /contact', mlsc_sanitize_link_override('/contact'), '/contact');
chk('tel:', mlsc_sanitize_link_override('tel:+8613800000000'), 'tel:+8613800000000');
chk('mailto:', mlsc_sanitize_link_override('mailto:a@b.com'), 'mailto:a@b.com');
chk('bare domain gets https', mlsc_sanitize_link_override('example.com/page'), 'https://example.com/page');
chk('attribute break-out input is dropped entirely',
    mlsc_sanitize_link_override('"><img src=x onerror=alert(1)>'), '');
$GLOBALS['__allow_js'] = true;
chk('no quotes survive in a js override',
    strpos(mlsc_sanitize_link_override('javascript:void(a("b"))'), '"') === false ? 'clean' : 'HAS QUOTE', 'clean');
chk('javascript with quotes: quotes stripped, scheme kept',
    mlsc_sanitize_link_override('javascript:void(MyWidget.open("x"))'),
    'javascript:void(MyWidget.open(x))');
$GLOBALS['__allow_js'] = false;
chk('empty stays empty', mlsc_sanitize_link_override('   '), '');

echo "\n=== 2. CUSTOM CSS CLASS ===\n";
chk('single class', mlsc_sanitize_css_classes('my-class'), 'my-class');
chk('two classes', mlsc_sanitize_css_classes('my-class other_one'), 'my-class other_one');
chk('extra spaces collapsed', mlsc_sanitize_css_classes('  a    b  '), 'a b');
chk('illegal chars stripped', mlsc_sanitize_css_classes('bad"class<script>'), 'badclassscript');
chk('empty', mlsc_sanitize_css_classes(''), '');

echo "\n=== 3. RENDER OUTPUT ===\n";
$GLOBALS['__options']['mlsc_options'] = array(
  'items'=>array(
    array('channel'=>'custom','value'=>'javascript:void(Tawk_API.toggle())','name'=>'Chat with us','color'=>'#25D366','css_class'=>'tawk-toggle'),
    array('channel'=>'custom','value'=>'#open-chat','name'=>'Open chat','color'=>'#0088CC','css_class'=>'scroll-anchor'),
  ),
  'contact_text'=>'','show_contact_text'=>0,'contact_link'=>'x',
  'position'=>'right','hover_label'=>'slide','widget_style'=>'classic');
$errs=array(); set_error_handler(function($n,$s)use(&$errs){$errs[]=$s;return true;});
ob_start(); mlsc_render_frontend(); $h = ob_get_clean(); restore_error_handler();
chk('no warnings', count($errs), 0);
chk('javascript href emitted', (strpos($h,'href="javascript:void(Tawk_API.toggle())"')!==false)?'yes':'no','yes');
chk('anchor href emitted', (strpos($h,'href="#open-chat"')!==false)?'yes':'no','yes');
chk('custom class on the link', (strpos($h,'tawk-toggle')!==false)?'yes':'no','yes');
chk('second custom class', (strpos($h,'scroll-anchor')!==false)?'yes':'no','yes');
chk('class lives inside item-inner', (bool)preg_match('/class="item-inner[^"]*tawk-toggle/', $h), 1);

echo "\n=== 4. CENTERED WRAPPER CLASS ===\n";
$GLOBALS['__options']['mlsc_options'] = array(
  'items'=>array(array('channel'=>'whatsapp','cc'=>'86','value'=>'1','name'=>'WA','color'=>'#25D366')),
  'contact_text'=>'','show_contact_text'=>0,'contact_link'=>'x','position'=>'right','center'=>1,'hover_label'=>'tooltip');
ob_start(); mlsc_render_frontend(); $hc = ob_get_clean();
chk('center on -> mlsc-centered', (strpos($hc,'mlsc-centered')!==false)?'yes':'no','yes');
$GLOBALS['__options']['mlsc_options']['center']=0;
ob_start(); mlsc_render_frontend(); $hn = ob_get_clean();
chk('center off -> no class', (strpos($hn,'mlsc-centered')===false)?'none':'PRESENT','none');

echo "\n=== 5. ADMIN FIELD ===\n";
$GLOBALS['__options']['mlsc_options'] = array('items'=>array(array('channel'=>'whatsapp','value'=>'1')));
ob_start(); mlsc_settings_page(); $pg = ob_get_clean();
chk('Custom CSS Class field present', (strpos($pg,'Custom CSS Class')!==false)?'yes':'no','yes');
chk('field name wired', (strpos($pg,'[css_class]')!==false)?'yes':'no','yes');
chk('override description mentions javascript', (strpos($pg,'javascript:void(SomeWidget.toggle())')!==false)?'yes':'no','yes');

echo "\nFAILURES: $fail\n";
