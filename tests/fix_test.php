<?php
define('ABSPATH', __DIR__ . '/');
$GLOBALS['__options'] = array();
function add_action(){} function add_filter(){} function apply_filters($h,$v){ return $v; }
function plugin_dir_path($f){ return dirname($f).'/'; } function plugin_dir_url($f){ return ''; }
function plugins_url($p='',$f=''){ return ''; } function plugin_basename($f){ return ''; }
function get_option($k,$d=false){ return array_key_exists($k,$GLOBALS['__options'])?$GLOBALS['__options'][$k]:$d; }
function update_option($k,$v){ $GLOBALS['__options'][$k]=$v; return true; }
function esc_html($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }
function esc_attr($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }
function esc_textarea($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }
function esc_js($s){ return addslashes((string)$s); }
function esc_url($s){ return $s; } function esc_url_raw($s){ return trim((string)$s); }
function esc_html__($s,$d=null){ return esc_html($s); } function esc_html_e($s,$d=null){ echo esc_html($s); }
function esc_attr__($s,$d=null){ return esc_attr($s); } function esc_attr_e($s,$d=null){ echo esc_attr($s); }
function __($s,$d=null){ return $s; }
function _n($s,$p,$n,$d=null){ return 1==$n?$s:$p; } function _x($s,$c,$d=null){ return $s; }
 function _e($s,$d=null){ echo $s; }
