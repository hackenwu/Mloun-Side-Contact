#!/usr/bin/env python3
"""Build the distributable plugin zip for Mloun Side Contact.

The top-level folder inside the zip MUST be the WordPress.org slug, not a short
name. Plugin Check derives the expected text domain from the folder the plugin is
installed in, so a zip that unpacks to `mloun-side-contact/` makes it report:

    The "Text Domain" header in the plugin file does not match the slug.
    Found "...wechat-phone", expected "mloun-side-contact"

The header itself is correct — the folder name is what is wrong. Building through
this script keeps the two in sync.

Usage:  python build-zip.py
"""

import os
import re
import sys
import zipfile
from pathlib import Path

# The real WordPress.org slug (https://plugins.svn.wordpress.org/<SLUG>).
SLUG = "mloun-side-contact-floating-contact-buttons-for-online-chat-wechat-phone"

# Files that must never ship to users.
EXCLUDE_SUFFIX = (".py", ".pyc", ".log", ".bak")
EXCLUDE_NAMES = {".DS_Store", "Thumbs.db"}


def plugin_version(php_file: Path) -> str:
    head = php_file.read_text(encoding="utf-8", errors="replace")[:4000]
    return re.search(r"^\s*\*?\s*Version:\s*([\d.]+)", head, re.M).group(1)


def check_readme(readme: Path) -> list[str]:
    """Mirror the two readme rules the plugin directory enforces."""
    problems = []
    text = readme.read_text(encoding="utf-8", errors="replace")
    match = re.search(r"^Tags:(.*)$", text, re.M)
    if not match:
        problems.append("readme.txt has no Tags: line")
        return problems

    raw = match.group(1)
    if raw.rstrip().endswith(","):
        problems.append("Tags: line ends with a comma, which registers an empty 6th tag")

    tags = [t.strip() for t in raw.split(",") if t.strip()]
    if len(tags) > 5:
        problems.append(f"{len(tags)} tags found, WordPress.org accepts 5: {tags[5:]}")
    if any(t != t.lower() for t in tags):
        problems.append("tags should be lower case")
    return problems


def main() -> int:
    here = Path(__file__).resolve().parent
    php = here / "mloun-side-contact.php"
    if not php.exists():
        print(f"not found: {php}")
        return 1

    version = plugin_version(php)
    out = here.parent / f"mloun-side-contact-v{version}.zip"
    if out.exists():
        out.unlink()

    written = 0
    with zipfile.ZipFile(out, "w", zipfile.ZIP_DEFLATED) as archive:
        for path in sorted(here.rglob("*")):
            if not path.is_file():
                continue
            if ".svn" in path.parts or "__pycache__" in path.parts:
                continue
            if path.name in EXCLUDE_NAMES or path.name.endswith(EXCLUDE_SUFFIX):
                continue
            archive.write(path, f"{SLUG}/{path.relative_to(here).as_posix()}")
            written += 1

    problems = check_readme(here / "readme.txt")

    with zipfile.ZipFile(out) as archive:
        names = archive.namelist()
        tops = {n.split("/")[0] for n in names}
        corrupt = archive.testzip()
        header = archive.read(f"{SLUG}/mloun-side-contact.php").decode("utf-8", "replace")
        text_domain = re.search(r"^\s*\*?\s*Text Domain:\s*(\S+)", header, re.M).group(1)

    if tops != {SLUG}:
        problems.append(f"zip top-level folder is {tops}, must be exactly {{{SLUG!r}}}")
    if text_domain != SLUG:
        problems.append(f"Text Domain header is {text_domain!r}, must equal the slug")
    if corrupt:
        problems.append(f"corrupt entry: {corrupt}")

    print(f"version   : {version}")
    print(f"zip       : {out.name}  ({out.stat().st_size / 1024:.1f} KB, {written} files)")
    print(f"folder    : {sorted(tops)[0]}")
    print(f"textdomain: {text_domain}")
    print(f"corrupt   : {corrupt}")

    if problems:
        print("\nPROBLEMS:")
        for p in problems:
            print("  -", p)
        return 1

    print("\nOK: slug, folder and text domain agree; readme tags valid.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
