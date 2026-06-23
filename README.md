# 概要
Laravel+Dockerの環境構築のサンプル

- Nginx 1.30.2 Stable
- PHP 8.5.6 
- Composer:2.10.1 LTS
- Laravel 13.14.0 
- MySQL 8.4.9 LTS
- Git 最新

※ 26/06時点

# Laravel起動までの流れ

私のブログページにまとめてます。  
[【Docker】【260607】#2 Docker 開発環境構築ファイル作成 & 起動](https://minememo.work/posts/docker_260607_01/)

## localhost
http://localhost:8080

# 主に実装したもの

## 基盤の元となるサンプル
簡単に以下を作成

- MCV+Services+Repositories構造
- フォームリクエスト
- CRUD基盤
- Migrations+Seeders
- 画像保存
- 論理、物理削除
- ログイン
- ロール
- ポリシー
- 単体テスト
- 結合テスト

※ サンプルなのでテストは Create + Index のみで他は割愛  
※ PSR12用に xdebug で自動フォーマットもある



