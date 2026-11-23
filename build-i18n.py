#!/usr/bin/env python3
"""Rebuild .pot / -zh_CN.po / -zh_CN.mo for Mloun Side Contact.

Scans the plugin PHP for every gettext call using $d as the text domain, keeps
existing translations, and adds any that are missing. Run it after adding or
changing a translatable string:

    python build-i18n.py
"""

import re
import struct
from pathlib import Path

SLUG = "mloun-side-contact-floating-contact-buttons-for-online-chat-wechat-phone"
BASE = Path(__file__).resolve().parent
LANG = BASE / "languages"
PHP = BASE / "mloun-side-contact.php"
VERSION = re.search(r"^ \* Version:\s+([\d.]+)", PHP.read_text(encoding="utf-8"), re.M).group(1)

# Translations added per release. Extend this dict; never delete keys, or an
# existing translation silently regresses to English.
ZH = {
    "Settings": "设置",
    "Settings saved.": "设置已保存。",
    "Choose image": "选择图片",
    "Use this image": "使用这张图片",
    "Remove image": "移除图片",
    "Preview could not be updated.": "预览更新失败。",
    "Add a channel to see the preview.": "添加一个频道后即可预览。",
    "Not allowed.": "没有权限。",
    "Preview payload too large.": "预览数据过大。",
    "Preview returned no markup. Open the browser console for details.": "预览没有返回内容，请打开浏览器控制台查看原因。",
    'Hidden on phones: the "Hide on mobile" option is on.': '手机端已隐藏：后台开启了"手机端隐藏"。',
    "QR Code Image": "二维码图片",
    "Image ALT Text": "图片 ALT 文字",
    "Applies to the QR code and custom icon of this channel. Leave blank to use the ALT text set on the image in the media library; if that is empty too, the channel name is used.":
        "适用于该频道的二维码与自定义图标。留空则使用媒体库中图片已设置的 ALT 文字；若那里也为空，则使用频道名称。",
    "Custom Icon": "自定义图标",
    "Optional. Replaces the channel icon with your own image.": "可选。用你自己的图片替换频道图标。",
    "Upload / Select image": "上传 / 选择图片",
    "The QR code is shown at exactly this size. Its white panel adds 10px (5px on each side) around it.":
        "二维码按此尺寸精确显示。白色底板在四周各加 5px（共 10px）。",
    "Live preview": "实时预览",
    "Widget preview": "组件预览",
    "Preview device": "预览设备",
    "Reflects your unsaved settings. Save to publish.": "显示的是未保存的设置，保存后才会正式生效。",
    "Desktop": "桌面端",
    "Mobile": "手机端",
    "Not shown: no channels selected": "未显示：还没有选择任何频道",
    "Live on your site · %d channel": "已在前台生效 · %d 个频道",
    "Live on your site · %d channels": "已在前台生效 · %d 个频道",
    "Paste any URL. Click the tile again to add more than one custom link.": "粘贴任意网址。再次点击该图块可以添加多个自定义链接。",
    "click again to add another": "再次点击可再添加一个",
    "Tiles marked with + can be added more than once — click again for another button. Custom Link works this way.": "带 + 标记的图块可以重复添加 —— 再次点击就会多出一个按钮，Custom Link 即为此类。",
    "Add another %s": "再添加一个%s",
    "Side Contact": "侧边联系",
    "Small": "小",
    "Medium": "中",
    "Large": "大",
    "Button Size": "按钮尺寸",
    "Preset button and icon sizes. The icon always stays proportional to the button.":
        "预设按钮与图标尺寸，图标始终与按钮保持相同比例。",
    "Hidden until the visitor scrolls past 20% of the page, then it fades in; clicking it scrolls smoothly to the top and it hides again.":
        "访客下滑超过页面 20% 后淡入显示；点击后平滑回到顶部，并自动隐藏。",
    "Always sits at the bottom of the bar. Hidden until the visitor scrolls past 20% of the page, then it fades in; clicking it scrolls smoothly to the top and it hides again.":
        "始终位于悬浮栏最下方。访客下滑超过页面 20% 后淡入显示；点击后平滑回到顶部，并自动隐藏。",
    "The dashed tile (Back to Top) is a page button, not a chat channel — it appears in the bottom corner once visitors scroll past 20% of the page.":
        "虚线磁贴（返回顶部）是页面按钮而非聊天频道——访客下滑超过页面 20% 后会出现在悬浮栏底部。",
    "Chat with dots": "气泡加三点",
    "Round chat with dots": "圆形气泡加三点",
    "Typing": "正在输入",
    "Smiley chat": "笑脸气泡",
    "Quote chat": "引用气泡",
    "Chat bot": "机器人客服",
    "Translate chat": "翻译气泡",
    "Group chat": "群组气泡",
    "Call chat": "通话气泡",
    "Bubble Background": "气泡背景色",
    "Bubble Text Color": "气泡文字颜色",
    "Always show the bubble": "气泡始终显示",
    "Off: the bubble appears on hover.": "关闭时：气泡在悬停时显示。",
    "0 = automatic, 48% of the button size (50px button = 24px icon, 60px = 29px). Enter 14-40 to override.":
        "0 = 自动，取按钮尺寸的 48%（50px 按钮 = 24px 图标，60px = 29px）。填写 14-40 可手动覆盖。",
    "Toggle Icon": "切换按钮图标",
    "Arrow (flips)": "箭头（自动翻转）",
    "Chat bubble": "聊天气泡",
    "Close": "关闭",
    "Plus": "加号",
    "Menu bars": "三横线",
    "Three dots": "三个点",
    "Sliders": "调节滑块",
    "Bottom Distance (px)": "距底部距离 (px)",
    "The label is shown as a bubble beside the button on hover, so the button itself stays a clean circle. The arrow icon flips automatically; every other icon stays the same in both states.":
        "文字以气泡形式在按钮旁悬停显示，按钮本身保持干净的圆形。箭头图标会自动翻转，其它图标在两个状态下保持不变。",
    "Toggle Style": "切换按钮风格",
    "Arrow + text (pill)": "箭头 + 文字（胶囊）",
    "Arrow only (round)": "仅箭头（圆形）",
    "Chat bubble / close (round)": "气泡 / 关闭（圆形）",
    "Text only (pill)": "仅文字（胶囊）",
    "Text While Folded": "折叠时文字",
    "Text While Expanded": "展开时文字",
    "Contact Us": "联系我们",
    "With the arrow styles the chevron points up while folded, because the list opens upward. The round styles only show their text on hover, following the Hover Label setting above.":
        "使用箭头风格时，折叠状态下箭头朝上，因为列表是向上展开的。圆形风格只在悬停时显示文字，遵循上方的 Hover Label 设置。",
    "Fold / Unfold": "折叠 / 展开",
    "Mode": "模式",
    "Always expanded (no toggle)": "始终展开（无切换按钮）",
    "Start folded": "初始折叠",
    "Start expanded (toggle available)": "初始展开（提供切换按钮）",
    "Toggle Color": "切换按钮颜色",
    "Toggle Hover Color": "切换按钮悬停颜色",
    "Show contacts": "显示联系方式",
    "Hide": "收起",
    "Show a single button at first. Visitors click it to reveal every channel, and a close button folds them away again. The toggle uses the same size, shape and shadow as your other buttons.":
        "初始只显示一个按钮。访客点击后展开所有频道，再点关闭按钮收起。切换按钮与其他按钮使用相同的尺寸、形状和阴影。",
    "Profile slug after /in/. For a company page, paste the full URL instead.":
        "填写 /in/ 之后的个人主页别名。如果是公司主页，请直接粘贴完整网址。",
    "Channel handle after @. For a legacy /c/ or /channel/ URL, paste the full URL instead.":
        "填写 @ 之后的频道名称。如果是旧版 /c/ 或 /channel/ 网址，请直接粘贴完整网址。",
    "The email you sign in to Microsoft Teams with. Visitors open a chat with you.":
        "填写你登录 Microsoft Teams 的邮箱，访客将通过它与你发起聊天。",
    "WeChat cannot be opened by a link. Upload your WeChat QR code below and visitors scan it.":
        "微信无法通过链接直接打开。请在下方上传你的微信二维码，访客扫码添加。",
    "Always sits at the bottom of the bar. Hidden until the visitor scrolls past half the page, then it fades in; clicking it scrolls smoothly to the top and it hides again.":
        "始终位于悬浮栏最下方。访客下滑超过半页后才淡入显示；点击后平滑回到顶部，并自动隐藏。",
    "Nothing to fill in here. This button only scrolls the page back to the top.":
        "此处无需填写。该按钮仅用于将页面滚动回顶部。",
    "Visitors open the chat with this message already typed. Leave blank to disable.":
        "访客打开聊天时已自动填入这句话。留空则关闭此功能。",
    "Overrides the auto-generated link. Accepts a URL, an anchor like #open-chat, or javascript:void(SomeWidget.toggle()).":
        "覆盖自动生成的链接。支持网址、锚点（如 #open-chat）或 javascript:void(SomeWidget.toggle()) 形式。",
    "Added to the button link, so you can target it from your own CSS or JS.":
        "会添加到按钮链接上，方便你用自定义 CSS 或 JS 定位它。",
    "Click a channel to add it. You only fill in your number or username — the plugin builds the correct link for you, so nothing can be mistyped.":
        "点击频道即可添加。你只需填写号码或用户名，插件会自动生成正确的链接，避免填错。",
    "The dashed tile (Back to Top) is a page button, not a chat channel — it appears in the bottom corner once visitors scroll past half the page.":
        "虚线磁贴（返回顶部）是页面按钮而非聊天频道——访客下滑超过半页后会出现在悬浮栏底部。",
    "These are alternatives, not additions: slide-out and tooltip both display the hover text, so only one can be active.":
        "这两者是替代关系而非叠加：滑出和气泡都会显示悬停文字，因此只能启用其中一个。",
    "Automatic — the bubble opens opposite the bar, so it never leaves the screen.":
        "自动——气泡会在悬浮栏的对侧展开，确保不会超出屏幕。",
    "Pick a preset, then fine-tune the size. All presets only change shape, border and shadow — your channel colours stay exactly as you set them.":
        "先选择预设，再微调尺寸。所有预设只改变形状、边框和阴影——频道颜色保持你设置的样子不变。",
}

