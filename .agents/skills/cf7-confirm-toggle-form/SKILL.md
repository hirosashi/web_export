---
name: cf7-confirm-toggle-form
description: Contact Form 7 で「問い合わせ種別による項目の表示切替」と「確認画面風UI（確認→戻る→送信）」を、プラグイン追加なし（テーマの JS/CSS＋autop 無効化）で実装する手順。「種別で入力欄を切り替えたい」「確認画面を付けたい」フォーム要件の時に使う。
---

# CF7 種別切替＋確認画面風フォーム

Contact Form 7（CF7）単体で、デザインによくある「種別プルダウンで予約欄などを出し分け」「確認画面→送信」の2機能をテーマ側の JS/CSS だけで実装する。マルチステップ系プラグインは入れない。

## 前提

- CF7 有効。フォーム本文は wp-cli で更新できる: `wp post meta update <form_id> _form "$(cat cf7-form.txt)"`（`wp post list --post_type=wpcf7_contact_form` で ID 確認）。
- テーマ側で `functions.php` / `assets/css/style.css` / `assets/js/script.js` を編集できること（`jyunbi-wp-default-theme` 参照）。
- 固定ページ `contact` の本文にショートコード `[contact-form-7 id="N" title="..."]` を入れ、`content-contact.php` では `the_content()` で出す。

## 手順

1. **autop 無効化**（CF7 が `<p>`/`<br>` を勝手に挿入してレイアウトが崩れるため）:
   ```php
   add_filter( 'wpcf7_autop_or_not', '__return_false' );
   ```
2. **フォーム本文**は `div.form__row > label + div` の行構造で書く（label 幅30% / 入力70%）。切替対象にだけ id/class を付ける:
   ```html
   <div class="form__row"><label>お問い合わせ種別<span class="req">必須</span></label>
   <div>[select* type id:contact-type first_as_label "選択してください" "お問い合わせ" "資料請求" "講座予約"]</div></div>
   ...
   <div class="form__row" id="row-message"><label>お問い合わせ内容</label><div>[textarea your-message]</div></div>
   <div class="form__reserve" id="row-reserve">
     <h3 class="form__ttl">ご予約内容</h3>
     ... 希望日 [select reserve-month ...] 月 [select reserve-day ...] 日 / 希望時間帯 / 備考 ...
   </div>
   <p class="form__agree">[acceptance agree] 個人情報保護ポリシーに同意する [/acceptance]</p>
   <div class="form__btns">
     <button type="button" class="btn" id="form-confirm">確認画面へ進む</button>
     <button type="button" class="btn btn--gray" id="form-back">入力画面に戻る</button>
     [submit class:btn "送信する"]
   </div>
   ```
   - 出し分け対象の項目は **必須(`*`)にしない**（非表示時に CF7 サーバ側バリデーションで弾かれる）。必須表示は `<span class="req">` で見せ、JS 側で「表示中かつ空」だけを検査する。
   - フォーム内リンク（プライバシーポリシー等）は **絶対URL** で書く（テーマの the_content フィルタでルート相対が二重化することがある）。
3. **CSS**（状態はフォーム要素のクラスで切替）:
   ```css
   .form__row{display:flex;gap:2rem;padding:1.5rem 0;border-bottom:1px solid #eee}
   .form__row>label,.form__row>p:first-child{width:30%;font-weight:700}
   .form__row>div,.form__row>p:last-child{width:70%}
   .form__reserve,#form-back,.wpcf7-form .wpcf7-submit{display:none}
   .form--reserve .form__reserve{display:block}
   .form--reserve #row-message{display:none}
   .form--confirm input,.form--confirm select,.form--confirm textarea{pointer-events:none;background:#f5f5f5;border-color:transparent}
   .form--confirm #form-confirm{display:none}
   .form--confirm #form-back,.form--confirm .wpcf7-submit{display:inline-block}
   .wpcf7-not-valid{border-color:#d33!important}
   ```
4. **JS**（`script.js`。フォーム全体に `form--reserve` / `form--confirm` を付け外し）:
   ```js
   document.addEventListener('DOMContentLoaded', function () {
     var form = document.querySelector('.wpcf7-form'); if (!form) return;
     var type = form.querySelector('#contact-type');
     var toggle = function(){ form.classList.toggle('form--reserve', type && type.value === '講座予約'); };
     if (type) { type.addEventListener('change', toggle); toggle(); }
     var confirmBtn = form.querySelector('#form-confirm'), backBtn = form.querySelector('#form-back');
     confirmBtn && confirmBtn.addEventListener('click', function () {
       var ok = true;
       form.querySelectorAll('[aria-required="true"]').forEach(function (el) {
         var visible = el.offsetParent !== null;
         var empty = el.type === 'checkbox' ? !el.checked : !el.value;
         el.classList.toggle('wpcf7-not-valid', visible && empty);
         if (visible && empty) ok = false;
       });
       if (!ok) { alert('必須項目をご入力ください。'); return; }
       form.classList.add('form--confirm'); form.scrollIntoView({behavior:'smooth'});
     });
     backBtn && backBtn.addEventListener('click', function(){ form.classList.remove('form--confirm'); });
     document.addEventListener('wpcf7invalid', function(){ form.classList.remove('form--confirm'); });
   });
   ```
5. **確認**: 実ブラウザで (a) 種別を「講座予約」にすると予約欄が出て問い合わせ内容が隠れる、(b) 必須未入力で確認ボタン→alert、(c) 入力後→入力欄がグレー化し「戻る」「送信」が出る、(d) 送信後 `wpcf7invalid` で入力画面に戻る、を目視。メール実送信の確認は依頼者に必要か確認する。

## 注意

- CF7 の select は `first_as_label` を付けて先頭を「選択してください」にすると `value` が空になり必須判定が効く。
- `wp post meta update` で `_form` を書き換えた後、管理画面から一度保存しなくても即反映される（メール本文 `_mail` に新フィールド `[reserve-month]` 等を追加するのを忘れない）。
- 確認画面は見た目のみ（サーバ側は1ステップ）。厳密な確認画面が必須要件なら別途プラグイン検討。
