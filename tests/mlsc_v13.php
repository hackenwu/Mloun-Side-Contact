<?php
define('ABSPATH', __DIR__ . '/');
$GLOBALS['__options'] = array();
function add_action($h,$c='',$p=10,$a=1){}
function add_filter($h,$c='',$p=10,$a=1){}
function apply_filters($h,$v){ return $v; }
function plugin_dir_path($f){ return dirname($f).'/'; }
function plugin_dir_url($f){ return 'http://x/'; }
function plugins_url($p='',$f=''){ return 'http://x/'.$p; }
function plugin_basename($f){ return basename(dirname($f)).'/'.basename($f); }
function get_option($k,$d=false){ return array_key_exists($k,$GLOBALS['__options']) ? $GLOBALS['__options'][$k] : $d; }
function update_option($k,$v){ $GLOBALS['__options'][$k]=$v; return true; }
function esc_html($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }
function esc_attr($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }
function esc_textarea($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }
function esc_js($s){ return addslashes((string)$s); }
function esc_url($s){ return $s; }
function esc_url_raw($s){ return trim((string)$s); }
function esc_html__($s,$d=null){ return esc_html($s); }
function esc_html_e($s,$d=null){ echo esc_html($s); }
function esc_attr__($s,$d=null){ return esc_attr($s); }
function esc_attr_e($s,$d=null){ echo esc_attr($s); }
function __($s,$d=null){ return $s; }
function _n($s,$p,$n,$d=null){ return 1==$n?$s:$p; } function _x($s,$c,$d=null){ return $s; }