# The text domain must be a string literal (WPCS NonSingularStringLiteralDomain),
# so the scanner matches the literal slug rather than a variable.
CALL = re.compile(
    r"""(?:esc_html__|esc_attr__|esc_html_e|esc_attr_e|__|_e)\(\s*'((?:[^'\\]|\\.)*)'\s*,\s*'"""
    + re.escape(SLUG)
    + r"""'\s*\)"""
)


def esc(text: str) -> str:
    return text.replace("\\", "\\\\").replace('"', '\\"')


def scan() -> list[str]:
    out: list[str] = []
    for m in CALL.finditer(PHP.read_text(encoding="utf-8")):
        s = m.group(1).replace("\\'", "'")
        if s not in out:
            out.append(s)
    return out


def load_existing() -> dict[str, str]:
    po = LANG / f"{SLUG}-zh_CN.po"
    if not po.exists():
        return {}
    body = po.read_text(encoding="utf-8")
    pairs: dict[str, str] = {}
    for block in body.split("\n\n"):
        if "msgid" not in block or "msgstr" not in block:
            continue
        mid = re.search(r'^msgid "((?:[^"\\]|\\.)*)"', block, re.M)
        mstr = re.search(r'^msgstr "((?:[^"\\]|\\.)*)"', block, re.M)
        if not mid or not mstr:
            continue
        key = mid.group(1).replace('\\"', '"').replace("\\\\", "\\")
        val = mstr.group(1).replace('\\"', '"').replace("\\\\", "\\")
        if key and val:
            pairs[key] = val
    return pairs


