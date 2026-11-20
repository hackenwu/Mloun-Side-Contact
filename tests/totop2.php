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

$GLOBALS['__options']['mlsc_options'] = array('items'=>array(array('channel'=>'totop')));
ob_start(); mlsc_settings_page(); $pg = ob_get_clean();

echo "=== SETTINGS PAGE (scoped assertions) ===\n";

// 1. hover_label select: scope to that select element only
preg_match('/<select name="mlsc_options\[hover_label\]"[^>]*>(.*?)<\/select>/s', $pg, $m);
chk('hover_label select found', isset($m[1])?'yes':'no','yes');
$opts = preg_match_all('/<option value="([a-z]+)"/', $m[1], $om) ? $om[1] : array();
chk('exactly 3 hover modes', implode(',', $opts), 'slide,tooltip,none');
chk('slide selected by default', (bool)preg_match('/value="slide"\s+selected="selected"/', $m[1]), 1);

// 2. totop saved card: extract the real card (not the template) and inspect it
preg_match('/<li class="mlsc-item" data-index="0"[^>]*data-channel="totop".*?<\/li>/s', $pg, $cm);
$card = isset($cm[0]) ? $cm[0] : '';
chk('totop saved card found', $card !== '' ? 'yes':'no', 'yes');
chk('card has NO url preview', (strpos($card,'mlsc-url-preview')===false)?'none':'HAS','none');
chk('card has NO value input', (strpos($card,'[value]')===false)?'none':'HAS','none');
chk('card has NO qr input in advanced', (strpos($card,'[qr]')===false)?'none':'HAS','none');
chk('card shows action note', (strpos($card,'Nothing to fill in here')!==false)?'yes':'no','yes');
chk('card still has color + label fields', (strpos($card,'[color]')!==false && strpos($card,'[name]')!==false)?'yes':'no','yes');

// 3. totop template likewise
preg_match('#<script type="text/html" id="mlsc-tpl-totop">(.*?)</script>#s', $pg, $tm);
chk('totop template exists', isset($tm[1])?'yes':'no','yes');
chk('template has __INDEX__', (strpos($tm[1],'__INDEX__')!==false)?'yes':'no','yes');
chk('template has NO url preview', (strpos($tm[1],'mlsc-url-preview')===false)?'none':'HAS','none');

// 4. a normal channel template DOES have preview + value
preg_match('#<script type="text/html" id="mlsc-tpl-whatsapp">(.*?)</script>#s', $pg, $wm);
chk('whatsapp template has preview', (strpos($wm[1],'mlsc-url-preview')!==false)?'yes':'no','yes');
chk('whatsapp template has value input', (strpos($wm[1],'[value]')!==false)?'yes':'no','yes');

echo "\nFAILURES: $fail\n";
