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

$items = array(
  array('channel'=>'whatsapp','cc'=>'86','value'=>'13800000000','name'=>'WA','color'=>'#25D366'),
  array('channel'=>'telegram','value'=>'mloun','name'=>'TG','color'=>'#0088CC'),
  array('channel'=>'totop','name'=>'Back to Top','color'=>'#4E7A54'),
);
function render($extra){
  global $items;
  $GLOBALS['__options']['mlsc_options'] = array_merge(array(
    'items'=>$items,'contact_text'=>'','show_contact_text'=>0,'contact_link'=>'x',
    'position'=>'right','hover_label'=>'slide','widget_style'=>'classic'), $extra);
  $e=array(); set_error_handler(function($n,$s)use(&$e){$e[]=$s;return true;});
  ob_start(); mlsc_render_frontend(); $h=ob_get_clean(); restore_error_handler();
  return array($h,$e);
}

echo "=== 1. DEFAULTS ===\n";
$d = mlsc_get_defaults();
chk('icon default chevron', $d['toggle_icon'], 'chevron');
chk('folded text default', $d['toggle_text'], 'Hide');
chk('open text default', $d['toggle_text_open'], 'Contact Us');
chk('no toggle_style key', array_key_exists('toggle_style',$d)?'PRESENT':'none', 'none');

echo "\n=== 2. SANITIZE ===\n";
foreach (array('chevron','bubble','bubble-dots','bubble-round','bubble-typing','bubble-smile','bubble-quote','bubble-bot','bubble-lang','bubble-group','bubble-call','plus','menu','dots','tune') as $ic) {
  chk("icon $ic kept", mlsc_sanitize_options(array('toggle_icon'=>$ic))['toggle_icon'], $ic);
}
chk('close icon removed', mlsc_sanitize_options(array('toggle_icon'=>'close'))['toggle_icon'], 'chevron');
chk('chat no longer accepted', mlsc_sanitize_options(array('toggle_icon'=>'chat'))['toggle_icon'], 'chevron');
chk('bogus icon -> chevron', mlsc_sanitize_options(array('toggle_icon'=>'<img onerror=x>'))['toggle_icon'], 'chevron');
chk('custom folded text', mlsc_sanitize_options(array('toggle_text'=>'Ukryj'))['toggle_text'], 'Ukryj');
chk('custom open text', mlsc_sanitize_options(array('toggle_text_open'=>"Let's Talk"))['toggle_text_open'], "Let's Talk");
chk('empty -> default', mlsc_sanitize_options(array('toggle_text'=>'  '))['toggle_text'], 'Hide');

echo "\n=== 3. BUBBLE MARKUP ===\n";
list($h,$e) = render(array('collapse_mode'=>'closed'));
chk('no warnings', count($e), 0);
chk('bubble carries folded text', strpos($h,'data-mlsc-tooltip="Hide"')!==false?'yes':'no','yes');
chk('bubble carries open text', strpos($h,'data-mlsc-tip-open="Contact Us"')!==false?'yes':'no','yes');
chk('bubble has a side', (bool)preg_match('/data-mlsc-tooltip-pos="(left|right)"/', $h), 1);
chk('no inline .text label inside toggle', preg_match('/mlsc-toggle">\s*<span class="text/', $h) ? 'HAS' : 'none', 'none');
chk('toggle is a button', (bool)preg_match('/<button type="button"\s+class="item-inner[^"]*mlsc-toggle"/', $h), 1);
chk('aria-expanded false', strpos($h,'aria-expanded="false"')!==false?'yes':'no','yes');

echo "\n=== 4. ICON FACES ===\n";
list($hc) = render(array('collapse_mode'=>'closed','toggle_icon'=>'chevron'));
chk('chevron up when folded', strpos($hc,'M12 7.4l6.3 6.3')!==false?'yes':'no','yes');
chk('open state shows a cross', strpos($hc,'M6.4 5.3 12 10.9')!==false?'yes':'no','yes');
list($hp) = render(array('collapse_mode'=>'closed','toggle_icon'=>'plus'));
chk('plus icon used', strpos($hp,'M10.6 4h2.8v6.6')!==false?'yes':'no','yes');
chk('plus only when collapsed, cross when open', substr_count($hp,'M10.6 4h2.8v6.6'), 1);
chk('plus config still gets a cross when open', strpos($hp,'M6.4 5.3 12 10.9')!==false?'yes':'no','yes');
foreach (array('bubble','bubble-dots','bubble-round','bubble-typing','bubble-smile','bubble-quote','bubble-bot','bubble-lang','bubble-group','bubble-call','menu','dots','tune') as $ic) {
  list($hh) = render(array('collapse_mode'=>'closed','toggle_icon'=>$ic));
  chk("$ic renders both faces", substr_count($hh,'mlsc-svg-icon mlsc-toggle-when-'), 2);
  chk("$ic is solid white", (strpos($hh,'fill="#fff"')!==false && strpos($hh,'stroke="currentColor"')===false)?'yes':'no', 'yes');
}

