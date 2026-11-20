<?php
define('ABSPATH', __DIR__ . '/');
$GLOBALS['__options'] = array();
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
function sanitize_email($s){ $s=trim((string)$s); return filter_var($s,FILTER_VALIDATE_EMAIL)?$s:''; }
function sanitize_hex_color($s){ $s=trim((string)$s); return preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/',$s)?$s:null; }
function wp_parse_args($a,$d){ if(!is_array($a))$a=array(); return array_merge($d,$a); }
function checked($a,$b=true,$e=true){ $r=((string)$a===(string)$b)?' checked="checked"':''; if($e)echo $r; return $r; }
function selected($a,$b=true,$e=true){ $r=((string)$a===(string)$b)?' selected="selected"':''; if($e)echo $r; return $r; }
function wp_enqueue_style(){} function wp_enqueue_script(){} function wp_register_script(){} function wp_localize_script(){}
function wp_add_inline_script(){} function wp_json_encode($d){ return json_encode($d); }
function current_user_can(){ return true; } function settings_errors(){} function submit_button(){}
function wp_nonce_field(){} function settings_fields(){} function admin_url(){ return ''; }
function get_locale(){ return 'zh_CN'; } function absint($v){ return abs(intval($v)); }
function load_plugin_textdomain(){} function is_admin(){ return true; } function remove_all_actions(){}
function add_menu_page(){} function add_submenu_page(){} function register_setting(){}
function sanitize_html_class($c,$f=''){ $c=preg_replace('/[^A-Za-z0-9_-]/','',(string)$c); return $c===''?$f:$c; }
require $argv[1];
$fail=0;
function chk($n,$g,$w){ global $fail; $ok=((string)$g===(string)$w); printf("  [%s] %s\n",$ok?'PASS':'FAIL',$n); if(!$ok){echo "        got : ".var_export($g,true)."\n        want: ".var_export($w,true)."\n"; $fail++;} }
function render($opts){ $GLOBALS['__options']['mlsc_options']=$opts; $e=array();
  set_error_handler(function($n,$s)use(&$e){$e[]=$s;return true;}); ob_start(); mlsc_render_frontend(); $h=ob_get_clean();
  restore_error_handler(); return array($h,$e); }

echo "=== 1. LINK OVERRIDE（修复前不生效）===\n";
// value filled AND override set: override must win
chk('override beats generated link',
    mlsc_build_link(array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','link'=>'https://example.com/custom')),
    'https://example.com/custom');
chk('override works on telegram',
    mlsc_build_link(array('channel'=>'telegram','value'=>'mloun','link'=>'https://t.me/someoneelse')),
    'https://t.me/someoneelse');
chk('override works on phone',
    mlsc_build_link(array('channel'=>'phone','cc'=>'86','value'=>'13800000000','link'=>'tel:+1999')),
    'tel:+1999');
chk('override wins over a pasted URL',
    mlsc_build_link(array('channel'=>'custom','value'=>'https://a.com','link'=>'https://b.com')),
    'https://b.com');
chk('no override -> generated link still used',
    mlsc_build_link(array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','link'=>'')),
    'https://wa.me/8613800000000');
chk('override + prefill appends text',
    mlsc_build_link(array('channel'=>'whatsapp','value'=>'1','link'=>'https://wa.me/999','prefill'=>'Hi')),
    'https://wa.me/999?text=Hi');
chk('override already containing text= is NOT doubled',
    mlsc_build_link(array('channel'=>'whatsapp','value'=>'1','link'=>'https://wa.me/999?text=Own','prefill'=>'Hi')),
    'https://wa.me/999?text=Own');
chk('override on non-prefill channel untouched',
    mlsc_build_link(array('channel'=>'phone','value'=>'1','link'=>'tel:+1999','prefill'=>'Hi')),
    'tel:+1999');

echo "\n=== 2. TOOLTIP MODE MARKUP ===\n";
list($h,$e) = render(array(
  'items'=>array(array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','name'=>'Chat on WhatsApp','color'=>'#25D366')),
  'contact_text'=>'CONTACT US','show_contact_text'=>1,'contact_link'=>'https://x/',
  'position'=>'right','hover_label'=>'tooltip','tooltip_position'=>'left'));
chk('no warnings', count($e), 0);
chk('wrapper has tooltip class', (strpos($h,'mlsc-label-tooltip')!==false)?'yes':'no','yes');
chk('bubble attr carries hover text', (strpos($h,'data-mlsc-tooltip="Chat on WhatsApp"')!==false)?'yes':'no','yes');
chk('bubble position attr', (strpos($h,'data-mlsc-tooltip-pos="left"')!==false)?'yes':'no','yes');

echo "\n=== 3. BACK TO TOP IS THE LAST BAR ITEM ===\n";
// totop saved FIRST on purpose — it must still render last
list($h2,$e2) = render(array(
  'items'=>array(
    array('channel'=>'totop','name'=>'Back to Top','color'=>'#4E7A54'),
    array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','name'=>'WhatsApp','color'=>'#25D366'),
    array('channel'=>'email','value'=>'a@b.com','name'=>'Email','color'=>'#EA4335'),
  ),
  'contact_text'=>'','show_contact_text'=>0,'contact_link'=>'https://x/',
  'position'=>'right','hover_label'=>'slide','widget_style'=>'rounded'));
chk('no warnings', count($e2), 0);
chk('totop is inside the <ul>', (bool)preg_match('/<ul>.*mlsc-totop-item.*<\/ul>/s', $h2), 1);
chk('totop li has the class', (strpos($h2,'mlsc-totop-item')!==false)?'yes':'no','yes');
chk('trigger carries data-mlsc-totop', (strpos($h2,'data-mlsc-totop="1"')!==false)?'yes':'no','yes');
chk('totop is NOT an <a>', (bool)preg_match('/mlsc-totop-item.*?<span[^>]*item-inner/s', $h2), 1);
chk('totop has no target=_blank', preg_match('/data-mlsc-totop="1"[^>]*target=/', $h2) ? 'HAS' : 'none', 'none');
// order check
$pos_totop = strpos($h2,'mlsc-totop-item');
$pos_wa    = strpos($h2,'https://wa.me/8613800000000');
$pos_mail  = strpos($h2,'mailto:a@b.com');
chk('totop rendered AFTER whatsapp', ($pos_totop > $pos_wa)?'yes':'no','yes');
chk('totop rendered AFTER email', ($pos_totop > $pos_mail)?'yes':'no','yes');
chk('totop inherits style preset', (strpos($h2,'mlsc-style-rounded')!==false)?'yes':'no','yes');

echo "\n=== 4. TOTOP CARD HAS NO IRRELEVANT FIELDS ===\n";
$GLOBALS['__options']['mlsc_options'] = array('items'=>array(array('channel'=>'totop')));
ob_start(); mlsc_settings_page(); $pg = ob_get_clean();
preg_match('/<li class="mlsc-item" data-index="0"[^>]*data-channel="totop".*?<\/li>/s', $pg, $cm);
$card = isset($cm[0]) ? $cm[0] : '';
chk('saved card found', $card!==''?'yes':'no','yes');
chk('NO "Open in New Window"', (strpos($card,'Open in New Window')===false)?'none':'PRESENT','none');
chk('NO Link Override', (strpos($card,'Link Override')===false)?'none':'PRESENT','none');
chk('NO value input', (strpos($card,'[value]')===false)?'none':'PRESENT','none');
chk('NO qr input', (strpos($card,'[qr]')===false)?'none':'PRESENT','none');
chk('keeps colour + hover text', (strpos($card,'[color]')!==false && strpos($card,'[name]')!==false)?'yes':'no','yes');
preg_match('/id="mlsc-tpl-totop">(.*?)<\/script>/s', $pg, $tm);
$tpl = isset($tm[1])?$tm[1]:'';
chk('template also has NO blank checkbox', (strpos($tpl,'[blank]')===false)?'none':'PRESENT','none');
chk('template also has NO link override', (strpos($tpl,'[link]')===false)?'none':'PRESENT','none');
chk('normal channel keeps its blank checkbox',
    (bool)preg_match('/id="mlsc-tpl-whatsapp">.*?\[blank\]/s', $pg), 1);

echo "\nFAILURES: $fail\n";
