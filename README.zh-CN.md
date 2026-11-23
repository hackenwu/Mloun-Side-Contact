[English](README.md) | **简体中文**

# Mloun Side Contact（美络云侧边客服）

WordPress 悬浮联系条插件 —— 支持 WhatsApp、微信、Telegram、电话、邮件等 20+ 渠道，可停靠在页面侧边或底部。

本仓库是**免费版源码**（同时发布在 [WordPress.org](https://wordpress.org/plugins/mloun-side-contact-floating-contact-buttons-for-online-chat-wechat-phone/)）。商业版 **Mloun Side Contact PRO** 增加了营业时间、按页面显示规则、询盘消息预填、留言表单与点击统计，详见 [mloun.com](https://mloun.com/product/mloun-side-contact-pro/)。

## 功能

- **24 个渠道** —— WhatsApp、微信、Telegram、Messenger、Zalo、Viber、LINE、KakaoTalk、邮件、电话、短信、自定义链接、回到顶部
- **两种布局** —— 侧边竖排（可折叠展开）或底部通栏；桌面端与手机端可分别设置
- **图片带 ALT** —— 二维码与自定义图标都会输出真实 ALT 文字（取媒体库设置，或在频道里单独覆盖），读屏软件与图片搜索可正常识别
- **二维码面板** —— 用于无法通过链接传参的渠道（微信，以及任何扫码渠道）
- **全套样式** —— 图标或文字按钮、三档尺寸、品牌色、单渠道配色、文字展开方向
- **可翻译** —— 所有文案都是真实文本节点，TranslatePress、WPML、Polylang 都能抓取；内置简体中文语言包
- **不依赖 jQuery、无需构建** —— 原生 JS + CSS，只在前台加载

## 安装

### 从 WordPress.org 安装

1. 后台进入 **插件 → 安装插件**
2. 搜索 *Mloun Side Contact*
3. 安装并启用，然后打开后台菜单中的 **Side Contact**

### 从本仓库安装

```bash
git clone https://github.com/hackenwu/Mloun-Side-Contact.git
cp -r Mloun-Side-Contact /path/to/wp-content/plugins/mloun-side-contact
```

或打包后通过 **插件 → 安装插件 → 上传插件** 上传：

```bash
zip -r mloun-side-contact.zip . -x "tests/*" -x "*.py" -x ".git/*"
```

### 从 Releases 下载

在 [Releases](https://github.com/hackenwu/Mloun-Side-Contact/releases) 页面下载 `mloun-side-contact-vX.Y.Z.zip`，直接上传安装即可（压缩包内是符合 WordPress 规范的顶层插件目录）。

## 环境要求

| | |
|---|---|
| WordPress | 5.0 或更高 |
| PHP | 7.4 或更高 |
| WooCommerce | 可选 —— 仅用于增加商品/购物车/结账页类型 |

## 开发

插件是纯 PHP + CSS + JavaScript，没有编译器，也没有 `node_modules`。`mloun-side-contact.php` 是唯一入口，包含渠道定义、设置页与前台渲染逻辑。

```
mloun-side-contact.php    插件入口、设置、渠道注册、渲染
admin/admin.css|js        后台设置页资源
assets/css/frontend.css   挂件本身
assets/js/frontend.js     折叠展开、二维码面板、点击统计钩子
languages/                .pot、zh_CN .po/.mo
tests/                    PHP 回归测试（按修复批次分组）
build-i18n.py             从源码重建 .pot/.po/.mo
build-zip.py              生成用于分发/发布的 zip
run-tests.py              跑全部测试 + php -l + 版本一致性检查
```

### 运行测试

```bash
python run-tests.py          # 全部套件、语法检查与版本一致性
python run-tests.py --quiet  # 只打印汇总
```

`tests/` 下每个文件都是独立的 PHP 脚本，以插件主文件作为 `argv[1]`；运行器按 `$MLSC_PHP`、再按常见安装路径查找 PHP。

### 重建语言包

```bash
python build-i18n.py         # 重新生成 .pot、zh_CN.po 与 zh_CN.mo
```

### 生成发布包

```bash
python build-zip.py          # 输出 mloun-side-contact-v<version>.zip（自动排除 .git、tests、*.py）
```

## 渲染实现说明

- 渠道链接按类型生成（`wa.me`、`mailto:`、`tel:`、`weixin://`、自定义），每个渠道元素都带 `data-mlsc-channel`，脚本与统计无需从 URL 猜测渠道类型。
- 文案以文本节点（`.mlsc-tip`）输出，而不是 CSS `content: attr()`，这样页面翻译插件才能识别。
- 样式统一命名空间在 `.mlsc` 下，并大量使用 CSS 变量（`--mlsc-blue`、`--mlsc-orange`、`--mlsc-size` 等），主题无需改动插件即可换肤。

## 许可证

GPLv2 或更高版本 —— 见 [LICENSE](LICENSE)。