echo "\n=== 5. ORDER: toggle ABOVE totop ===\n";
chk('toggle before totop', strpos($h,'mlsc-toggle-item') < strpos($h,'mlsc-totop-item') ? 'yes':'no', 'yes');
chk('totop still last', (bool)preg_match('/mlsc-totop-item.*?<\/ul>/s', $h), 1);

echo "\n=== 6. MODES ===\n";
list($h0) = render(array('collapse_mode'=>'off'));
chk('off: no toggle', strpos($h0,'mlsc-toggle-item')===false?'none':'PRESENT','none');
list($h1) = render(array('collapse_mode'=>'closed'));
chk('closed: wrapper collapsed', strpos($h1,'mlsc-collapsed')!==false?'yes':'no','yes');
list($h2) = render(array('collapse_mode'=>'open'));
chk('open: wrapper open', strpos($h2,'mlsc-open')!==false?'yes':'no','yes');
chk('open: aria true', strpos($h2,'aria-expanded="true"')!==false?'yes':'no','yes');

echo "\n=== 7. BOTTOM ANCHORING ===\n";
$GLOBALS['__css'] = '';
$GLOBALS['__options']['mlsc_options'] = array('items'=>$items,'position'=>'right','offset'=>'0','top'=>'120');
mlsc_enqueue_frontend_assets();
$css = $GLOBALS['__css'];
chk('uses bottom', strpos($css,'bottom: 120px')!==false?'yes':'no','yes');
chk('does not use top for distance', strpos($css,'top: 120px')===false?'none':'PRESENT','none');
chk('top reset to auto', strpos($css,'top: auto')!==false?'yes':'no','yes');
$GLOBALS['__css'] = '';
$GLOBALS['__options']['mlsc_options'] = array('items'=>$items,'position'=>'left','offset'=>'8','top'=>'60','center'=>1);
mlsc_enqueue_frontend_assets();
$css2 = $GLOBALS['__css'];
chk('center keeps 50% + translate', strpos($css2,'top: 50%')!==false && strpos($css2,'translateY(-50%)')!==false?'yes':'no','yes');
chk('center clears bottom', strpos($css2,'bottom: auto')!==false?'yes':'no','yes');

echo "\n=== 8. ADMIN ===\n";
$GLOBALS['__options']['mlsc_options'] = array('items'=>array());
ob_start(); mlsc_settings_page(); $pg=ob_get_clean();
chk('no Toggle Style select', strpos($pg,'mlsc_options[toggle_style]')===false?'none':'PRESENT','none');
chk('has icon picker', strpos($pg,'mlsc-icon-picker')!==false?'yes':'no','yes');
chk('15 radio choices', preg_match_all('/name="mlsc_options\[toggle_icon\]" value="([\w-]+)"/', $pg), 15);
chk('no chat in toggle picker', preg_match('/name="mlsc_options\[toggle_icon\]" value="chat"/', $pg) ? 'HAS' : 'none', 'none');
chk('bubble colour fields', (strpos($pg,'mlsc_options[toggle_bubble_bg]')!==false && strpos($pg,'mlsc_options[toggle_bubble_fg]')!==false)?'yes':'no','yes');
chk('always-on checkbox', strpos($pg,'mlsc_options[toggle_bubble_always]')!==false?'yes':'no','yes');
chk('Bottom Distance label', strpos($pg,'Bottom Distance (px)')!==false?'yes':'no','yes');
chk('no Top Distance label', strpos($pg,'Top Distance (px)')===false?'none':'PRESENT','none');
chk('text fields present', (strpos($pg,'mlsc_options[toggle_text]')!==false && strpos($pg,'mlsc_options[toggle_text_open]')!==false)?'yes':'no','yes');

echo "\n=== 9. TOGGLE BUBBLE COLOURS ===\n";
$d2 = mlsc_get_defaults();
chk('bubble bg default', $d2['toggle_bubble_bg'], '#1a1a2e');
chk('bubble fg default', $d2['toggle_bubble_fg'], '#ffffff');
chk('always default off', $d2['toggle_bubble_always'], 0);
chk('bg sanitized', mlsc_sanitize_options(array('toggle_bubble_bg'=>'#ff5722'))['toggle_bubble_bg'], '#ff5722');
chk('fg sanitized', mlsc_sanitize_options(array('toggle_bubble_fg'=>'#112233'))['toggle_bubble_fg'], '#112233');
chk('bad bg -> default', mlsc_sanitize_options(array('toggle_bubble_bg'=>'javascript:x'))['toggle_bubble_bg'], '#1a1a2e');
chk('always on when submitted', mlsc_sanitize_options(array('toggle_bubble_always'=>'1'))['toggle_bubble_always'], 1);
chk('always off when absent', mlsc_sanitize_options(array())['toggle_bubble_always'], 0);

