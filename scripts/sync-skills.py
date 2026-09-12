#!/usr/bin/env python3
"""Generate plugins/ and both plugin manifests from .agents/skills/*/SKILL.md.

Source of truth: .agents/skills/<name>/SKILL.md (frontmatter: name, description).
Generated:
  plugins/<name>/.claude-plugin/plugin.json
  plugins/<name>/.devin-plugin/plugin.json
  plugins/<name>/skills/<name>/SKILL.md  (symlink to the source)
  .claude-plugin/marketplace.json
  .devin-plugin/plugin.json
"""
import json
import os
import re
import shutil
import sys
from pathlib import Path

import yaml

ROOT = Path(__file__).resolve().parent.parent
SKILLS = ROOT / ".agents" / "skills"
PLUGINS = ROOT / "plugins"
REPO_URL = "https://github.com/hirosashi/web_export.git"
OWNER = "hirosashi"
VERSION = "1.0.0"


def frontmatter(path: Path) -> dict:
    text = path.read_text(encoding="utf-8")
    m = re.match(r"^---\n(.*?)\n---\n", text, re.S)
    if not m:
        sys.exit(f"{path}: missing YAML frontmatter")
    meta = yaml.safe_load(m.group(1)) or {}
    for key in ("name", "description"):
        if not isinstance(meta.get(key), str) or not meta[key].strip():
            sys.exit(f"{path}: frontmatter '{key}' must be a non-empty string")
    if meta["name"] != path.parent.name:
        sys.exit(f"{path}: frontmatter name '{meta['name']}' must match directory '{path.parent.name}'")
    meta["description"] = " ".join(meta["description"].split())
    return meta


def write_json(path: Path, data: dict) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(json.dumps(data, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")


def main() -> None:
    skills = sorted(p.parent for p in SKILLS.glob("*/SKILL.md"))
    names = {p.name for p in skills}

    if PLUGINS.exists():
        for stale in PLUGINS.iterdir():
            if stale.is_dir() and stale.name not in names:
                shutil.rmtree(stale)

    marketplace_plugins = []
    required = []
    for skill_dir in skills:
        name = skill_dir.name
        meta = frontmatter(skill_dir / "SKILL.md")
        desc = meta.get("description", "")
        plugin_dir = PLUGINS / name

        write_json(plugin_dir / ".claude-plugin" / "plugin.json",
                   {"name": name, "description": desc, "version": VERSION, "author": {"name": OWNER}})
        write_json(plugin_dir / ".devin-plugin" / "plugin.json",
                   {"name": name, "version": VERSION, "description": desc})

        link = plugin_dir / "skills" / name / "SKILL.md"
        link.parent.mkdir(parents=True, exist_ok=True)
        target = os.path.relpath(skill_dir / "SKILL.md", link.parent)
        if link.is_symlink() or link.exists():
            link.unlink()
        os.symlink(target, link)

        marketplace_plugins.append({
            "name": name,
            "source": f"./plugins/{name}",
            "description": desc,
            "version": VERSION,
            "author": {"name": OWNER},
        })
        required.append({"source": "git-subdir", "url": REPO_URL, "path": f"plugins/{name}"})

    write_json(ROOT / ".claude-plugin" / "marketplace.json", {
        "$schema": "https://code.claude.com/schemas/marketplace.json",
        "name": "web_export",
        "owner": {"name": OWNER},
        "description": "hirosashi's Claude Code skills marketplace. Mirrors skills also used by Devin under .agents/skills/.",
        "version": VERSION,
        "metadata": {"pluginRoot": "./plugins"},
        "plugins": marketplace_plugins,
    })
    write_json(ROOT / ".devin-plugin" / "plugin.json", {
        "name": "web-export-skills",
        "version": VERSION,
        "description": "hirosashi's shared skills for Devin and Claude Code. Generated from .agents/skills/ by scripts/sync-skills.py.",
        "requiredPlugins": required,
    })
    print(f"synced {len(skills)} skill(s): {', '.join(sorted(names))}")


if __name__ == "__main__":
    main()
