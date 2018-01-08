## ディレクトリ構成

```
├── LICENSE
├── README.md
├── composer.json
├── config
│   ├── certs
│   ├── docker
│   ├── nginx
│   ├── php-fpm
│   └── variables.env.sample
├── docker-compose.yml
├── docs
│   ├── README.10up_wp-local-docker.md
│   └── README.ttskch_WordPress.Skeleton.md
├── local-config-sample.php
├── logs
│   └── nginx
├── scripts
│   ├── Installer.php
│   └── Localizer.php
├── wp
│   ├── index.php
│   ├── 〜省略〜
│   ├── wp-content
│   │   ├── languages
│   │   ├── mu-plugins -> ../../wp-content/mu-plugins // composer で wpackagist.org から require した必須プラグイン
│   │   ├── my-themes -> ../../wp-content/my-themes   // 独自製作のテーマ
│   │   ├── plugins -> ../../wp-content/plugins       // WP管理画面またはwp-cli経由で入れたプラグイン
│   │   ├── themes
│   │   ├── upgrade
│   │   └── uploads -> ../../wp-content/uploads
│   └── 〜省略〜
├── wp-cli.yml
├── wp-config.php
└── wp-content
    ├── mu-plugins
    ├── my-themes
    ├── plugins
    └── uploads
```

`wp` ディレクトリは `composer.json` で指定したWordPressコアの配置場所。  
リポジトリには含まれません（ `.gitignore` に指定している）が、  
`wp-content` ディレクトリとのリンクを示すために記載しています。

基本的に `wp` ディレクトリ以下を人の手で触ることはしません。(後述の再インストール手順も参照)


## インストール手順

### docker-composeを使ったローカル開発環境

```
$ cp local-config-sample.php local-config.php
$ vim local-config.php

$ cp config/variables.env.sample config/variables.env
$ vim config/variables.env

$ docker-compose up -d
$ docker-compose exec --user www-data wp composer install

// ブラウザで通常のWPのインストール作業へ
// wp-cli でインストール作業を行う場合は以下の通り

$ docker-compose exec --user www-data wp bash
www-data@45e0f84cff0e:~/html$ wp ~~~
```

なんらかの理由でうまく行かなかった時は以下のような方法で再インストールしてみて下さい


```
$ docker-compose down
$ rm -rf composer.lock vendor wp
$ docker-compose up -d
$ docker-compose exec --user www-data wp composer install
```


## 日本語化について

最新のWPを使っていても、更新を促す通知が管理画面に出続けるバグへの対処が加えられています

> バグの例: WP4.9.1を使っている時に、管理画面に「WordPress4.9.1がリリースされました」と出続ける

WP開発陣によって動作上は問題無いとされているものの、WPコアが正しく更新できているか判らなくなってしまいます。  
このバグの発生条件は  

- WPコアファイルの言語と、管理画面から設定した使用中の言語が異なる場合
- WPコアファイルにローカライズ版を使っていて、その最新バージョンが英語版の最新バージョンに追いついていない場合

の2通り。  
なので、WP初回インストール時に「英語版WPコアファイルを設置し、インストールする時に日本語翻訳を選択した」場合は必ずこのバグを踏む。

WordPress.Skeletonが使う [johnpbloch/wordpress](https://packagist.org/packages/johnpbloch/wordpress) は使用するコアファイルのlocale指定が出来ないので、  
日本語化を行うと「英語版WPを設置し、インストールする時に日本語翻訳を選択した」というバグ発生条件に該当します。

また、日本語化を行った後で composer を使ってWP本体を更新すると [johnpbloch/wordpress](https://packagist.org/packages/johnpbloch/wordpress) の入れ直しとなるので、これもまたバグ発生条件に該当します。


なので、 composer の `post-install-cmd` / `post-update-cmd` フックでwp-cliを用いたチェックと修正を行っています