list($hb) = render(array('collapse_mode'=>'closed','toggle_bubble_bg'=>'#ff5722','toggle_bubble_fg'=>'#00ff00','toggle_bubble_always'=>1));
chk('wrapper carries bubble colours', (strpos($hb,'--mlsc-tip-bg:#ff5722')!==false && strpos($hb,'--mlsc-tip-fg:#00ff00')!==false)?'yes':'no','yes');
chk('wrapper carries always class', strpos($hb,'mlsc-tip-always')!==false?'yes':'no','yes');
list($hb2) = render(array('collapse_mode'=>'closed'));
chk('no always class by default', strpos($hb2,'mlsc-tip-always')===false?'none':'PRESENT','none');

echo "\n=== 10. CSS: To Top slot must stay reserved ===\n";
$fcss = file_get_contents(dirname($argv[1]) . '/assets/css/frontend.css');
chk('totop base has no display:none', preg_match('/\.mlsc li\.mlsc-totop-item \{[^}]*display:\s*none/', $fcss) ? 'HAS' : 'none', 'none');
chk('totop uses visibility', preg_match('/\.mlsc li\.mlsc-totop-item \{[^}]*visibility:\s*hidden/', $fcss) ? 'yes' : 'no', 'yes');
chk('locked keeps the slot', preg_match('/\.mlsc\.mlsc-totop-locked li\.mlsc-totop-item \{[^}]*display/', $fcss) ? 'HAS' : 'none', 'none');
// v1.4.32 flipped this pair: the Fold / Unfold setting used to be desktop-only
// (phones were force-expanded). It now applies at every width, so these assert
// the opposite of what they used to.
chk('fold rule is unconditional (all widths)', preg_match('/^\s*\.mlsc\.mlsc-collapsed li:not\(\.mlsc-toggle-item\):not\(\.mlsc-totop-item\) \{/m', $fcss) ? 'yes' : 'no', 'yes');
chk('fold rule is no longer wrapped in a desktop media query', preg_match('/@media \(min-width: 769px\) \{\s*\.mlsc\.mlsc-collapsed/', $fcss) ? 'PRESENT' : 'none', 'none');
// The split is by phone layout: the bottom bar hides the fold control and lays
// every channel flat; only the side rail follows the setting. So the hide rule
// MUST be scoped with :not(.mlsc-mobile-side).
chk('fold toggle hidden only in the bottom-bar layout',
    preg_match('/\.mlsc:not\(\.mlsc-mobile-side\) li\.mlsc-toggle-item \{[^}]*display:\s*none/', $fcss) ? 'yes' : 'no', 'yes');
chk('no unscoped rule hides the fold toggle',
    preg_match('/(?<!:not\(\.mlsc-mobile-side\) )li\.mlsc-toggle-item\s*\{[^}]*display:\s*none/', $fcss) ? 'PRESENT' : 'none', 'none');
chk('collapsed bar keeps totop at natural size', preg_match('/\.mlsc\.mlsc-collapsed li\.mlsc-totop-item \{[^}]*flex:\s*0 0 var\(--mlsc-size\)/', $fcss) ? 'yes' : 'no', 'yes');

echo "\n=== 11. THREE SIZE PRESETS ===\n";
$d3 = mlsc_get_defaults();
chk('size_preset default', $d3['size_preset'], 'medium');
$sp = mlsc_get_size_presets();
chk('three presets', count($sp), 3);
chk('small button', $sp['small']['button'], 40);
chk('small icon', $sp['small']['icon'], 24);
chk('medium button', $sp['medium']['button'], 46);
chk('medium icon', $sp['medium']['icon'], 28);
chk('large button', $sp['large']['button'], 52);
chk('large icon', $sp['large']['icon'], 34);
chk('preset sanitized', mlsc_sanitize_options(array('size_preset'=>'large'))['size_preset'], 'large');
chk('bogus preset -> medium', mlsc_sanitize_options(array('size_preset'=>'x'))['size_preset'], 'medium');
foreach (array('small'=>array(40,24), 'medium'=>array(46,28), 'large'=>array(52,34)) as $k=>$pair) {
  $GLOBALS['__options']['mlsc_options'] = array('items'=>$items,'position'=>'right','hover_label'=>'none',
     'size_preset'=>$k,'widget_style'=>'classic');
  ob_start(); mlsc_render_frontend(); $hh=ob_get_clean();
  preg_match('/--mlsc-size:(\d+)px;--mlsc-icon:(\d+)px/', $hh, $mm);
  chk("$k renders {$pair[0]}/{$pair[1]}", $mm[1].'/'.$mm[2], $pair[0].'/'.$pair[1]);
}

echo "\n=== 13. NEW SOURCE ICONS ===\n";
foreach (array('bubble-dots','bubble-lang','bubble-smile') as $ic) {
  list($hi) = render(array('collapse_mode'=>'closed','toggle_icon'=>$ic));
  chk("$ic is the 800-grid artwork", (strpos($hi,'viewBox="-133 -133 1066 1066"')!==false && strpos($hi,'fill-rule="evenodd"')!==false)?'yes':'no','yes');
  chk("$ic has no <style> or class", (strpos($hi,'class="st0"')===false && strpos($hi,'<style')===false)?'none':'PRESENT','none');
}
chk('close icon absent from svg table', strpos(file_get_contents($argv[1]), "'close' => '<svg")===false?'none':'PRESENT','none');

