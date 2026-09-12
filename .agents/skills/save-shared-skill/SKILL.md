---
name: save-shared-skill
description: 再利用できそうな作業手順・調査手順・ノウハウを、Devin と Claude Code の両方で使える共有スキルとして hirosashi/web_export に保存するときに使う。
---

# 共有スキルの保存手順

hirosashi/web_export は Devin と Claude Code の共有スキル置き場。正本は `.agents/skills/<name>/SKILL.md` のみで、`plugins/` と両マニフェスト（`.claude-plugin/marketplace.json`, `.devin-plugin/plugin.json`）は自動生成される。

## いつ使うか

- タスク中に、別プロジェクトでも繰り返し使える手順が固まったとき（例: 特定CMSの調査手順、移行作業のチェックリスト、環境固有のデプロイ手順）
- 既存スキルに不足・誤りを見つけてブラッシュアップしたいとき
- 一度きりのタスク固有の手順は保存しない

## 手順

1. `hirosashi/web_export` をクローンし、新しいブランチを作る
2. `.agents/skills/<kebab-case-name>/SKILL.md` を作成または更新する
   - 先頭に YAML frontmatter: `name`（ディレクトリ名と同じ）と `description`（1文。どんな時に使うかが分かるように）
   - 本文: 前提 / 手順 / 注意点 の構成で、他人が読んでそのまま実行できる粒度にする
3. `python3 scripts/sync-skills.py` を実行して `plugins/` とマニフェストを再生成する（main への push 時に GitHub Actions でも自動実行されるが、PR 内で差分を見せるためにローカルでも実行する）
4. PR を作成し、依頼者にマージを依頼する。マージ後は Devin のリポジトリ自動インデックスとプラグイン経由で全セッションに反映される

## 注意点

- `plugins/**` と `.claude-plugin/`, `.devin-plugin/` は手で編集しない（次回の同期で上書きされる）
- スキル名は小文字英数字とハイフンのみ
- 秘密情報（パスワード、ホスト名、鍵）は書かない。必要なら secret 名（`${NAME}`）で参照する
