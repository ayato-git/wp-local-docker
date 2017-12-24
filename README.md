## 言語について

- 最新のWPを使っていても、更新を促す通知が管理画面に出続けるバグへの対処が加えられています
  - バグの例: WP4.9.1を使っている時に、管理画面に「WordPress4.9.1がリリースされました」と出続ける
  - 動作上は問題無いとされているものの、WP本体が正しく更新できているか判らなくなってしまう
- このバグの発生条件は
  - WP本体の言語と、管理画面から設定した使用中の言語が異なる場合
	- WP本体にローカライズ版を使っていて、その最新バージョンが英語版の最新バージョンに追いついていない場合
	- の2通り。
	- なので、初回インストール時に「英語版WPを設置し、インストールする時に日本語を選択した」場合は必ずこのバグを踏む
- WordPress.Skeletonが使う [johnpbloch/wordpress](https://packagist.org/packages/johnpbloch/wordpress) は言語の指定が出来ないので、そのまま composer.json を使ってWP本体を更新すると、前述のバグ発生条件の1つ目に必ず抵触する
  - なので、 composer の `post-install-cmd` / `post-update-cmd` の後でそのチェックと修正を行っている