echo "\n=== 14. BACK TO TOP AT 20% ===\n";
$fjs = file_get_contents(dirname($argv[1]) . '/assets/js/frontend.js');
chk('threshold is 0.2', preg_match('/SCROLL_THRESHOLD\s*=\s*0\.2;/', $fjs) ? 'yes' : 'no', 'yes');
chk('no 0.5 threshold left', preg_match('/SCROLL_THRESHOLD\s*=\s*0\.5;/', $fjs) ? 'PRESENT' : 'none', 'none');

echo "\n=== 12. ICON PICKER UI ===\n";
chk('has visible label per tile', substr_count($pg, 'mlsc-icon-choice-label', 0), 15);
chk('check badge per icon tile', substr_count($pg, 'mlsc-icon-choice-check'), 18);
chk('no icon-size number input', strpos($pg,'mlsc_options[icon_size]')===false?'none':'PRESENT','none');
chk('no button-size number input', strpos($pg,'mlsc_options[button_size]')===false?'none':'PRESENT','none');

echo "\n=== 15. WORDPRESS.ORG REVIEW RULES ===\n";
$SLUG = 'mloun-side-contact-floating-contact-buttons-for-online-chat-wechat-phone';
$dir  = dirname($argv[1]);
$head = file_get_contents($argv[1]);
$hg = $head;
$rd   = file_get_contents($dir . '/readme.txt');

// Text Domain must equal the plugin slug, or Plugin Check reports a mismatch.
preg_match('/^\s*\*?\s*Text Domain:\s*(\S+)/m', $head, $m);
chk('Text Domain equals the slug', isset($m[1]) ? $m[1] : 'none', $SLUG);
preg_match('/^\s*\*?\s*Domain Path:\s*(\S+)/m', $head, $m2);
chk('Domain Path declared', isset($m2[1]) ? $m2[1] : 'none', '/languages');
chk('languages/ exists', is_dir($dir . '/languages') ? 'yes' : 'no', 'yes');

// readme Tags: at most 5, no trailing comma (an empty 6th tag is what triggers
// "One or more tags were ignored").
preg_match('/^Tags:(.*)$/m', $rd, $mt);
$raw  = isset($mt[1]) ? $mt[1] : '';
$tags = array_values(array_filter(array_map('trim', explode(',', $raw)), 'strlen'));
chk('readme has a Tags line', ($raw !== '') ? 'yes' : 'no', 'yes');
chk('at most 5 tags', count($tags), 5);
chk('no trailing comma', rtrim($raw) === '' ? 'empty' : (substr(rtrim($raw), -1) === ',' ? 'HAS' : 'none'), 'none');
chk('tags are lowercase', $tags === array_map('strtolower', $tags) ? 'yes' : 'no', 'yes');
chk('no empty tag between commas', count(explode(',', rtrim($raw, ','))) === count($tags) ? 'yes' : 'no', 'yes');

// Stable tag must match the plugin header version.
preg_match('/^Stable tag:\s*(\S+)/m', $rd, $ms);
preg_match('/^\s*\*?\s*Version:\s*([\d.]+)/m', $head, $mv);
chk('Stable tag matches Version', isset($ms[1]) ? $ms[1] : 'none', isset($mv[1]) ? $mv[1] : '?');

// Plugin header essentials.
// "Tested up to" is deliberately NOT in this list: WordPress.org requires it to be
// declared only in readme.txt, and Plugin Check errors when the PHP header has it
// (plugin_header_tested_up_to_not_allowed).
foreach (array('Plugin Name','Plugin URI','Description','Author','License','Requires at least','Requires PHP') as $field) {
    chk("header has $field", preg_match('/^\s*\*?\s*' . preg_quote($field, '/') . ':/m', $head) ? 'yes' : 'no', 'yes');
}
chk('header must NOT declare Tested up to', preg_match('/^\s*\*?\s*Tested up to:/m', $head) ? 'PRESENT' : 'none', 'none');
chk('readme declares Tested up to', preg_match('/^Tested up to:/m', $rd) ? 'yes' : 'no', 'yes');

echo "\n=== 16. MEDIA LIBRARY FIELDS ===\n";
chk('helper exists', function_exists('mlsc_media_field') ? 'yes' : 'no', 'yes');
chk('no raw URL input for custom_icon', preg_match('/<input type="url" name="<\?php echo esc_attr\( \$pre \); \?>\[custom_icon\]/', $hg) ? 'RAW' : 'none', 'none');
chk('no raw URL input for qr', preg_match('/<input type="url" name="<\?php echo esc_attr\( \$pre \); \?>\[qr\]/', $hg) ? 'RAW' : 'none', 'none');
chk('upload button per image field', substr_count($pg, 'mlsc-media-pick') >= 2 ? 'yes' : 'no', 'yes');
chk('remove link present', strpos($pg, 'mlsc-media-clear') !== false ? 'yes' : 'no', 'yes');
chk('thumbnail holder present', strpos($pg, 'mlsc-media-thumb') !== false ? 'yes' : 'no', 'yes');
ob_start(); mlsc_media_field('mlsc_options[items][0][qr]', 'https://x/y.png', 'QR Code Image', 'help'); $mf = ob_get_clean();
chk('helper renders the url', strpos($mf, 'value="https://x/y.png"') !== false ? 'yes' : 'no', 'yes');
chk('helper marks empty state', strpos($mf, 'is-empty') === false ? 'no(is-empty absent when filled)' : 'yes', 'no(is-empty absent when filled)');
ob_start(); mlsc_media_field('a', '', 'L', ''); $mf2 = ob_get_clean();
chk('empty value -> is-empty class', strpos($mf2, 'mlsc-media-thumb is-empty') !== false ? 'yes' : 'no', 'yes');