function _e($s,$d=null){ echo $s; }
function wp_kses($s,$a){ return $s; }
function wp_kses_post($s){ return $s; }
function sanitize_text_field($s){ return is_string($s)?trim(strip_tags($s)):''; }
function sanitize_textarea_field($s){ return is_string($s)?trim(strip_tags($s)):''; }
function sanitize_email($s){ $s=trim((string)$s); return filter_var($s, FILTER_VALIDATE_EMAIL) ? $s : ''; }
function sanitize_hex_color($s){ $s=trim((string)$s); return preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/',$s)?$s:null; }
function wp_parse_args($a,$d){ if(!is_array($a))$a=array(); return array_merge($d,$a); }
function checked($a,$b=true,$e=true){ $r=((string)$a===(string)$b)?' checked="checked"':''; if($e)echo $r; return $r; }
function selected($a,$b=true,$e=true){ $r=((string)$a===(string)$b)?' selected="selected"':''; if($e)echo $r; return $r; }
function wp_enqueue_style(){} function wp_enqueue_script(){} function wp_register_script(){} function wp_localize_script(){}
function wp_add_inline_script(){} function wp_add_inline_style(){} function wp_json_encode($d){ return json_encode($d); }
function current_user_can($c){ return true; }
function settings_errors(){} function submit_button($t=''){} function wp_nonce_field(){}
function settings_fields($g=''){} function do_settings_sections($g=''){}
function admin_url($p=''){ return 'http://x/'.$p; }
function get_locale(){ return 'zh_CN'; } function absint($v){ return abs(intval($v)); }
function load_plugin_textdomain(){ return true; } function is_admin(){ return true; }
function remove_all_actions(){} function screen_icon(){} function is_ssl(){ return false; }
function add_menu_page(){ return 'x'; } function add_submenu_page(){ return 'x'; }
function register_setting(){}

function sanitize_html_class($c,$f=''){ $c=preg_replace('/[^A-Za-z0-9_-]/','',(string)$c); return $c===''?$f:$c; }
function wp_strip_all_tags($s){ return strip_tags((string)$s); }
require $argv[1];

$fail = 0;
function chk($name, $got, $want) {
    global $fail;
    $ok = ((string) $got === (string) $want);
    printf("  [%s] %s\n", $ok ? 'PASS' : 'FAIL', $name);
    if ( ! $ok ) { echo "        got : " . var_export($got, true) . "\n        want: " . var_export($want, true) . "\n"; $fail++; }
}

echo "=== 1. URL BUILDING ===\n";
chk('whatsapp cc+number', mlsc_build_link(array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000')), 'https://wa.me/8613800000000');
chk('whatsapp strips spaces/dashes', mlsc_build_link(array('channel'=>'whatsapp','cc'=>'86','value'=>'138 0000-0000')), 'https://wa.me/8613800000000');
chk('whatsapp intl + wins over cc', mlsc_build_link(array('channel'=>'whatsapp','cc'=>'86','value'=>'+447700900123')), 'https://wa.me/447700900123');
chk('whatsapp prefill ascii', mlsc_build_link(array('channel'=>'whatsapp','cc'=>'1','value'=>'4155551234','prefill'=>'Hello there')), 'https://wa.me/14155551234?text=Hello%20there');
chk('whatsapp prefill chinese single-encoded', mlsc_build_link(array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','prefill'=>'请报价')), 'https://wa.me/8613800000000?text=%E8%AF%B7%E6%8A%A5%E4%BB%B7');
chk('telegram strips @', mlsc_build_link(array('channel'=>'telegram','value'=>'@mloun')), 'https://t.me/mloun');
chk('telegram prefill', mlsc_build_link(array('channel'=>'telegram','value'=>'mloun','prefill'=>'Hi')), 'https://t.me/mloun?text=Hi');
chk('phone tel:+', mlsc_build_link(array('channel'=>'phone','cc'=>'86','value'=>'13800000000')), 'tel:+8613800000000');
chk('sms sms:+', mlsc_build_link(array('channel'=>'sms','cc'=>'86','value'=>'13800000000')), 'sms:+8613800000000');
chk('viber encodes +', mlsc_build_link(array('channel'=>'viber','cc'=>'86','value'=>'13800000000')), 'viber://chat?number=%2B8613800000000');
chk('zalo cc 84', mlsc_build_link(array('channel'=>'zalo','cc'=>'84','value'=>'912345678')), 'https://zalo.me/84912345678');
chk('email mailto', mlsc_build_link(array('channel'=>'email','value'=>'sales@mloun.com')), 'mailto:sales@mloun.com');
chk('linkedin profile', mlsc_build_link(array('channel'=>'linkedin','value'=>'hacken-wu')), 'https://www.linkedin.com/in/hacken-wu');
chk('youtube @handle', mlsc_build_link(array('channel'=>'youtube','value'=>'hackenwu')), 'https://youtube.com/@hackenwu');
chk('instagram', mlsc_build_link(array('channel'=>'instagram','value'=>'@mloun')), 'https://instagram.com/mloun');
chk('messenger', mlsc_build_link(array('channel'=>'messenger','value'=>'mlounpage')), 'https://m.me/mlounpage');
chk('x', mlsc_build_link(array('channel'=>'x','value'=>'hackenwu5')), 'https://x.com/hackenwu5');
chk('line', mlsc_build_link(array('channel'=>'line','value'=>'mylineid')), 'https://line.me/ti/p/~mylineid');
chk('qq msgrd', mlsc_build_link(array('channel'=>'qq','value'=>'10001')), 'https://wpa.qq.com/msgrd?v=3&uin=10001&site=qq&menu=yes');
chk('skype chat', mlsc_build_link(array('channel'=>'skype','value'=>'live:abc')), 'skype:live:abc?chat');
chk('location encoded', mlsc_build_link(array('channel'=>'location','value'=>'Foshan, China')), 'https://maps.google.com/?q=Foshan%2C%20China');
chk('pasted URL wins (linkedin company)', mlsc_build_link(array('channel'=>'linkedin','value'=>'https://www.linkedin.com/company/mloun')), 'https://www.linkedin.com/company/mloun');
chk('custom passthrough', mlsc_build_link(array('channel'=>'custom','value'=>'https://example.com/x?a=1')), 'https://example.com/x?a=1');
chk('wechat has no link', mlsc_build_link(array('channel'=>'wechat','value'=>'whatever')), '');
chk('empty value falls back to override', mlsc_build_link(array('channel'=>'whatsapp','cc'=>'86','value'=>'','link'=>'https://wa.me/999')), 'https://wa.me/999');
chk('no double encoding', (strpos(mlsc_build_link(array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','prefill'=>'中文 & stuff')), '%25') === false) ? 'clean' : 'DIRTY', 'clean');

echo "\n=== 2. LEGACY MIGRATION (1.2.x) ===\n";
$GLOBALS['__options']['mlsc_options'] = array(
    'items' => array(
        array('icon'=>'whatsapp','name'=>'WhatsApp','link'=>'https://wa.me/8613800000000','color'=>'#25D366','blank'=>1),
        array('icon'=>'phone','name'=>'Call','link'=>'tel:+123456','color'=>'#4E7A54'),
    ),
    'contact_text'=>'CONTACT US','show_contact_text'=>1,'contact_link'=>'https://x/',
    'entrance_animation'=>'bounceIn','hover_effect'=>'pulse','tooltip_enabled'=>1,'tooltip_position'=>'left',
);
$errs = array();
set_error_handler(function($n,$s) use (&$errs){ $errs[]=$s; return true; });
ob_start(); mlsc_render_frontend(); $html = ob_get_clean();
restore_error_handler();
chk('legacy: no PHP warnings', count($errs), 0);
chk('legacy: old whatsapp link kept', (strpos($html,'href="https://wa.me/8613800000000"')!==false)?'yes':'no', 'yes');
chk('legacy: old tel link kept', (strpos($html,'href="tel:+123456"')!==false)?'yes':'no', 'yes');
chk('legacy: animation class', (strpos($html,'mlsc-anim-bounceIn')!==false)?'yes':'no', 'yes');
chk('legacy: tooltip attr', (strpos($html,'data-mlsc-tooltip="WhatsApp"')!==false)?'yes':'no', 'yes');

echo "\n=== 3. NEW MODEL RENDER ===\n";
$GLOBALS['__options']['mlsc_options'] = array(
    'items' => array(
        array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','name'=>'WhatsApp','color'=>'#25D366','blank'=>1,'prefill'=>'Hi, please quote'),
        array('channel'=>'wechat','qr'=>'https://x/qr.png','name'=>'WeChat','color'=>'#07C160'),
        array('channel'=>'email','value'=>'sales@mloun.com','name'=>'Email','color'=>'#EA4335'),
    ),
    'contact_text'=>'CONTACT US','show_contact_text'=>1,'contact_link'=>'https://x/',
    'tooltip_enabled'=>1,'tooltip_position'=>'left','hover_effect'=>'color',
);
$errs = array();
set_error_handler(function($n,$s) use (&$errs){ $errs[]=$s; return true; });
ob_start(); mlsc_render_frontend(); $html = ob_get_clean();
restore_error_handler();
chk('new: no PHP warnings', count($errs), 0);
chk('new: whatsapp built + prefill', (strpos($html,'https://wa.me/8613800000000?text=')!==false)?'yes':'no','yes');
chk('new: email mailto', (strpos($html,'href="mailto:sales@mloun.com"')!==false)?'yes':'no','yes');
chk('new: wechat rendered as span', (strpos($html,'<span class="item-inner')!==false)?'yes':'no','yes');
chk('new: wechat qr block', (strpos($html,'class="qr"')!==false)?'yes':'no','yes');
chk('new: no dead href="#"', (strpos($html,'href="#"')===false)?'clean':'DEAD','clean');

echo "\n=== 4. SETTINGS PAGE ===\n";
$GLOBALS['__options']['mlsc_options'] = array('items'=>array());
$errs = array();
set_error_handler(function($n,$s) use (&$errs){ $errs[]=$s; return true; });
ob_start(); mlsc_settings_page(); $page = ob_get_clean();
restore_error_handler();
$channels = mlsc_get_channels();
chk('settings: no PHP warnings', count($errs), 0);
foreach (array_unique($errs) as $e) { echo "        -> $e\n"; }
chk('settings: size > 20KB', (strlen($page) > 20000), true);
chk('settings: step 1 heading', (strpos($page,'Step 1: Choose your channels')!==false)?'yes':'no','yes');
chk('settings: step 2 heading', (strpos($page,'Step 2: Widget customization')!==false)?'yes':'no','yes');
chk('settings: channel count (incl. hidden skype)', count($channels), 22);
chk('settings: 21 tiles', substr_count($page,'class="mlsc-channel-tile'), 21);
chk('settings: 21 templates', substr_count($page,'type="text/html" id="mlsc-tpl-'), 21);
chk('settings: no leftover icon selector', (strpos($page,'mlsc-select-icon')===false)?'gone':'LEFT','gone');

echo "\n=== 5. SANITIZE ===\n";
$clean = mlsc_sanitize_options(array('items' => array(
    array('channel'=>'whatsapp','cc'=>'86','value'=>'138 0000-0000','prefill'=>'  Hi  ','name'=>'WA','color'=>'#25D366'),
    array('channel'=>'custom','value'=>'javascript:alert(1)','name'=>'Bad'),
    array('channel'=>'email','value'=>'not-an-email'),
    'garbage-not-an-array',
)));
chk('sanitize: phone digits kept', $clean['items'][0]['value'], '13800000000');
chk('sanitize: prefill trimmed', $clean['items'][0]['prefill'], 'Hi');
chk('sanitize: bad email dropped', $clean['items'][2]['value'], '');
chk('sanitize: non-array row skipped', count($clean['items']), 3);

echo "\nFAILURES: $fail\n";