def write_mo(path: Path, messages: dict[str, str], metadata: str) -> None:
    """Write a GNU gettext .mo file.

    Layout: 28-byte header, original descriptor table, translation descriptor
    table, then the two string blobs. Every descriptor holds (length, absolute
    offset) into the corresponding blob.
    """
    items = sorted(messages.items())
    items = [("", metadata)] + items
    n = len(items)

    orig_table_off = 28
    trans_table_off = orig_table_off + 8 * n
    orig_data_off = trans_table_off + 8 * n

    orig_blob = b""
    orig_desc = b""
    for key, _ in items:
        kb = key.encode("utf-8")
        orig_desc += struct.pack("II", len(kb), orig_data_off + len(orig_blob))
        orig_blob += kb + b"\x00"

    trans_data_off = orig_data_off + len(orig_blob)
    trans_blob = b""
    trans_desc = b""
    for _, value in items:
        vb = value.encode("utf-8")
        trans_desc += struct.pack("II", len(vb), trans_data_off + len(trans_blob))
        trans_blob += vb + b"\x00"

    header = struct.pack(
        "IIIIIII",
        0x950412DE,          # magic
        0,                   # revision
        n,                   # number of strings
        orig_table_off,
        trans_table_off,
        0,                   # hash table size
        0,                   # hash table offset
    )
    path.write_bytes(header + orig_desc + trans_desc + orig_blob + trans_blob)