echo "\n=== 17. QR SIZE MOVED + PANEL SIZING ===\n";
$pos_w  = strpos($pg, 'mlsc_options[qr_width]');
$pos_wc = strpos($pg, 'Widget customization');
$pos_sb = strpos($pg, 'Side Button');
chk('qr width after Widget customization', $pos_w > $pos_wc ? 'yes' : 'no', 'yes');
chk('qr width before Side Button', $pos_w < $pos_sb ? 'yes' : 'no', 'yes');
chk('qr height moved too', strpos($pg, 'mlsc_options[qr_height]') < $pos_sb ? 'yes' : 'no', 'yes');

$GLOBALS['__options']['mlsc_options'] = array('items'=>$items,'position'=>'right','hover_label'=>'none','qr_width'=>180,'qr_height'=>140,'size_preset'=>'medium');
$css_qr = mlsc_build_inline_css($GLOBALS['__options']['mlsc_options']);
chk('panel width = qr + 10', (strpos($css_qr, 'width: 190px') !== false) ? 'yes' : 'no', 'yes');
chk('panel height = qr + 10', (strpos($css_qr, 'height: 150px') !== false) ? 'yes' : 'no', 'yes');
chk('panel padding 5px', (strpos($css_qr, 'padding: 5px') !== false) ? 'yes' : 'no', 'yes');
chk('image exactly qr size', (strpos($css_qr, '.mlsc .qr img { width: 180px; height: 140px; }') !== false) ? 'yes' : 'no', 'yes');

echo "\n=== 18. SIDE BUTTON FOLLOWS THE PRESET ===\n";
$fcss2 = file_get_contents(dirname($argv[1]) . '/assets/css/frontend.css');
chk('contact-title uses --mlsc-radius', preg_match('/\.mlsc \.contact-title \{[^}]*border-radius:\s*var\(--mlsc-radius\)/', $fcss2) ? 'yes' : 'no', 'yes');
chk('classic radius = half size', preg_match('/\.mlsc-style-classic \{[^}]*--mlsc-radius:\s*calc\(var\(--mlsc-size\) \/ 2\)/', $fcss2) ? 'yes' : 'no', 'yes');
chk('square radius = 0', preg_match('/\.mlsc-style-square \{[^}]*--mlsc-radius:\s*0/', $fcss2) ? 'yes' : 'no', 'yes');
chk('qr panel no fixed width', preg_match('/\.mlsc \.qr \{[^}]*width:\s*190px/', $fcss2) ? 'HAS' : 'none', 'none');

echo "\n=== 19. LIVE PREVIEW PANEL ===\n";
chk('two-column grid', strpos($pg, 'mlsc-admin-grid') !== false ? 'yes' : 'no', 'yes');
chk('main column', strpos($pg, 'mlsc-admin-main') !== false ? 'yes' : 'no', 'yes');
chk('preview aside', strpos($pg, 'id="mlsc-preview"') !== false ? 'yes' : 'no', 'yes');
chk('preview iframe', strpos($pg, 'id="mlsc-preview-frame"') !== false ? 'yes' : 'no', 'yes');
chk('browser chrome bar', strpos($pg, 'mlsc-preview-bar') !== false ? 'yes' : 'no', 'yes');
chk('desktop device button', strpos($pg, 'data-device="desktop"') !== false ? 'yes' : 'no', 'yes');
chk('mobile device button', strpos($pg, 'data-device="mobile"') !== false ? 'yes' : 'no', 'yes');
chk('ajax handler defined', function_exists('mlsc_ajax_preview') ? 'yes' : 'no', 'yes');
chk('render accepts an options array', (function () {
    $GLOBALS['__options']['mlsc_options'] = array('items'=>array(),'position'=>'right','hover_label'=>'none');
    ob_start(); mlsc_render_widget( array('items'=>array(array('channel'=>'whatsapp','cc'=>'86','value'=>'8613800000000','name'=>'X','color'=>'#25D366')),'position'=>'right','hover_label'=>'none'));
    $h = ob_get_clean();
    return (strpos($h, 'mlsc') !== false) ? 'yes' : 'no';
})(), 'yes');
chk('empty options renders nothing', (function () {
    ob_start(); mlsc_render_widget( array('items'=>array())); $h = ob_get_clean();
    return ('' === trim($h)) ? 'none' : 'OUTPUT';
})(), 'none');

