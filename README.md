# web_export

運営会社の倒産により管理不能となった WordPress サイトのコンテンツを抜き出し、WordPress サイトとして復元するプロジェクトです。

## フェーズ1: サイト情報の抜き出し・保存

- ドメイン配下の固定ページを HTML として取得・保存
- ヘッダー等で読み込んでいるドメイン配下のファイル（CSS/JS等）を取得・保存
- ドメイン配下の画像をパス構造を維持して保存
- 投稿・カスタム投稿（single ページ）はフェーズ1では対象外
- 別ドメインや外部ページは対応外

## 共有スキル（Devin / Claude Code）

`.agents/skills/<name>/SKILL.md` が正本。`scripts/sync-skills.py` が `plugins/` と両マニフェストを生成する（main への push 時に GitHub Actions が自動実行）。

- Devin: 接続リポジトリとして自動インデックスされる。加えて https://app.devin.ai/customize から `hirosashi/web_export` をプラグイン登録すると全セッションで有効
- Claude Code: `/plugin marketplace add hirosashi/web_export` → `/plugin install <name>@web_export`
- 新規スキルの追加手順は `save-shared-skill` スキルを参照
