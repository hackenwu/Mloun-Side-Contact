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

function render_with($opts) {
    $GLOBALS['__options']['mlsc_options'] = $opts;
    $errs = array();
    set_error_handler(function($n,$s) use (&$errs){ $errs[]=$s; return true; });
    ob_start(); mlsc_render_frontend(); $html = ob_get_clean();
    restore_error_handler();
    return array($html, $errs);
}

echo "=== 1. BACK TO TOP CHANNEL ===\n";
$chs = mlsc_get_channels();
chk('totop channel exists', isset($chs['totop'])?'yes':'no','yes');
chk('totop marked bar_last', !empty($chs['totop']['bar_last'])?'yes':'no', 'yes');
chk('totop type is action', $chs['totop']['type'], 'action');
chk('arrowup icon renders', strlen(mlsc_get_svg_icon('arrowup')) > 100 ? 'yes':'no','yes');
chk('arrowup is 24x24', (strpos(mlsc_get_svg_icon('arrowup'),'viewBox="0 0 24 24"')!==false)?'yes':'no','yes');
chk('totop has no build url', $chs['totop']['build'], '');

echo "\n=== 2. TOTOP RENDERS OUTSIDE THE BAR ===\n";
list($h, $e) = render_with(array(
  'items'=>array(
    array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','name'=>'WhatsApp','color'=>'#25D366'),
    array('channel'=>'totop','name'=>'Back to Top','color'=>'#4E7A54'),
  ),
  'contact_text'=>'CONTACT US','show_contact_text'=>1,'contact_link'=>'https://x/','position'=>'right','hover_label'=>'slide'));
chk('no warnings', count($e), 0);
chk('bar renders both items', substr_count($h,'class="item-inner '), 2);
chk('totop rendered as a bar li', (bool)preg_match('/<li class="[^"]*mlsc-totop-item[^"]*"/', $h), 1);
chk('totop trigger is the item-inner', (bool)preg_match('/data-mlsc-totop="1"/', $h), 1);
chk('totop has aria-label', (strpos($h,'aria-label="Back to Top"')!==false)?'yes':'no','yes');
chk('totop arrow icon inside', (strpos($h,'M4 12l1.41 1.41L11 7.83V20h2V7.83')!==false)?'yes':'no','yes');

echo "\n=== 3. TOTOP INHERITS WIDGET SETTINGS ===\n";
list($hl, $el) = render_with(array(
  'items'=>array(array('channel'=>'totop','name'=>'Top','color'=>'#333')),
  'contact_text'=>'CONTACT US','show_contact_text'=>1,'contact_link'=>'https://x/',
  'position'=>'left','hover_label'=>'slide','widget_style'=>'square','size_preset'=>'small'));
chk('no warnings', count($el), 0);
chk('inherits style preset', (strpos($hl,'mlsc-style-square')!==false)?'yes':'no','yes');
chk('inherits size vars', (strpos($hl,'--mlsc-size:40px')!==false)?'yes':'no','yes');
chk('inherits icon var', (strpos($hl,'--mlsc-icon:24px')!==false)?'yes':'no','yes');
chk('lives inside the bar ul', (bool)preg_match('/<ul>.*mlsc-totop-item.*<\/ul>/s', $hl), 1);