$js_admin = file_get_contents(dirname($argv[1]) . '/admin/admin.js');
chk('js posts to the preview action', strpos($js_admin, "action: 'mlsc_preview'") !== false ? 'yes' : 'no', 'yes');
chk('js uses wp.media', strpos($js_admin, 'window.wp.media') !== false ? 'yes' : 'no', 'yes');
chk('js handles device switch', strpos($js_admin, "data-device") !== false ? 'yes' : 'no', 'yes');
chk('js debounces', strpos($js_admin, 'setTimeout( request, 400 )') !== false ? 'yes' : 'no', 'yes');

echo "\n=== 20. PREVIEW DEVICE MODES + WIDTH ===\n";
$adcss = file_get_contents(dirname($argv[1]) . '/admin/admin.css');
$adjs  = file_get_contents(dirname($argv[1]) . '/admin/admin.js');
chk('settings page not width-capped', preg_match('/\.mlsc-wrap \{[^}]*max-width:\s*none/', $adcss) ? 'yes' : 'no', 'yes');
chk('iframe fills the preview panel', preg_match('/\.mlsc-preview-stage iframe \{[^}]*width:\s*100%/', $adcss) ? 'yes' : 'no', 'yes');
chk('desktop canvas = panel width', strpos($adjs, "var w = 'mobile' === device ? MOBILE_W : stageW;") !== false ? 'yes' : 'no', 'yes');
chk('mobile canvas is 390px', strpos($adjs, 'var MOBILE_W = 390') !== false ? 'yes' : 'no', 'yes');
chk('canvas is never scaled', strpos($adjs, "transform: 'scale(") === false ? 'yes' : 'no', 'yes');
chk('full-height canvas', strpos($adjs, 'var CANVAS_H = 760') !== false ? 'yes' : 'no', 'yes');
chk('refit on resize', strpos($adjs, "on( 'resize'") !== false ? 'yes' : 'no', 'yes');
chk('desktop canvas above the 768 breakpoint', 900 > 768 ? 'yes' : 'no', 'yes');

echo "\n=== 21. FOOTER HOOK SAFETY ===\n";
$rf = new ReflectionFunction('mlsc_render_frontend');
chk('render_frontend takes no params', $rf->getNumberOfParameters(), 0);
chk('render_widget exists', function_exists('mlsc_render_widget') ? 'yes' : 'no', 'yes');
$GLOBALS['__options']['mlsc_options'] = array('items'=>$items,'position'=>'right','hover_label'=>'none');
ob_start(); mlsc_render_frontend( 'a-theme-passed-this' ); $safe = ob_get_clean();
chk('stray hook argument still renders', strlen($safe) > 100 ? 'yes' : 'no', 'yes');

echo "\n=== 22. STATUS PILL ===\n";
$GLOBALS['__options']['mlsc_options'] = array('items'=>$items,'position'=>'right','hover_label'=>'none');
ob_start(); mlsc_settings_page(); $pg2 = ob_get_clean();
chk('pill present', strpos($pg2, 'mlsc-live-status') !== false ? 'yes' : 'no', 'yes');
chk('pill shows live with counts', preg_match('/mlsc-live-status is-on">[^<]*' . count($items) . ' (channel|channels)/', $pg2) ? 'yes' : 'no', 'yes');
$GLOBALS['__options']['mlsc_options'] = array('items'=>array(),'position'=>'right','hover_label'=>'none');
ob_start(); mlsc_settings_page(); $pg3 = ob_get_clean();
chk('empty state says not shown', strpos($pg3, 'Not shown: no channels selected') !== false ? 'yes' : 'no', 'yes');

echo "\n=== 23. PREVIEW IS 1:1 AND FULL HEIGHT ===\n";
$adcss = file_get_contents(dirname($argv[1]) . '/admin/admin.css');
$adjs  = file_get_contents(dirname($argv[1]) . '/admin/admin.js');
chk('preview column is 40% (3fr / 2fr)', preg_match('/grid-template-columns:\s*minmax\(520px, 3fr\) minmax\(560px, 2fr\)/', $adcss) ? 'yes' : 'no', 'yes');
chk('canvas renders at the panel width', preg_match('/\.mlsc-preview-stage iframe \{[^}]*width:\s*100%/', $adcss) ? 'yes' : 'no', 'yes');
chk('stage is full height', preg_match('/\.mlsc-preview-stage \{[^}]*height:\s*760px/', $adcss) ? 'yes' : 'no', 'yes');
chk('desktop canvas uses panel width', strpos($adjs, ": stageW;") !== false ? 'yes' : 'no', 'yes');
chk('canvas height constant', strpos($adjs, 'var CANVAS_H = 760') !== false ? 'yes' : 'no', 'yes');
chk('no scaling applied at all', strpos($adjs, "transform: 'none'") !== false && strpos($adjs, 'MIN_DESKTOP_W') === false ? 'yes' : 'no', 'yes');
chk('phone breakpoint neutralised in preview', strpos($adjs, 'max-width:0px') !== false ? 'yes' : 'no', 'yes');
chk('stylesheet fetched as text', strpos($adjs, "dataType: 'text'") !== false ? 'yes' : 'no', 'yes');
chk('repaint on device switch', strpos($adjs, "paint();") !== false ? 'yes' : 'no', 'yes');
chk('stacks to one column on narrow admin', strpos($adcss, 'max-width: 1300px') !== false ? 'yes' : 'no', 'yes');
chk('page skeleton in preview', strpos($adjs, 'mlsc-demo') !== false ? 'yes' : 'no', 'yes');

