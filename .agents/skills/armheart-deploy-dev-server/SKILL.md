---
name: armheart-deploy-dev-server
description: 開発サーバ（さくら、ホストは secret ARMHEART_DEV_HOST）へ src/ を反映し、HTTP疎通を確認する。「開発サーバへ反映」「デプロイ」「さくらに上げる」と言われたら使う。
---

# 開発サーバへの反映

## 前提
- プロジェクト: `/home/ubuntu/phase1_dev`
- 反映スクリプト: `deploy.sh`（`src/` を tar→scp→ssh で展開。`config.local.php`・`install.php`・ログは除外）
- SFTP: host `${ARMHEART_DEV_HOST}` / port 22 / user `${ARMHEART_SFTP_USER}` / 配置先 `${ARMHEART_DEV_DIR}`
- 接続情報は secret で参照する: `ARMHEART_DEV_HOST`（ホスト名）/ `ARMHEART_SFTP_USER` / `ARMHEART_SFTP_PASS` / `ARMHEART_DEV_DIR`（配置先の絶対パス）/ `ARMHEART_ADMIN_PW`（admin 初期パスワード）。値を会話・ログ・ソースに出力しないこと。
- secret が未設定なら `request_secret`（should_save=true, save_scope=org）で3択を提示して登録してもらう。
- 確認URLは `https://${ARMHEART_DEV_HOST}/armheart.com/`

## 手順
1. 反映前にローカル検査（`armheart-verify-changes` スキル）を通す。
2. デプロイ実行（`exec` の `env` に `SFTP_PASS=secret:org:ARMHEART_SFTP_PASS` を渡す）:
   ```bash
   cd /home/ubuntu/phase1_dev && ./deploy.sh
   ```
   - `Permission denied` が出たら `echo ${#SFTP_PASS}`（長さのみ）で渡っているか確認する。値は表示しない。
3. DBスキーマ変更を伴う場合は `armheart-apply-db-schema` スキルで開発サーバDBにも適用する。
4. 疎通確認（未ログインは 302、静的ファイルは 200）:
   ```bash
   for p in "" login assets/css/app.css assets/js/app.js; do
     printf '%s ' "$p"; curl -s -o /dev/null -w '%{http_code}\n' "https://${ARMHEART_DEV_HOST}/armheart.com/$p"
   done
   ```
5. ログイン後画面の確認が必要なら cookie jar でログインして対象URLを叩く:
   ```bash
   J=/tmp/cj.txt; U=https://${ARMHEART_DEV_HOST}/armheart.com
   T=$(curl -s -c $J $U/login | grep -oP 'name="_token" value="\K[^"]+')
   curl -s -b $J -c $J -o /dev/null -d "_token=$T&login_id=admin&password=${ARMHEART_ADMIN_PW}" $U/login
   for p in progress stock require orders; do printf '%s ' $p; curl -s -b $J -o /dev/null -w '%{http_code}\n' $U/$p; done
   ```
   （`_token` の input 名は `src/app/Core/Csrf.php` を確認）
6. ユーザーへは URL と変更点を簡潔に報告する。開発サーバの在庫等はデモ値である旨を必要に応じて添える。

## 注意
- `.htaccess`（`src/.htaccess`, `src/app/.htaccess`）は削除・除外しない（内部フォルダ保護）。
- `config/config.sakura.php` は開発サーバ側に既にあるもの。上書きしてよいが、内容を会話に出さない。
