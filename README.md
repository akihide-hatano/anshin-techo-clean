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

**「安心手帳」**は、高齢者の服薬記録を家族が**遠隔から管理**できるWebアプリケーションです。  
Laravelの**メール送信機能**と**カレンダーUI**を活用し、服薬状況を**リアルタイムで確認・通知**します。

---

# ✨ 主な機能

- ✅ **内服記録の管理**  
  ダッシュボードで服薬履歴を一括管理。

- 📅 **服薬カレンダー表示**  
  `FullCalendar.js` を使用し、服薬スケジュールを視覚的に表示。

- ✉️ **メールによる内服忘れ通知**  
  Laravelの `Mail` ファサードを活用し、自動で家族に通知。

- 👨‍👩‍👧‍👦 **家族間の共有**  
  家族メンバーを登録し、共同で服薬管理が可能。

---

# 🧱 技術スタックと選定理由

| 技術 | 用途 | 理由 |
|------|------|------|
| **Laravel 12** | バックエンド | RESTfulな構造と、Mail・Schedule機能の柔軟性が高い。 |
| **Blade + JavaScript** | フロントエンド | Laravelと親和性が高く、最小限の構成で動的UIを実現。 |
| **Vite** | ビルドツール | モダンな開発環境を即時反映で実現。 |
| **Tailwind CSS** | UIスタイリング | コンポーネントを素早く整形でき、保守性が高い。 |
| **FullCalendar.js** | カレンダー表示 | シンプルかつ高機能な日付UIライブラリ。 |
| **PostgreSQL** | RDBMS | JSONB対応・正規化のしやすさ。 |
| **pgAdmin** | DB管理ツール | GUIでのデータ確認やクエリ発行が容易。 |
| **Docker + Laravel Sail** | 環境構築 | 環境差異をなくし、開発効率を向上。 |
| **Mailpit** | メールテスト | 実際の送信をせずにローカル確認が可能。 |

---

# ⚙️ 開発環境セットアップ

```bash
# リポジトリをクローン
git clone https://github.com/your-username/anshin-techo-clean.git
cd anshin-techo-clean
```

```bash
# Dockerコンテナを起動
./vendor/bin/sail up -d
```

```bash
# 依存関係をインストール
sail composer install
sail npm install
```

```bash
# 環境ファイルの準備
cp .env.example .env
```

```bash
# マイグレーション + シーディング
sail artisan migrate:fresh --seed

```bash
# アプリケーションにアクセス
ブラウザで http://localhost にアクセスしてください。
```