echo "\n=== 24. MOBILE BAR DOES NOT COVER THE PAGE END ===\n";
$fjs = file_get_contents(dirname($argv[1]) . '/assets/js/frontend.js');
chk('reserve helper exists', strpos($fjs, 'function reserveBottomSpace()') !== false ? 'yes' : 'no', 'yes');
chk('called on boot', preg_match('/function boot\(\)[^}]*reserveBottomSpace\(\);/s', $fjs) ? 'yes' : 'no', 'yes');
chk('re-runs on resize', strpos($fjs, "addEventListener('resize', reserveBottomSpace)") !== false ? 'yes' : 'no', 'yes');
chk('measures the real bar height', strpos($fjs, "bar.getBoundingClientRect().height") !== false ? 'yes' : 'no', 'yes');
chk('skips when the bar is display:none', strpos($fjs, "'none' !== window.getComputedStyle(bar).display") !== false ? 'yes' : 'no', 'yes');
chk('restores the site own padding on desktop', strpos($fjs, 'bodyPaddingBefore') !== false ? 'yes' : 'no', 'yes');
chk('adds clearance below the bar', strpos($fjs, 'Math.round(height) + 8') !== false ? 'yes' : 'no', 'yes');

echo "\n=== 25. CUSTOM ICON CENTRING ===\n";
$ci = mlsc_get_defaults();
$ci['items'] = array( array(
    'channel' => 'custom', 'value' => 'https://example.com/x', 'name' => 'Custom',
    'color' => '#333333', 'custom_icon' => 'https://example.com/i.png',
    'show_desktop' => 1, 'show_mobile' => 1,
) );
$ci['hover_label'] = 'none';
ob_start(); mlsc_render_widget( $ci ); $ci_html = ob_get_clean();
chk('uploaded icon uses the SVG wrapper', strpos( $ci_html, 'mlsc-svg-icon mlsc-custom-icon' ) !== false ? 'yes' : 'no', 'yes');
chk('no bare <img> straight inside .item-inner', preg_match( '/<[a-z]+ class="item-inner[^>]*>\s*<img/', $ci_html ) ? 'yes' : 'no', 'no');
chk('uploaded icon is rendered', strpos( $ci_html, 'example.com/i.png' ) !== false ? 'yes' : 'no', 'yes');
$fcs = file_get_contents( dirname( $argv[1] ) . '/assets/css/frontend.css' );
chk('uploaded icon size follows the preset', preg_match( '/\.mlsc \.item-inner img \{[^}]*width:\s*var\(--mlsc-icon\)/', $fcs ) ? 'yes' : 'no', 'yes');
chk('uploaded icon has no side margins', preg_match( '/\.mlsc \.item-inner img \{[^}]*margin:\s*0;/', $fcs ) ? 'yes' : 'no', 'yes');
chk('uploaded icon height follows the preset', preg_match( '/\.mlsc \.item-inner img \{[^}]*height:\s*var\(--mlsc-icon\)/', $fcs ) ? 'yes' : 'no', 'yes');
chk('hardcoded 30px icon box gone', strpos( $fcs, "width: 30px;\n    height: 30px;" ) === false ? 'yes' : 'no', 'yes');

echo "\n=== 26. MULTIPLE CUSTOM LINKS ===\n";
$chans = mlsc_get_channels();
chk('custom channel is repeatable', ! empty( $chans['custom']['repeatable'] ) ? 'yes' : 'no', 'yes');
chk('whatsapp is not repeatable', empty( $chans['whatsapp']['repeatable'] ) ? 'yes' : 'no', 'yes');

$dup = mlsc_get_defaults();
$dup['items'] = array(
    array( 'channel' => 'custom', 'value' => 'https://a.example.com', 'name' => 'A', 'color' => '#111111', 'show_desktop' => 1, 'show_mobile' => 1 ),
    array( 'channel' => 'custom', 'value' => 'https://b.example.com', 'name' => 'B', 'color' => '#222222', 'show_desktop' => 1, 'show_mobile' => 1 ),
    array( 'channel' => 'custom', 'value' => 'https://c.example.com', 'name' => 'C', 'color' => '#333333', 'show_desktop' => 1, 'show_mobile' => 1 ),
);
$dup_saved = mlsc_sanitize_options( $dup );
chk('sanitizer keeps every custom item', count( $dup_saved['items'] ), 3 );
ob_start(); mlsc_render_widget( $dup_saved ); $dup_html = ob_get_clean();
chk('all three links rendered', substr_count( $dup_html, 'href="https://' ) >= 3 ? 'yes' : 'no', 'yes');
foreach ( array( 'a', 'b', 'c' ) as $letter ) {
    chk( 'link ' . $letter . ' present', strpos( $dup_html, 'https://' . $letter . '.example.com' ) !== false ? 'yes' : 'no', 'yes' );
}

