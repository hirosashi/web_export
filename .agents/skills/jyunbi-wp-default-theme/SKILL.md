---
name: jyunbi-wp-default-theme
description: 準備サイト（さくら jyunbi.sakura.ne.jp/wpN）の WordPress `_default` テーマ規約に沿って、固定ページ・投稿CMS（カテゴリ方式）・CF7フォーム・CSSを構築し、SSH+wp-cliで反映・確認する手順。「いつもの準備サイトに構築」「デザイン画像からWordPressを作る」時に使う。
---

# 準備サイト `_default` テーマでのサイト構築

デザイン画像（JPG/PNG）や Figma を元に、準備サイト `https://jyunbi.sakura.ne.jp/wpN/` の有効テーマ `_default` を直接編集してコーポレートサイトを組む手順。

## 前提

- 接続: secret `SAKURA_SSH_HOST` / `SAKURA_SSH_USER` / `SAKURA_SSH_PRIVATE`（鍵は `~/.ssh/sakura_key` に書き出して 600）。値を会話・ログ・コードに出さない。
- サーバ側: `wp` は `/usr/local/bin/wp`、DocumentRoot は `~/www/wpN/`、テーマは `~/www/wpN/wp-content/themes/_default/`。
- WPログイン情報はユーザーから添付で渡される。作業はSSH＋wp-cli中心で行い、管理画面は確認用。
- リモート実行ヘルパーを作っておくと楽:
  ```bash
  cat > ~/rsh <<'EOF'
  #!/bin/bash
  ssh -o BatchMode=yes -i ~/.ssh/sakura_key "${SAKURA_SSH_USER}@${SAKURA_SSH_HOST}" bash -s <<< "cd ~/www/wp3; $1"
  EOF
  chmod +x ~/rsh   # 例: ~/rsh 'wp option get permalink_structure'
  ```

## テーマ規約（必ず守る）

| 対象 | ファイル |
|---|---|
| 固定ページ（スラッグ） | `template-parts/content-<slug>.php`（`page.php` が `get_template_part('template-parts/content', $post->post_name)` で読む） |
| 投稿一覧 | `archive.php` → `template-parts/archive-post.php`（WP-PageNavi 使用） |
| 投稿詳細 | `single.php` → `template-parts/single-post.php` |
| スタイル | `assets/css/style.css`（追記・修正OK。`reset.css` → `style.css` を header.php で直読み） |
| JS | `assets/js/script.js`（footer.php で直読み） |
| 画像 | `assets/img/` |
| 共通 | `header.php` / `footer.php` / `functions.php` は直接編集OK |

- CSSは `html{font-size:10px}` + `@media(max-width:1600px){html{font-size:.62vw}}` + `@media(max-width:767px){html{font-size:2.67vw}}` の rem 基準。SPブレークポイントは 767px。
- クラス名は `xxx__area > .in__box` の BEM風。`.flex__box`（横並び）、`.btn .btn--xxx`、`.ttl`、`.box`、`.tag`、`.ph`（プレースホルダー）程度に絞り、タグ構成はできる限りシンプルに。
- 汎用 `ul li` セレクタは入れ子リストに波及するので `.xxx__list > li` と直下指定する。
- 画像・動画・地図・アイコンなど未提供素材は `<div class="ph">※画像が入ります</div>` で置く。
- `functions.php` に `the_content` 内リンクを `home_url` 付きへ補完するフィルタがあるため、CF7フォーム等にルート相対リンク `/wpN/...` を書くと二重になる。絶対URLで書く。

## 手順

1. **把握**: デザイン画像を全て読み、サイトマップ・共通パーツ（ヘッダー/CTA帯/フッター）・CMS対象・フォーム仕様を箇条書きにしてユーザーへ報告し、方針の承認を得る（独自テーマ新規作成はしない）。
2. **現状確認**（wp-cli）: `wp core version`, `wp theme list`, `wp plugin list`, `wp option get permalink_structure`, `wp post list --post_type=page`, `wp term list category`。
3. **プラグイン**: `wp plugin install --activate advanced-custom-fields contact-form-7 classic-editor seo-simple-pack wp-pagenavi`（不足分のみ）。パーマリンクは `/%category%/%postname%/` が多い。
4. **コンテンツ登録**（wp-cli）:
   - 固定ページ: `wp post create --post_type=page --post_status=publish --post_title=... --post_name=<slug>`。TOPは `show_on_front=page` / `page_on_front=<id>`。
   - CMSは **標準投稿＋親子カテゴリ** で分ける（例: 親 `column`/`voice`、子 `speech`/`voice-corporate`…）。CPTは指示が無ければ作らない。
   - `/column/`・`/voice/` を親カテゴリ一覧URLにするため `functions.php` に `add_rewrite_rule('^column/?$','index.php?category_name=column','top')` 等を追加し、`wp rewrite flush --hard`。
   - サンプル投稿を数件入れる（一覧・詳細・TOP連携の確認用）。
5. **テーマ取得**: `rsync -az -e "ssh -i ~/.ssh/sakura_key" user@host:~/www/wpN/wp-content/themes/_default/ ~/repos/wpN-theme/_default/` → `git init` して元状態を1コミット。
6. **実装**: header/footer/functions → `content-top.php` → 各 `content-<slug>.php` → `archive-post.php`/`card-post.php`/`single-post.php` → `style.css` → `script.js`。共通のパンくず＋h1は `ld_page_head($title,$lead)` のようなヘルパーを functions.php に置く。
7. **フォーム**: `cf7-confirm-toggle-form` スキル参照。
8. **検証**: `for f in $(find . -name '*.php'); do php -l $f | grep -v "No syntax"; done`（既存 `comments.php` のエラーは既知・無視）。
9. **反映**: サーバでバックアップ後 rsync。
   ```bash
   ~/rsh 'cd wp-content/themes && [ -d _default_bak ] || cp -r _default _default_bak'
   rsync -az --delete --exclude .git -e "ssh -i ~/.ssh/sakura_key" _default/ user@host:~/www/wpN/wp-content/themes/_default/
   ~/rsh 'wp rewrite flush --hard'
   ```
10. **確認**: 主要URLを curl で 200 確認 → headless Chrome で全ページのフルスクリーンショット（`google-chrome --headless=new --window-size=1440,6000 --screenshot=x.png URL`、SPは 390 幅）を撮って崩れを目視 → フォーム動作は実ブラウザで確認。
11. ローカルの git にコミット。テーマにリモートは無いことが多いので PR 先はユーザーへ確認。報告は URL・スクリーンショット・プレースホルダー箇所・退避先を簡潔に。

## 注意

- `git rm` はリポジトリルートから実行する（サブディレクトリからの相対パスで失敗しがち）。
- 個別ページが不要なCMS（クライアントの声等）は `single-post.php` 冒頭で親カテゴリ判定して一覧へ `wp_safe_redirect`。
- 一覧の件数は `pre_get_posts` で `posts_per_page` を設定（デザインの1ページ件数に合わせる）。
