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

- 💊 **内服一覧**
  内服薬の一覧を検索機能と作用と副作用なども確認が可能。

- 📅 **服薬カレンダー表示**  
  `FullCalendar.js` を使用し、服薬スケジュールを視覚的に表示。

- ✉️ **メールによる内服忘れ通知**  
  Laravelの `Mail` ファサードを活用し、自動で家族に通知。

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
```

```bash
# アプリケーションにアクセス
ブラウザで http://localhost にアクセスしてください。
```

---

# 🚀 今後の機能追加（Future Features）

今後のアップデートで追加を予定している、または検討中の機能一覧です。

### 🔔 通知・アラート関連
- [ ] **プッシュ通知の導入**
      スマートフォンやブラウザへリアルタイム通知を送信。
- [ ] **バイタル異常の通知**
      血圧や体温などが異常値の場合、家族や医療者に自動通知。
- [ ] **通知先のカスタマイズ**
      家族、ケアマネージャー、医療機関など通知先を選択可能に。
- [ ] **服薬リマインダー通知**
      薬の服用時間ごとにリマインド通知。

---

### 💊 内服記録・管理の強化
- [ ] **服薬スケジュールの設定**
      「いつ」「何錠」「どの薬」を服用するかを自由に登録。
- [ ] **薬の在庫（所持数）管理**
      残り錠数の自動計算、服用忘れ・不足時の通知。
- [ ] **服薬しやすいUI**
      ワンタップ記録、音声入力への対応など。

---

### 📊 健康データの蓄積・可視化
- [ ] **バイタル記録機能**
      血圧・体温・脈拍・血糖値などを定期記録。
- [ ] **グラフ表示**
      健康データや服薬履歴を時系列で可視化。
- [ ] **異常傾向の分析レポート**
      AIを活用して体調変化の兆候を事前に検知。

---

### 🧑‍🤝‍🧑 家族・チーム連携機能
- [ ] **複数家族メンバーの管理**
      家族ごとに閲覧・編集権限を設定可能に。
- [ ] **担当者の役割分担**
      服薬確認、体調記録、通院付き添いなどタスクの分担表示。
---

# 📝 現状の課題と今後の展望

現在の「安心手帳」は、ユーザーが自由に内服薬をCRUD操作できる仕組みですが、
これは柔軟性がある反面、**医療的な正確性や安全性の担保**という面では限界があります。

### ❗️ 現状の課題

- **薬剤の情報に公式APIが存在しない**
  → 薬の用法・用量・成分・商品名などを一貫して取得できるデータベースがない。

- **医療者でないと判断が難しい項目が多い**
  → 同じ薬でも用法や適応疾患が違ったり、服用タイミングが医師の裁量で変わることも。

- **誰でも薬データを登録・変更できてしまう**
  → 誤登録による副作用や服薬ミスのリスク。

---

### 🌱 今後の展望（将来的な拡張可能性）

- [ ] **公的・民間の薬剤データベースとの連携（API）**
      例：PMDA、KEGG、MediBank などとのデータ連携

- [ ] **薬剤師・医師アカウントの導入**
      医療従事者が服薬データを監修・承認できるようにする

- [ ] **薬ごとのプロファイル自動生成**
      「この薬は朝食後に1錠」などをプリセットで表示

- [ ] **監査ログの導入**
      「誰が・いつ・どの薬情報を変更したか」を記録