echo "\n=== 4. HOVER LABEL MODES (tooltip no longer duplicates) ===\n";
$base = array(
  'items'=>array(array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','name'=>'Chat on WhatsApp','color'=>'#25D366')),
  'contact_text'=>'CONTACT US','show_contact_text'=>1,'contact_link'=>'https://x/','position'=>'right');

$m = $base; $m['hover_label']='slide';
list($hs,$es) = render_with($m);
chk('slide: wrapper class', (strpos($hs,'mlsc-label-slide')!==false)?'yes':'no','yes');
chk('slide: NO tooltip attribute', (strpos($hs,'data-mlsc-tooltip')===false)?'none':'PRESENT','none');

$m = $base; $m['hover_label']='tooltip'; $m['tooltip_position']='left';
list($ht,$et) = render_with($m);
chk('tooltip: wrapper class', (strpos($ht,'mlsc-label-tooltip')!==false)?'yes':'no','yes');
chk('tooltip: attribute present', (strpos($ht,'data-mlsc-tooltip="Chat on WhatsApp"')!==false)?'yes':'no','yes');
chk('tooltip: position honoured', (strpos($ht,'data-mlsc-tooltip-pos="left"')!==false)?'yes':'no','yes');
chk('tooltip: label text still in DOM', (strpos($ht,'<span class="text">Chat on WhatsApp</span>')!==false)?'yes':'no','yes');

$m = $base; $m['hover_label']='none';
list($hn,$en) = render_with($m);
chk('none: wrapper class', (strpos($hn,'mlsc-label-none')!==false)?'yes':'no','yes');
chk('none: no tooltip attribute', (strpos($hn,'data-mlsc-tooltip')===false)?'none':'PRESENT','none');

echo "\n=== 5. LEGACY MIGRATION (1.2.x tooltip_enabled=1) ===\n";
$GLOBALS['__options']['mlsc_options'] = array(
  'tooltip_enabled'=>1,'tooltip_position'=>'right',
  'items'=>array(array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','name'=>'WA','color'=>'#25D366')),
);
$o = mlsc_get_options();
chk('migrated to tooltip mode', $o['hover_label'], 'tooltip');
$GLOBALS['__options']['mlsc_options'] = array(
  'tooltip_enabled'=>1,'hover_label'=>'slide',
  'items'=>array(array('channel'=>'whatsapp','cc'=>'86','value'=>'1','name'=>'WA')),
);
chk('explicit hover_label wins over legacy', mlsc_get_options()['hover_label'], 'slide');
$GLOBALS['__options']['mlsc_options'] = array('items'=>array(array('channel'=>'whatsapp','value'=>'1')));
chk('fresh install defaults to slide', mlsc_get_options()['hover_label'], 'slide');

echo "\n=== 6. SANITIZE ===\n";
chk('valid mode kept', mlsc_sanitize_options(array('hover_label'=>'tooltip'))['hover_label'], 'tooltip');
chk('bogus mode rejected', mlsc_sanitize_options(array('hover_label'=>'javascript:alert(1)'))['hover_label'], 'slide');
chk('missing mode defaults', mlsc_sanitize_options(array())['hover_label'], 'slide');

echo "\n=== 7. SETTINGS PAGE ===\n";
$GLOBALS['__options']['mlsc_options'] = array('items'=>array(array('channel'=>'totop')));
$errs=array(); set_error_handler(function($n,$s)use(&$errs){$errs[]=$s;return true;});
ob_start(); mlsc_settings_page(); $pg = ob_get_clean();
restore_error_handler();
chk('no warnings', count($errs), 0);
chk('hover label select present', (strpos($pg,'mlsc_options[hover_label]')!==false)?'yes':'no','yes');
preg_match('/<select name="mlsc_options\[hover_label\]"[^>]*>(.*?)<\/select>/s', $pg, $hm);
$hopts = preg_match_all('/<option value="([a-z]+)"/', $hm[1], $om) ? $om[1] : array();
chk('three label modes offered', implode(',', $hopts), 'slide,tooltip,none');
chk('old tooltip checkbox gone', (strpos($pg,'mlsc_options[tooltip_enabled]')===false)?'gone':'STILL','gone');
chk('totop tile in picker', (strpos($pg,'data-channel="totop"')!==false)?'yes':'no','yes');
preg_match('/<li class="mlsc-item" data-index="0"[^>]*data-channel="totop".*?<\/li>/s', $pg, $cm);
$tcard = isset($cm[0]) ? $cm[0] : '';
chk('totop saved card found', $tcard !== '' ? 'yes':'no', 'yes');
chk('totop card has no value input', (strpos($tcard,'[value]')===false)?'none':'HAS','none');
chk('totop card shows action note', (strpos($tcard,'Nothing to fill in here')!==false)?'yes':'no','yes');
chk('totop card hides url preview', (strpos($tcard,'mlsc-url-preview')===false)?'none':'HAS','none');
chk('picker tile count (21 visible incl totop)', substr_count($pg,'class="mlsc-channel-tile'), 21);

echo "\nFAILURES: $fail\n";
