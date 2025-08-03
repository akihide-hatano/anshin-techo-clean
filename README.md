<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/your-username/anshin-techo-clean"><img src="https://img.shields.io/badge/Project-Anshin--Techo-blue.svg" alt="Project Name"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Laravel Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

About Anshin-Techo
「安心手帳」は、高齢者の内服記録を家族が遠隔で管理し、内服忘れを通知するウェブアプリケーションです。内服状況をリアルタイムで把握し、内服忘れがあった際には家族にメールで通知することで、服薬コンプライアンスの向上と家族の安心をサポートします。

主な機能
内服記録の管理: 内服状況を記録し、ダッシュボードで一目で確認できます。

内服忘れ通知: 内服記録が未完了の場合、登録されたメールアドレスに通知を送信します。

家族間での共有: 家族メンバーが内服記録を共有し、協力して管理できます。

Technical Stack
Backend: Laravel 11 (PHP)

Frontend: JavaScript, Vite, Tailwind CSS

Database: PostgreSQL (Docker Sail)

Container: Docker

Mail Testing: Mailpit

Development Setup
プロジェクトをローカルで実行するには、以下の手順に従ってください。

リポジトリをクローンする

Bash

git clone https://github.com/your-username/anshin-techo-clean.git
cd anshin-techo-clean
Dockerコンテナを起動する

Bash

./vendor/bin/sail up -d
依存関係をインストールする

Bash

sail composer install
sail npm install
環境設定ファイルを準備する

Bash

cp .env.example .env
データベースのマイグレーションとシードの実行

Bash

sail artisan migrate:fresh --seed
Viteのビルド

Bash

sail npm run build
アプリケーションにアクセス
ブラウザで http://localhost にアクセスしてください。

Roadmap
家族メンバー機能: 複数の家族メンバーがユーザーの記録を管理できる機能。

通知の送信先の拡張: ダッシュボードや複数のメールアドレスへの通知送信。

通知の多様化: 内服切れやバイタルサインの異常など、内服忘れ以外の通知機能。

プッシュ通知: スマートフォンアプリへのリアルタイム通知。