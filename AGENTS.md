# Shared skills (hirosashi/web_export)

このリポジトリは Devin と Claude Code の共有スキル置き場です。

- 正本は `.agents/skills/<name>/SKILL.md`。`plugins/`、`.claude-plugin/marketplace.json`、`.devin-plugin/plugin.json` は `scripts/sync-skills.py` が生成するので手で編集しない。
- タスク中に別プロジェクトでも再利用できる手順が固まったら、`save-shared-skill` スキルの手順に従ってこのリポジトリへ PR を出して共有する。
