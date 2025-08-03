<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/your-username/anshin-techo-clean">
    <img src="https://img.shields.io/badge/Project-Anshin--Techo-blue.svg" alt="Project Name">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Laravel Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
</p>

---

# 📘 About Anshin-Techo

「安心手帳」は、高齢者の内服記録を家族が**遠隔で管理**し、**内服忘れをリアルタイムに通知**するWebアプリケーションです。

家族は専用のダッシュボードから服薬状況を確認でき、Laravelのメール送信機能を用いて**通知の自動配信**を実現。さらに、**服薬スケジュールをカレンダー形式で可視化**することで、視覚的にわかりやすい服薬管理を提供します。

---

# ✨ 主な機能

- ✅ **内服記録の管理**  
  日々の服薬状況を記録し、ダッシュボードで確認可能。

- ✉️ **内服忘れ通知**  
  LaravelのMail機能を使用し、未記録があった場合に家族に自動通知。

- 👨‍👩‍👧‍👦 **家族間での共有**  
  複数の家族メンバーで内服情報を共有・管理。

- 📅 **服薬カレンダー機能**  
  カレンダーUIで服薬スケジュールや履歴を一目で把握。

---

# 🧱 技術スタックと選定理由

| 技術 | 用途 | 理由 |
|------|------|------|
| **Laravel 11** | バックエンド | 強力なEloquent ORM、バリデーション、Mailなど、開発効率の高いPHPフレームワーク。 |
| **Vite** | フロントビルド | 高速なHMRとビルド性能で、開発体験が向上。 |
| **Tailwind CSS** | UIデザイン | ユーティリティファーストでレスポンシブ対応が容易。 |
| **PostgreSQL** | データベース | JSONB対応やスケーラビリティが高く、Dockerとの相性も良好。 |
| **Docker + Laravel Sail** | 環境構築 | 環境依存を減らし、チーム開発やCI/CDを容易に。 |
| **Mailpit** | メールテスト | Mailの送受信をローカル環境でテストできる軽量ツール。 |

---

# ⚙️ 開発環境セットアップ

```bash
# リポジトリをクローン
git clone https://github.com/your-username/anshin-techo-clean.git
cd anshin-techo-clean

# Dockerコンテナを起動
./vendor/bin/sail up -d

# 依存関係をインストール
sail composer install
sail npm install

# 環境ファイルの準備
cp .env.example .env

# マイグレーション + シーディング
sail artisan migrate:fresh --seed

# フロントエンドビルド
sail npm run build
