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
function wp_add_inline_script(){} function wp_add_inline_style(){} function wp_json_encode($d){ return json_encode($d); }
function current_user_can(){ return true; } function settings_errors(){} function submit_button(){}
function wp_nonce_field(){} function settings_fields(){} function admin_url(){ return ''; }
function get_locale(){ return 'zh_CN'; } function absint($v){ return abs(intval($v)); }
function load_plugin_textdomain(){} function is_admin(){ return true; } function remove_all_actions(){}
function add_menu_page(){} function add_submenu_page(){} function register_setting(){}
function sanitize_html_class($c,$f=''){ $c=preg_replace('/[^A-Za-z0-9_-]/','',(string)$c); return $c===''?$f:$c; }
function wp_strip_all_tags($s){ return strip_tags((string)$s); }
require $argv[1];
$fail=0;
function chk($n,$g,$w){ global $fail; $ok=((string)$g===(string)$w); printf("  [%s] %s\n",$ok?'PASS':'FAIL',$n); if(!$ok){echo "        got : ".var_export($g,true)."\n        want: ".var_export($w,true)."\n"; $fail++;} }
function render($opts){ $GLOBALS['__options']['mlsc_options']=$opts; $e=array();
  set_error_handler(function($n,$s)use(&$e){$e[]=$s;return true;}); ob_start(); mlsc_render_frontend(); $h=ob_get_clean();
  restore_error_handler(); return array($h,$e); }

$base_item = array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','name'=>'WA','color'=>'#25D366');
$base_opts = array('contact_text'=>'CONTACT US','show_contact_text'=>1,'contact_link'=>'https://x/','hover_label'=>'tooltip');

echo "=== 1. TOOLTIP POSITION IS AUTOMATIC ===\n";
list($h1,$e1) = render(array_merge($base_opts, array('position'=>'right','items'=>array($base_item))));
chk('no warnings', count($e1), 0);
chk('bar right -> bubble opens LEFT', (strpos($h1,'data-mlsc-tooltip-pos="left"')!==false)?'yes':'no','yes');

list($h2,$e2) = render(array_merge($base_opts, array('position'=>'left','items'=>array($base_item))));
chk('bar left -> bubble opens RIGHT', (strpos($h2,'data-mlsc-tooltip-pos="right"')!==false)?'yes':'no','yes');

// an explicit stale option value must be ignored
list($h3,$e3) = render(array_merge($base_opts, array('position'=>'right','tooltip_position'=>'right','items'=>array($base_item))));
chk('stale tooltip_position option ignored', (strpos($h3,'data-mlsc-tooltip-pos="left"')!==false)?'yes':'no','yes');

$GLOBALS['__options']['mlsc_options'] = array('items'=>array());
ob_start(); mlsc_settings_page(); $pg = ob_get_clean();
chk('no Tooltip Position select in admin', (strpos($pg,'mlsc_options[tooltip_position]')===false)?'gone':'STILL THERE','gone');
chk('shows the automatic note', (strpos($pg,'Automatic')!==false)?'yes':'no','yes');

echo "\n=== 2. PER-CHANNEL DEVICE VISIBILITY ===\n";
$it_off_mobile = array_merge($base_item, array('show_desktop'=>1,'show_mobile'=>0));
$it_off_desktop = array_merge($base_item, array('show_desktop'=>0,'show_mobile'=>1,'value'=>'13900000000'));
list($hd,$ed) = render(array_merge($base_opts, array('position'=>'right','hover_label'=>'slide','items'=>array($it_off_mobile,$it_off_desktop))));
chk('no warnings', count($ed), 0);
chk('hide-mobile class emitted', (strpos($hd,'mlsc-hide-mobile')!==false)?'yes':'no','yes');
chk('hide-desktop class emitted', (strpos($hd,'mlsc-hide-desktop')!==false)?'yes':'no','yes');
chk('desktop-only item has no hide-desktop', substr_count($hd,'mlsc-hide-desktop'), 1);
chk('mobile-only item has no hide-mobile', substr_count($hd,'mlsc-hide-mobile'), 1);

// legacy item without the keys must stay visible on BOTH
list($hl,$el) = render(array_merge($base_opts, array('position'=>'right','hover_label'=>'slide','items'=>array($base_item))));
chk('legacy item: no hide classes at all', (strpos($hl,'mlsc-hide-')===false)?'none':'PRESENT','none');

echo "\n=== 3. SANITIZE KEEPS THE FLAGS ===\n";
$s = mlsc_sanitize_options(array('items'=>array(
  array('channel'=>'whatsapp','value'=>'1','show_desktop'=>'1','show_mobile'=>'1'),
  array('channel'=>'whatsapp','value'=>'2','show_desktop'=>'1'),
  array('channel'=>'whatsapp','value'=>'3'),
)));
chk('both on', $s['items'][0]['show_desktop'].'/'.$s['items'][0]['show_mobile'], '1/1');
chk('mobile off', $s['items'][1]['show_desktop'].'/'.$s['items'][1]['show_mobile'], '1/0');
chk('both off when unchecked', $s['items'][2]['show_desktop'].'/'.$s['items'][2]['show_mobile'], '0/0');

echo "\n=== 4. ADMIN SEGMENTED CONTROL ===\n";
$GLOBALS['__options']['mlsc_options'] = array('items'=>array(
  array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','name'=>'WA','color'=>'#25D366','show_desktop'=>1,'show_mobile'=>1),
  array('channel'=>'totop','name'=>'Back to Top','color'=>'#4E7A54'),
));
ob_start(); mlsc_settings_page(); $pg2 = ob_get_clean();
chk('switch container present', (strpos($pg2,'mlsc-device-switch')!==false)?'yes':'no','yes');
preg_match('/<li class="mlsc-item" data-index="0"[^>]*data-channel="whatsapp".*?<\/li>/s', $pg2, $m0);
$c0 = isset($m0[0])?$m0[0]:'';
chk('whatsapp card has both checkboxes', (strpos($c0,'[show_desktop]')!==false && strpos($c0,'[show_mobile]')!==false)?'yes':'no','yes');
chk('both checked by default', substr_count($c0,'checked="checked"') >= 2 ? 'yes':'no','yes');
chk('desktop icon svg present', (strpos($c0,'M21 2H3c-1.1 0-2 .9-2 2v12')!==false)?'yes':'no','yes');
chk('mobile icon svg present', (strpos($c0,'M17 1H7c-1.1 0-2 .9-2 2v18')!==false)?'yes':'no','yes');
preg_match('/<li class="mlsc-item" data-index="1"[^>]*data-channel="totop".*?<\/li>/s', $pg2, $m1);
$c1 = isset($m1[0])?$m1[0]:'';
chk('totop card also has the switch', (strpos($c1,'[show_desktop]')!==false)?'yes':'no','yes');

echo "\nFAILURES: $fail\n";
