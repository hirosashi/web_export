/* このJavaScriptファイルは、</body>の直前に呼ばれます */
/* ここから：スマホメニュー関係の処理 */
let menuButton = document.getElementById('menu-button');
let mainMenuContainer = document.getElementById('main-menu-container');
let mainMenuItems = mainMenuContainer.querySelectorAll('a');
let contentBody = document.getElementById('body');
// スマホメニューボタンが押されたら、is-activeクラスを付け、メニューを開け閉めする
menuButton.addEventListener("click", function() {
	menuButton.classList.toggle("is-active");
	mainMenuContainer.classList.toggle("is-active");
	contentBody.classList.toggle('is-active');
}, false);

var spMenuClose = function(){
	menuButton.classList.remove("is-active");
	mainMenuContainer.classList.remove("is-active");
	contentBody.classList.remove("is-active");
}

for(let i = 0; i < mainMenuItems.length; i++){
	mainMenuItems[i].addEventListener('click',spMenuClose,false);
}

// メニューのアイテムがクリックされたらメニューを閉じる。主にページ内リンク向け
// const documentUrl = location.origin + location.pathname + location.search;
// for(let i = 0; i < mainMenuItems.length; i++){
// 	mainMenuItems[i].addEventListener("touchend", function(){
// 		let anchor = event.currentTarget;
// 		let anchorUrl = anchor.protocol + '//' + anchor.host + anchor.pathname + anchor.search;
// 		setTimeout(function(){
// 			if(documentUrl == anchorUrl){
// 				menuButton.classList.remove("is-active");
// 				mainMenuContainer.classList.remove("is-active");
// 				contentBody.classList.remove("is-active");
// 				window.location = anchorUrl;
// 			}else{
				
// 			}
// 		},700);
// 	}, false);
// }
//PC用
// for(let i = 0; i < mainMenuItems.length; i++){
// 	mainMenuItems[i].addEventListener("click", function(){
// 		let anchor = event.currentTarget;
// 		let anchorUrl = anchor.protocol + '//' + anchor.host + anchor.pathname + anchor.search;
// 		setTimeout(function(){
// 			if(documentUrl == anchorUrl){
// 				menuButton.classList.remove("is-active");
// 				mainMenuContainer.classList.remove("is-active");
// 				contentBody.classList.remove("is-active");
// 				window.location = anchorUrl;
// 			}else{
				
// 			}
// 		},700);
// 	}, false);
// }
/* ここまで */
jQuery(document).ready(function($) {
	/* スクロールした量に応じて、ページトップに戻るボタンを表示する */
	let backtopButton = $('#pc-backtop');
	let showRange = 100;
	$(window).scroll(function () {
        if ($(this).scrollTop() > showRange) {
            backtopButton.addClass('is-visible');
        } else {
            backtopButton.removeClass('is-visible');
        }
    });

});
