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
function sanitize_email($s){ return trim((string)$s); }
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

function render($extra) {
    $GLOBALS['__options']['mlsc_options'] = array_merge(array(
      'items'=>array(array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','name'=>'WhatsApp','color'=>'#25D366','hover_color'=>'#128C7E','blank'=>1)),
      'contact_text'=>'CONTACT US','show_contact_text'=>1,'contact_link'=>'https://x/','position'=>'right','hover_label'=>'slide',
    ), $extra);
    $errs=array(); set_error_handler(function($n,$s)use(&$errs){$errs[]=$s;return true;});
    ob_start(); mlsc_render_frontend(); $h = ob_get_clean();
    restore_error_handler();
    return array($h, $errs);
}

echo "=== 1. DEFAULTS ===\n";
$d = mlsc_get_defaults();
chk('widget_style default', $d['widget_style'], 'classic');
chk('size_preset default', $d['size_preset'], 'medium'); // v1.4.12: three fixed steps
chk('button_gap default', $d['button_gap'], '0');

echo "\n=== 2. SANITIZE ===\n";
foreach (array('classic','minimal','rounded','square','outline') as $preset) {
    chk("preset $preset kept", mlsc_sanitize_options(array('widget_style'=>$preset))['widget_style'], $preset);
}
chk('bogus preset rejected', mlsc_sanitize_options(array('widget_style'=>'<script>'))['widget_style'], 'classic');
chk('missing preset defaults', mlsc_sanitize_options(array())['widget_style'], 'classic');
chk('size preset small kept', mlsc_sanitize_options(array('size_preset'=>'small'))['size_preset'], 'small');
chk('size preset large kept', mlsc_sanitize_options(array('size_preset'=>'large'))['size_preset'], 'large');
chk('bogus size preset -> medium', mlsc_sanitize_options(array('size_preset'=>'huge'))['size_preset'], 'medium');
list($hx) = render(array('size_preset'=>'small','button_size'=>64,'icon_size'=>32));
chk('stale button_size ignored', (strpos($hx,'--mlsc-size:40px')!==false && strpos($hx,'--mlsc-size:64px')===false)?'yes':'no','yes');
chk('stale icon_size ignored', (strpos($hx,'--mlsc-icon:24px')!==false && strpos($hx,'--mlsc-icon:32px')===false)?'yes':'no','yes');
chk('gap clamped high', mlsc_sanitize_options(array('button_gap'=>99))['button_gap'], 16);
chk('gap negative -> 0', mlsc_sanitize_options(array('button_gap'=>-5))['button_gap'], 0);

echo "\n=== 3. RENDER: wrapper classes + css vars ===\n";
list($h,$e) = render(array());
chk('no warnings', count($e), 0);
chk('style class emitted', (strpos($h,'mlsc-style-classic')!==false)?'yes':'no','yes');
chk('size var emitted (medium 46)', (strpos($h,'--mlsc-size:46px')!==false)?'yes':'no','yes');
chk('icon var emitted (medium 28)', (strpos($h,'--mlsc-icon:28px')!==false)?'yes':'no','yes');
chk('gap var emitted', (strpos($h,'--mlsc-gap:0px')!==false)?'yes':'no','yes');
chk('solid keeps inline background', (strpos($h,'background:#25D366')!==false)?'yes':'no','yes');
chk('solid has NO item-color var', (strpos($h,'--mlsc-item-color')===false)?'none':'PRESENT','none');

echo "\n=== 4. SIZE PRESET FLOWS THROUGH ===\n";
list($h2,$e2) = render(array('size_preset'=>'large','button_gap'=>6,'widget_style'=>'rounded'));
chk('no warnings', count($e2), 0);
chk('size 52 (large)', (strpos($h2,'--mlsc-size:52px')!==false)?'yes':'no','yes');
chk('icon 34 (large)', (strpos($h2,'--mlsc-icon:34px')!==false)?'yes':'no','yes');
chk('gap 6', (strpos($h2,'--mlsc-gap:6px')!==false)?'yes':'no','yes');
chk('rounded class', (strpos($h2,'mlsc-style-rounded')!==false)?'yes':'no','yes');

echo "\n=== 5. OUTLINE MODE ===\n";
list($ho,$eo) = render(array('widget_style'=>'outline'));
chk('no warnings', count($eo), 0);
chk('outline class', (strpos($ho,'mlsc-style-outline')!==false)?'yes':'no','yes');
chk('uses --mlsc-item-color', (strpos($ho,'--mlsc-item-color:#25D366')!==false)?'yes':'no','yes');
chk('uses --mlsc-item-hover', (strpos($ho,'--mlsc-item-hover:#128C7E')!==false)?'yes':'no','yes');
chk('NO inline background on button', (strpos($ho,'background:#25D366')===false)?'none':'PRESENT','none');

echo "\n=== 6. TOTOP FOLLOWS STYLE ===\n";
list($ht,$et) = render(array(
  'widget_style'=>'square','size_preset'=>'small',
  'items'=>array(array('channel'=>'totop','name'=>'Back to Top','color'=>'#4E7A54'))));
chk('no warnings', count($et), 0);
chk('totop has style class', (strpos($ht,'mlsc-style-square')!==false)?'yes':'no','yes');
chk('totop has size vars', (strpos($ht,'--mlsc-size:40px')!==false)?'yes':'no','yes');
chk('totop has icon var', (strpos($ht,'--mlsc-icon:24px')!==false)?'yes':'no','yes');

echo "\n=== 7. SETTINGS PAGE ===\n";
$GLOBALS['__options']['mlsc_options'] = array('items'=>array(),'widget_style'=>'rounded','size_preset'=>'large','button_gap'=>'4');
$errs=array(); set_error_handler(function($n,$s)use(&$errs){$errs[]=$s;return true;});
ob_start(); mlsc_settings_page(); $pg = ob_get_clean();
restore_error_handler();
chk('no warnings', count($errs), 0);
chk('5 preset cards', substr_count($pg,'class="mlsc-style-card'), 5);
chk('rounded is active', (bool)preg_match('/class="mlsc-style-card is-active">\s*<input type="radio" name="mlsc_options\[widget_style\]" value="rounded"/', $pg), 1);
chk('rounded radio checked', (bool)preg_match('/value="rounded"\s+checked="checked"/', $pg), 1);
chk('size preset radios present', substr_count($pg,'name="mlsc_options[size_preset]"'), 3);
chk('gap input value 4', (bool)preg_match('/name="mlsc_options\[button_gap\]" value="4"/', $pg), 1);
chk('style section heading', (strpos($pg,'Buttons &amp; Style')!==false)?'yes':'no','yes');
$pos_style = strpos($pg,'Buttons &amp; Style');
$pos_side  = strpos($pg,'Side Button');
chk('style section before Side Button', ($pos_style !== false && $pos_side !== false && $pos_style < $pos_side) ? 'yes':'no', 'yes');
chk('all 5 preset radios present', preg_match_all('/name="mlsc_options\[widget_style\]" value="(classic|minimal|rounded|square|outline)"/', $pg), 5);

echo "\nFAILURES: $fail\n";