def main() -> None:
    strings = scan()
    LANG.mkdir(exist_ok=True)

    existing = load_existing()
    merged = dict(existing)

    missing = [s for s in strings if s not in merged]
    for s in missing:
        if s in ZH:
            merged[s] = ZH[s]

    still = [s for s in strings if s not in merged or not merged[s]]
    if still:
        print(f"WARNING: {len(still)} string(s) still untranslated:")
        for s in still:
            print("   -", s[:100])

    header_comment = (
        "# Copyright (C) 2026 Mloun\n"
        "# This file is distributed under the GPL-2.0-or-later license.\n"
        "msgid \"\"\n"
        "msgstr \"\"\n"
        f"\"Project-Id-Version: Mloun Side Contact {VERSION}\\n\"\n"
        "\"Report-Msgid-Bugs-To: https://wordpress.org/support/plugin/"
        f"{SLUG}/\\n\"\n"
        "\"POT-Creation-Date: 2026-10-25 00:00+0800\\n\"\n"
        "\"PO-Revision-Date: 2026-10-25 00:00+0800\\n\"\n"
        "\"Last-Translator: Mloun <sales@mloun.com>\\n\"\n"
        "\"Language-Team: Chinese (China)\\n\"\n"
        "\"Language: zh_CN\\n\"\n"
        "\"MIME-Version: 1.0\\n\"\n"
        "\"Content-Type: text/plain; charset=UTF-8\\n\"\n"
        "\"Content-Transfer-Encoding: 8bit\\n\"\n"
        "\"Plural-Forms: nplurals=1; plural=0;\\n\"\n"
        f"\"X-Domain: {SLUG}\\n\"\n"
    )

    # .pot
    pot = ['#', 'msgid ""', 'msgstr ""']
    pot += [
        line
        for line in header_comment.split("\n")[3:]
        if line.startswith('"')
    ]
    for s in strings:
        pot += ["", "#: mloun-side-contact.php", f'msgid "{esc(s)}"', 'msgstr ""']
    (LANG / f"{SLUG}.pot").write_text("\n".join(pot) + "\n", encoding="utf-8")

    # .po
    po_body = ["# Mloun Side Contact - Chinese (China) translation.", header_comment.rstrip()]
    for s in strings:
        tr = merged.get(s, "")
        po_body += ["", "#: mloun-side-contact.php", f'msgid "{esc(s)}"', f'msgstr "{esc(tr)}"']
    po_path = LANG / f"{SLUG}-zh_CN.po"
    po_path.write_text("\n".join(po_body) + "\n", encoding="utf-8")

    # .mo
    messages = {s: merged[s] for s in strings if merged.get(s)}
    metadata = (
        f"Project-Id-Version: Mloun Side Contact {VERSION}\n"
        "Content-Type: text/plain; charset=UTF-8\n"
        "Language: zh_CN\n"
        f"X-Domain: {SLUG}\n"
    )
    write_mo(LANG / f"{SLUG}-zh_CN.mo", messages, metadata)

    print(f"version       : {VERSION}")
    print(f"strings found : {len(strings)}")
    print(f"translated    : {len(messages)}")
    print(f"newly added   : {len([s for s in missing if s in ZH])}")
    print(f"untranslated  : {len(still)}")


if __name__ == "__main__":
    main()