$adjs  = file_get_contents( dirname( $argv[1] ) . '/admin/admin.js' );
chk('tile click honours data-repeatable', strpos( $adjs, 'repeatable' ) !== false && strpos( $adjs, "=== \$tile.attr( 'data-repeatable' )" ) !== false ? 'yes' : 'no', 'yes');
chk('non-repeatable still jumps to the card', strpos( $adjs, 'if ( ! repeatable && $existing.length )' ) !== false ? 'yes' : 'no', 'yes');
chk('badge counts repeatable cards', strpos( $adjs, "\$t.find( '.mlsc-tile-times' )" ) !== false ? 'yes' : 'no', 'yes');
chk('new cards auto-numbered', strpos( $adjs, "base + ' ' + total" ) !== false ? 'yes' : 'no', 'yes');

ob_start(); mlsc_settings_page(); $pg4 = ob_get_clean();
chk('custom tile marked repeatable in markup', preg_match( '/data-channel="custom"[^>]*data-repeatable="1"/s', $pg4 ) ? 'yes' : 'no', 'yes');
chk('whatsapp tile not marked repeatable', preg_match( '/data-channel="whatsapp"[^>]*data-repeatable/s', $pg4 ) ? 'yes' : 'no', 'no');

echo "\n=== 27. REPEAT AFFORDANCES ===\n";
ob_start(); mlsc_settings_page(); $pg5 = ob_get_clean();
chk('tile has a permanent + chip', strpos( $pg5, 'mlsc-tile-plus' ) !== false ? 'yes' : 'no', 'yes');
chk('label carries a multiplier slot', strpos( $pg5, 'mlsc-tile-times' ) !== false ? 'yes' : 'no', 'yes');
chk('explanation hint under the grid', strpos( $pg5, 'mlsc-repeat-hint' ) !== false ? 'yes' : 'no', 'yes');
chk('card footer offers Add another', strpos( $pg5, 'mlsc-add-another' ) !== false ? 'yes' : 'no', 'yes');
chk('only one + chip (repeatables only)', substr_count( $pg5, 'class="mlsc-tile-plus"' ), 1 );
chk('non-repeatable tiles keep the tick', strpos( $pg5, 'mlsc-tile-check' ) !== false ? 'yes' : 'no', 'yes');

$adjs2 = file_get_contents( dirname( $argv[1] ) . '/admin/admin.js' );
chk('shared add helper', strpos( $adjs2, 'function addChannelCard' ) !== false ? 'yes' : 'no', 'yes');
chk('card button reuses it', strpos( $adjs2, "'.mlsc-add-another'" ) !== false ? 'yes' : 'no', 'yes');
chk('multiplier only past one', strpos( $adjs2, "n > 1 ? '×' + n : ''" ) !== false ? 'yes' : 'no', 'yes');
$adcss2 = file_get_contents( dirname( $argv[1] ) . '/admin/admin.css' );
chk('plus chip styled', strpos( $adcss2, '.mlsc-tile-plus {' ) !== false ? 'yes' : 'no', 'yes');
chk('multiplier styled', strpos( $adcss2, '.mlsc-tile-times {' ) !== false ? 'yes' : 'no', 'yes');
chk('add-another styled', strpos( $adcss2, '.mlsc-add-another {' ) !== false ? 'yes' : 'no', 'yes');

echo "\n=== 28. DEAD \"Default Background Color\" SETTING IS GONE ===\n";
// It was stored and sanitized but never read by any renderer: every button takes
// its colour from its own Icon Color field or from the channel brand colour.
// Removed in 1.4.36 — these guards keep it from creeping back.
$ddef = mlsc_get_defaults();
chk('defaults have no bgcolor key', array_key_exists( 'bgcolor', $ddef ) ? 'PRESENT' : 'none', 'none');
chk('sanitizer emits no bgcolor key',
    array_key_exists( 'bgcolor', mlsc_sanitize_options( array( 'bgcolor' => '#ff0000' ) ) ) ? 'PRESENT' : 'none', 'none');
chk('settings page has no bgcolor input', strpos( $pg, 'mlsc_options[bgcolor]' ) !== false ? 'PRESENT' : 'none', 'none');
chk('settings page no longer labels it', strpos( $pg, 'Default Background Color' ) !== false ? 'PRESENT' : 'none', 'none');
// The contact-title colour is a different, working field — it must stay.
chk('contact_bgcolor still in defaults', array_key_exists( 'contact_bgcolor', $ddef ) ? 'yes' : 'no', 'yes');
chk('contact_bgcolor still editable', strpos( $pg, 'mlsc_options[contact_bgcolor]' ) !== false ? 'yes' : 'no', 'yes');
chk('contact-title colour reaches the CSS',
    ( false !== strpos( mlsc_build_inline_css( array( 'contact_bgcolor' => '#123456' ) ), '#123456' ) ) ? 'yes' : 'no', 'yes');

echo "\nFAILURES: $fail\n";
