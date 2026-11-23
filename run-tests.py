#!/usr/bin/env python3
"""Mloun Side Contact —— 回归测试 + 静态检查 + 版本一致性，一条命令跑完。

用法：
    python run-tests.py            # 全部套件 + lint + 版本一致性
    python run-tests.py --quiet    # 只打印汇总

测试套件在 ./tests/ 里，每个都是独立 PHP 脚本，接受插件主文件路径作为 argv[1]。
PHP 不在 PATH 里，默认用 C:/Users/DELL/AppData/Local/Temp/php81/php.exe，
可用环境变量 MLSC_PHP 覆盖。
"""

import os
import re
import shutil
import subprocess
import sys
from pathlib import Path

HERE = Path(__file__).resolve().parent
SRC = HERE / "mloun-side-contact" / "mloun-side-contact.php"
PLUGIN_DIR = SRC.parent
TESTS = HERE / "tests"

# 回归顺序 = 开发时间顺序，先跑的挂了通常说明底层被改坏
SUITES = [
    "mlsc_v13.php",
    "fix_test.php",
    "teams_test.php",
    "totop_test.php",
    "totop2.php",
    "style_test.php",
    "fix3_test.php",
    "fix4_test.php",
    "fix5_test.php",
    "fix6_test.php",
    "fix7_test.php",
    "fix8_test.php",
    "fix9_test.php",
    "fix10_test.php",
    "mobile_layout_test.php",
    "alt_test.php",
]

PHP_CANDIDATES = [
    os.environ.get("MLSC_PHP", ""),
    r"C:/Users/DELL/AppData/Local/Temp/php81/php.exe",
    r"C:/php/php.exe",
    r"C:/xampp/php/php.exe",
]


def find_php() -> str | None:
    for c in PHP_CANDIDATES:
        if c and Path(c).exists():
            return c
    return shutil.which("php")


def run(cmd, **kw):
    return subprocess.run(cmd, capture_output=True, text=True, encoding="utf-8",
                          errors="replace", **kw)


def grep_version(pattern: str, path: Path) -> str:
    m = re.search(pattern, path.read_text(encoding="utf-8", errors="replace"))
    return m.group(1) if m else "?"


def main() -> int:
    quiet = "--quiet" in sys.argv
    php = find_php()
    if not SRC.exists():
        print(f"主文件不存在: {SRC}")
        return 1
    if not php:
        print("找不到 php.exe —— 设置环境变量 MLSC_PHP 指向 PHP 8.1+ 可执行文件")
        return 1

    total_pass = total_fail = total_fatal = 0
    rows = []
    for name in SUITES:
        path = TESTS / name
        if not path.exists():
            rows.append((name, 0, 0, 0, "MISSING"))
            total_fatal += 1
            continue
        r = run([php, str(path), str(SRC)])
        out = r.stdout + r.stderr
        p, f = out.count("[PASS]"), out.count("[FAIL]")
        fatal = 1 if ("Fatal error" in out or "Parse error" in out) else 0
        total_pass += p
        total_fail += f
        total_fatal += fatal
        bad = " ".join(l.strip() for l in out.split("\n") if "[FAIL]" in l)[:120]
        rows.append((name, p, f, fatal, bad))
        if not quiet:
            flag = "FATAL" if fatal else ("FAIL" if f else "ok")
            print(f"  {name:22s} {p:4d} PASS  {f} FAIL  [{flag}] {bad}")

    print(f"\n  {'TOTAL':22s} {total_pass:4d} PASS  {total_fail} FAIL  {total_fatal} fatal")

    # ---- lint ----
    problems = []
    r = run([php, "-l", str(SRC)])
    print("\nLINT")
    print("  PHP        :", "OK" if r.returncode == 0 else r.stdout.strip()[:200])
    if r.returncode:
        problems.append("php -l")

    node = shutil.which("node")
    for rel in ["admin/admin.js", "assets/js/frontend.js"]:
        f = PLUGIN_DIR / rel
        if not node:
            print(f"  JS {rel:26s}: skipped (no node)")
            continue
        r = run([node, "--check", str(f)])
        print(f"  JS {rel:26s}:", "OK" if r.returncode == 0 else r.stderr[:200])
        if r.returncode:
            problems.append(rel)

    for rel in ["assets/css/frontend.css", "admin/admin.css"]:
        f = PLUGIN_DIR / rel
        c = f.read_text(encoding="utf-8", errors="replace")
        delta = c.count("{") - c.count("}")
        print(f"  CSS {rel:25s}: braces={delta}")
        if delta:
            problems.append(f"{rel} braces={delta}")

    # ---- 版本五处一致 ----
    main_php = PLUGIN_DIR / "mloun-side-contact.php"
    versions = {
        "plugin header": grep_version(r"Version:\s+([\d.]+)", main_php),
        "MLSC_VERSION": grep_version(r"MLSC_VERSION',\s*'([\d.]+)'", main_php),
        "readme stable": grep_version(r"Stable tag:\s+([\d.]+)", PLUGIN_DIR / "readme.txt"),
        "frontend.js": grep_version(r"@version ([\d.]+)", PLUGIN_DIR / "assets/js/frontend.js"),
        "admin.js": grep_version(r"@version ([\d.]+)", PLUGIN_DIR / "admin/admin.js"),
    }
    print("\nVERSION")
    for k, v in versions.items():
        print(f"  {k:16s}: {v}")
    if len(set(versions.values())) != 1:
        problems.append("版本号不一致")
        print("  -> INCONSISTENT")
    else:
        print("  -> CONSISTENT")

    if problems:
        print("\n问题:", ", ".join(problems))
    ok = (total_fail == 0 and total_fatal == 0 and not problems)
    print("\n结果:", "ALL GREEN" if ok else "有失败项，禁止发版")
    return 0 if ok else 1


if __name__ == "__main__":
    sys.exit(main())
