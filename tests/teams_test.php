<?php
define('ABSPATH', __DIR__ . '/');
$GLOBALS['__options'] = array();
function add_action(){} function add_filter(){} function apply_filters($h,$v){ return $v; }
function plugin_dir_path($f){ return dirname($f).'/'; } function plugin_dir_url($f){ return ''; }
function plugins_url($p='',$f=''){ return ''; } function plugin_basename($f){ return ''; }
function get_option($k,$d=false){ return array_key_exists($k,$GLOBALS['__options'])?$GLOBALS['__options'][$k]:$d; }
function update_option($k,$v){ return true; }
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
function wp_strip_all_tags($s){ return strip_tags((string)$s); }
require $argv[1];
$fail=0;
function chk($n,$g,$w){ global $fail; $ok=((string)$g===(string)$w); printf("  [%s] %s\n",$ok?'PASS':'FAIL',$n); if(!$ok){echo "        got : ".var_export($g,true)."\n        want: ".var_export($w,true)."\n"; $fail++;} }

echo "=== TEAMS REPLACES SKYPE ===\n";
$chs = mlsc_get_channels();
chk('teams channel exists', isset($chs['teams'])?'yes':'no', 'yes');
chk('teams label', $chs['teams']['label'], 'Microsoft Teams');
chk('teams type is email', $chs['teams']['type'], 'email');
chk('teams brand color', $chs['teams']['color'], '#6264A7');
chk('skype marked hidden', !empty($chs['skype']['hidden'])?'yes':'no', 'yes');
chk('teams icon renders (not empty)', strlen(mlsc_get_svg_icon('teams')) > 100 ? 'yes':'no', 'yes');
chk('teams icon is 24x24', (strpos(mlsc_get_svg_icon('teams'),'viewBox="0 0 24 24"')!==false)?'yes':'no','yes');

echo "\n=== TEAMS LINK ===\n";
chk('teams deep link',
    mlsc_build_link(array('channel'=>'teams','value'=>'hacken@mloun.com')),
    'https://teams.microsoft.com/l/chat/0/0?users=hacken%40mloun.com');
chk('teams pasted URL wins',
    mlsc_build_link(array('channel'=>'teams','value'=>'https://teams.microsoft.com/l/team/xyz')),
    'https://teams.microsoft.com/l/team/xyz');
chk('legacy skype still resolves',
    mlsc_build_link(array('channel'=>'skype','value'=>'live:abc')),
    'skype:live:abc?chat');

echo "\n=== PICKER EXCLUDES HIDDEN ===\n";
$GLOBALS['__options']['mlsc_options'] = array('items'=>array());
ob_start(); mlsc_settings_page(); $page = ob_get_clean();
chk('total channels defined', count($chs), 22);
chk('picker tiles (skype hidden)', substr_count($page,'class="mlsc-channel-tile'), 21);
chk('templates rendered', substr_count($page,'type="text/html" id="mlsc-tpl-'), 21);
chk('no skype template', (strpos($page,'id="mlsc-tpl-skype"')===false)?'gone':'STILL THERE', 'gone');
chk('teams template present', (strpos($page,'id="mlsc-tpl-teams"')!==false)?'yes':'no','yes');
chk('teams tile present', (strpos($page,'data-channel="teams"')!==false)?'yes':'no','yes');

echo "\n=== LEGACY SKYPE ITEM STILL RENDERS ===\n";
$GLOBALS['__options']['mlsc_options'] = array(
  'items'=>array(array('channel'=>'skype','value'=>'live:abc','name'=>'Skype','color'=>'#00AFF0')),
  'contact_text'=>'CONTACT US','show_contact_text'=>1,'contact_link'=>'https://x/');
$errs=array(); set_error_handler(function($n,$s)use(&$errs){$errs[]=$s;return true;});
ob_start(); mlsc_render_frontend(); $fe = ob_get_clean();
ob_start(); mlsc_settings_page(); $pg = ob_get_clean();
restore_error_handler();
chk('legacy skype frontend link', (strpos($fe,'href="skype:live:abc?chat"')!==false)?'yes':'no','yes');
chk('legacy skype card renders in admin', (strpos($pg,'data-channel="skype"')!==false)?'yes':'no','yes');
chk('no warnings', count($errs), 0);

echo "\n=== CUSTOM ICON NO LONGER EMPTY ===\n";
chk('custom icon not empty', strlen(mlsc_get_svg_icon('custom')) > 100 ? 'yes':'no','yes');
chk('unknown key falls back to phone', mlsc_get_svg_icon('nope-key'), mlsc_get_svg_icon('phone'));

echo "\nFAILURES: $fail\n";
