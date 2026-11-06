---
name: armheart-deploy-prod-server
description: スイーツ生産管理システム（hirosashi/armheart-sweets）を本番（公開）サーバ（さくら）へ反映し、DB変更を安全に適用して確認する。「本番へ反映」「公開サイトへコピー」と言われたら使う。
---

# 本番サーバへの反映（armheart-sweets）

## 前提
- リポジトリ: `hirosashi/armheart-sweets`（ローカル例 `/home/ubuntu/phase1_dev`）。ホスト名・配置先・公開URLはリポジトリの `deploy.sh`（`prod` ブロック）と `.agents/skills/deploy-prod-server/SKILL.md` を正とする。
- 接続情報は org secret で参照し、値は会話・ログ・ソース・PRに出さない:
  - `ARMHEART_PROD_SSH_USER` / `ARMHEART_PROD_SSH_PASS`（SSH）
  - `ARMHEART_PROD_DB_PASS`（本番DB）
  - exec の `env` に `secret:session:NAME` で束縛する。未設定なら `request_secret`（should_save=true, save_scope=org）で3択を提示。
- サーバのログインシェルは csh。複数コマンドは `ssh ... /bin/sh <<'EOF' ... EOF` で sh に渡す（そのまま `2>&1` 等を書くと `Ambiguous output redirect`）。
- 本番設定 `src/config/config.production.php` はサーバにだけ置く（git管理外、`deploy.sh` は転送しない）。このファイルがあれば `config.php` は必ずこれを読む。`base_path` は公開パス、`debug` は false。
- さくらの「国外IPアドレスフィルタ」がONだと、海外IPからは SSH が `Permission denied`、FTP はパスワード送信後に切断される（許可IPリストはウェブにしか効かない）。パスワードが正しいのに拒否されたら、まずフィルタの状態をユーザーに確認する。

## 手順
1. ローカル検査（`armheart-verify-changes`）を通す。
2. DB変更がある場合は、先にバックアップしてから適用する:
   - `~/.my_armheart.cnf`（umask 077、`[client] host/user/password`）を一時作成し、`mysqldump --defaults-extra-file=... --no-tablespaces --set-gtid-purged=OFF <DB名> > ~/backup/prod_YYYYmmddHHMM.sql`（`~/backup` は 700、Web公開外）
   - migration SQL を scp で `~/backup/` に送り `mysql --defaults-extra-file=... <DB名> < ~/backup/xxx.sql`
   - 終わったら `~/.my_armheart.cnf` を削除する。
3. コード反映: env に `PROD_SSH_USER` / `PROD_SSH_PASS` を束縛して `./deploy.sh prod`（サーバに `config.production.php` が無いと中止する）。
4. 確認: 未ログイン `/` → 302、`/login` → 200、`/config/config.production.php`・`/db/`・`/storage/logs/` → 403。ログイン後（CSRF の input 名は `_token`）`/ /schedule /materials /parts /products /require /progress /orders /stock` が 200 で warning/fatal を含まない。

## 注意点
- 本番DBは実運用データ。開発DBから上書きしない、本番でテストデータを作らない。
- 初回移植時はマスタ（users・companies・suppliers・materials・parts・part_materials・products・product_parts・product_materials）だけを開発DBから `mysqldump --no-data`（AUTO_INCREMENT を除去）＋ `--no-create-info` で移し、在庫・発注・進捗・案件・ログは空で開始した。
- admin のパスワードは本番用の値（ユーザー提供）。開発用の値を使わない。