function wp_kses($s,$a){ return $s; } function sanitize_text_field($s){ return is_string($s)?trim(strip_tags($s)):''; }
function sanitize_textarea_field($s){ return is_string($s)?trim(strip_tags($s)):''; }
function sanitize_email($s){ $s=trim((string)$s); return filter_var($s,FILTER_VALIDATE_EMAIL)?$s:''; }
function sanitize_hex_color($s){ $s=trim((string)$s); return preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/',$s)?$s:null; }
function wp_parse_args($a,$d){ if(!is_array($a))$a=array(); return array_merge($d,$a); }
function checked($a,$b=true,$e=true){ $r=((string)$a===(string)$b)?' checked="checked"':''; if($e)echo $r; return $r; }
function selected($a,$b=true,$e=true){ $r=((string)$a===(string)$b)?' selected="selected"':''; if($e)echo $r; return $r; }
function wp_enqueue_style(){} function wp_enqueue_script(){} function wp_register_script(){} function wp_localize_script(){}
function wp_add_inline_script(){} function wp_add_inline_style(){} function wp_json_encode($d){ return json_encode($d); }
function current_user_can(){ return true; } function settings_errors(){} function submit_button(){}
function wp_nonce_field(){} function settings_fields(){} function do_settings_sections(){}
function admin_url($p=''){ return ''; } function get_locale(){ return 'zh_CN'; } function absint($v){ return abs(intval($v)); }
function load_plugin_textdomain(){} function is_admin(){ return true; } function remove_all_actions(){}
function add_menu_page(){} function add_submenu_page(){} function register_setting(){}

function sanitize_html_class($c,$f=''){ $c=preg_replace('/[^A-Za-z0-9_-]/','',(string)$c); return $c===''?$f:$c; }
function wp_strip_all_tags($s){ return strip_tags((string)$s); }
require $argv[1];
$fail = 0;
function chk($n,$g,$w){ global $fail; $ok=((string)$g===(string)$w); printf("  [%s] %s\n",$ok?'PASS':'FAIL',$n); if(!$ok){echo "        got : ".var_export($g,true)."\n        want: ".var_export($w,true)."\n"; $fail++;} }

/* ---- render empty settings page ---- */
$GLOBALS['__options']['mlsc_options'] = array('items'=>array());
ob_start(); mlsc_settings_page(); $page = ob_get_clean();

echo "=== A. STRUCTURE ===\n";
chk('page has 2 tab panels', substr_count($page,'class="mlsc-tab-panel'), 2);
chk('panel 1 active', (strpos($page,'mlsc-tab-panel is-active" data-panel="1"')!==false)?'yes':'no','yes');
chk('panel 2 not active', (strpos($page,'mlsc-tab-panel" data-panel="2"')!==false)?'yes':'no','yes');
chk('steps are buttons', substr_count($page,'<button type="button" class="mlsc-step'), 2);
chk('goto buttons present', substr_count($page,'class="button button-primary mlsc-goto"') + substr_count($page,'class="button mlsc-goto"'), 2);
$open  = substr_count($page,'<div');
$close = substr_count($page,'</div>');
chk('div balance', $open, $close);

echo "\n=== B. TEMPLATE HAS data-index (the bug) ===\n";
chk('templates carry data-index="__INDEX__"', substr_count($page,'data-index="__INDEX__"'), 21);
chk('names carry [__INDEX__]', (strpos($page,'mlsc_options[items][__INDEX__][channel]')!==false)?'yes':'no','yes');
chk('no template missing data-index', substr_count($page,'<li class="mlsc-item" data-index='), 21);

echo "\n=== C. SIMULATE ADDING 3 CHANNELS THEN SAVING ===\n";
/* Mimic what admin.js does: clone template, replace __INDEX__, append.
   nextIndex() = max(existing data-index) + 1, starting at 0 for an empty list. */
$existing = array();            // data-index values already in the DOM
preg_match_all('/<li class="mlsc-item" data-index="(\d+|[^"]+)"/', $page, $m);
foreach ($m[1] as $v) { if (is_numeric($v)) $existing[] = (int)$v; }

function tpl_for($page, $channel) {
    if (preg_match('#<script type="text/html" id="mlsc-tpl-' . $channel . '">(.*?)</script>#s', $page, $mm)) {
        return $mm[1];
    }
    return '';
}

$added = array();
foreach (array('whatsapp','telegram','phone') as $channel) {
    $idx = $existing ? (max($existing) + 1) : 0;
    $existing[] = $idx;
    $html = str_replace('__INDEX__', (string) $idx, tpl_for($page, $channel));
    $added[$idx] = $html;
    echo "    added $channel at index $idx\n";
}
chk('3 unique indices assigned', count(array_unique(array_keys($added))), 3);

/* Build the $_POST array PHP would receive from those cards */
$post = array('items' => array());
foreach ($added as $idx => $html) {
    // pull the input names out of the generated card
    preg_match_all('/name="mlsc_options\[items\]\[' . $idx . '\]\[([a-z_]+)\]"(?:[^>]*value="([^"]*)")?/', $html, $nm, PREG_SET_ORDER);
    $row = array();
    foreach ($nm as $one) {
        $row[$one[1]] = isset($one[2]) ? html_entity_decode($one[2], ENT_QUOTES) : '';
    }
    // simulate the values the user typed
    if ('whatsapp' === $row['channel']) { $row['cc'] = '86'; $row['value'] = '13800000000'; }
    if ('telegram' === $row['channel']) { $row['value'] = '@mloun'; }
    if ('phone'    === $row['channel']) { $row['cc'] = '86'; $row['value'] = '13900000000'; }
    $post['items'][$idx] = $row;
}
echo '    POST item keys: ' . implode(', ', array_keys($post['items'])) . "\n";

$saved = mlsc_sanitize_options($post);
chk('SAVED ITEM COUNT (was 1, must be 3)', count($saved['items']), 3);
$chans = array();
foreach ($saved['items'] as $it) { $chans[] = $it['channel']; }
chk('all three channels survived', implode(',', $chans), 'whatsapp,telegram,phone');
chk('whatsapp value intact', $saved['items'][0]['value'], '13800000000');
chk('telegram value intact', $saved['items'][1]['value'], '@mloun');

echo "\n=== D. RENDER ALL 3 BACK ===\n";
$GLOBALS['__options']['mlsc_options'] = array('items'=>$saved['items'],'contact_text'=>'CONTACT US','show_contact_text'=>1,'contact_link'=>'https://x/');
$errs=array(); set_error_handler(function($n,$s)use(&$errs){$errs[]=$s;return true;});
ob_start(); mlsc_render_frontend(); $fe = ob_get_clean();
restore_error_handler();
chk('frontend: 3 buttons rendered', substr_count($fe,'class="item-inner'), 3);
chk('frontend: no warnings', count($errs), 0);
chk('frontend: wa.me link', (strpos($fe,'https://wa.me/8613800000000')!==false)?'yes':'no','yes');
chk('frontend: t.me link', (strpos($fe,'https://t.me/mloun')!==false)?'yes':'no','yes');
chk('frontend: tel link', (strpos($fe,'tel:+8613900000000')!==false)?'yes':'no','yes');

echo "\n=== E. ICONS ===\n";
foreach (array('messenger','line','viber','skype','zalo') as $k) {
    $svg = mlsc_get_svg_icon($k);
    chk("icon $k is 24x24 official", (strpos($svg,'viewBox="0 0 24 24"')!==false && substr_count($svg,'<path')>=1)?'yes':'no', 'yes');
}

echo "\nFAILURES: $fail\n";
