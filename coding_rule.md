# Laravel 13 コーディング規約

## はじめに

今後できるだけ改修、分析、レビューで困らないように、共通認識として守った方が良さそうなことを簡単に記載します。

すべてを最初から完璧に守るのは難しいと思います。
まずは命名、責務分離、フォルダ構成をできるだけ統一し、誰が見ても処理の場所を予測しやすいコードを目指します。

一番大事なのは、知らない人が見たときにも流れがわかるようにコードを記載する思いやりの意識かなと思います。

**何かおかしな点があれば、遠慮なく指摘・改善提案をお願いします。**
**コード整形など、自動管理で便利なツールがあれば導入を検討します。**

---

## 0. 基本方針

Laravelでは以下を基本方針とします。

* PHPの標準規約は **PSR-12** を基準にする  
  ※基本的に意識しなくていいようにコード整形で制御する  
* クラスのautoloadは **PSR-4** に従う  
  ※クラス名とファイル名は一致させる  
  ※namespaceとフォルダ構成は一致させる  
* Laravel標準の命名規則、フォルダ構成に合わせる
* URLはWeb標準として読みやすい **kebab-case** を使用する
* DBのテーブル名、カラム名はLaravel標準に合わせる
* Controllerに処理を書きすぎず、Service、Repositoryに分ける

※ PSR-2は現在では古く、PSR-12が推奨されているため、PSR-12を基準にします。


---

## 1.コーディングスタイル

基本はPSR-12に合わせます。  

* インデントはスペース4つ
* PHPファイルの開始タグは `<?php`
* クラス名はPascalCase
* メソッド名はcamelCase
* 変数名はcamelCase
* 1行が長くなりすぎないようにする
* 不要なuse文は削除する
* 使っていない変数は残さない

---

## 2. コード整形

Laravelでは、コード整形ツールとして Laravel Pint の利用を検討します。

目的：

* インデントのズレを防ぐ
* PSR-12に沿った整形を自動化する
* 人による書き方の差を減らす
* レビュー時に本質的な処理を確認しやすくする

手動で毎回規約を確認するのではなく、基本は自動整形に任せます。

---

## 3. 命名の意識

- 汎用的過ぎるワードは避け、どんな機能か予測ができる名前にする
- マジックナンバーを使わない そのうえで命名規則を守る

---

## 4. コメントについて

多すぎるコメントは好まれませんが、読んだ人がわからないが一番よくありません。  
以下のようにコードを見てもパッとわからないことを中心に、今後誰かが見たときに困らないように記載しておきましょう

- 実装背景
- 機能の説明、補足
- 今後の懸念など

またパっと見でコードを読めばわかるようなコメントは、基本的に不要です。

---

## 5. 1クラス1責務

1つのクラスに役割を持たせすぎないようにします。
コードが長すぎると以下の弊害が生まれます。  

- 仕様把握、解析、改修ができない
- コードレビューができずに案件が進まない
- テストの実装が範囲広すぎ + 分離されてない場合にできない

人がパッと見てわかる程度の短いコード、分離「1クラス1責務」を意識しましょう。    
1ファイルのコードが極力短く、依存性がなく、1クラスの役割を1つに絞る意識が大事です。  
※1クラスに複数メソッドはOK。適切な分離で少ないのが一番べスト。  

---

## 6. 命名規則

### クラス名

クラス名は **PascalCase** を使用します。

---

### Controller

末尾に `Controller` を付けます。

```php
UserController
ProductController
OrderController
```

---

### Service

末尾に `Service` を付けます。

```php
UserService
ProductService
OrderService
```

---

### Repository

末尾に `Repository` を付けます。

```php
UserRepository
ProductRepository
OrderRepository
```

---

### FormRequest

登録用、更新用で分けます。

```php
UserStoreRequest
UserUpdateRequest
```

---

### メソッド名

メソッド名は **camelCase** を使用します。

LaravelのControllerでは、Laravel標準のCRUD名を優先します。

```php
index()
create()
store()
edit()
update()
destroy()
```

---

### 変数名

変数名は **camelCase** を使用します。

---

### 定数

定数は **UPPER_SNAKE_CASE** を使用します。

---

## 7. DB命名規則

### テーブル名

テーブル名は **複数形のsnake_case** にします。

```text
users
user_profiles
orders
order_details
```

LaravelのEloquentは、基本的にModel名の複数形をテーブル名として扱うため、Laravel標準に合わせます。

```php
User → users
Product → products
OrderDetail → order_details
```

---

### カラム名

カラム名は **snake_case** にします。

```text
id
name
email
password
profile
icon_path
created_at
updated_at
```

外部キーは「テーブル名+_id」の形にします。

```text
user_id
product_id
order_id
```

---

## 8. URL・ルーティング命名

URLはWeb標準として読みやすい **kebab-case** を使用します。

基本方針：

* URLは小文字
* 単語区切りが必要な場合は kebab-case

---

## 9. MVC基本フォルダ構成

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── UserController.php
│   └── Requests/
│       ├── UserStoreRequest.php
│       └── UserUpdateRequest.php
├── Models/
│   └── User.php
├── Services/
│   └── UserService.php
└── Repositories/
    └── UserRepository.php

resources/
└── views/
    └── users/
        ├── index.blade.php
        ├── create.blade.php
        └── edit.blade.php
```

---

## 10. MVC + Service + Repository の流れ

```text
View
↓
Controller
↓
Service
↓
Repository
↓
Model
↓
DB
```

役割：

```text
View
→ 画面表示

Controller
→ 入力受付、画面遷移、Service呼び出し

Service
→ 業務ロジック

Repository
→ DB取得・保存・更新・削除

Model
→ テーブル定義、fillable、リレーション

DB
→ データ保存
```

---

## 11. Controllerの設計

Controllerには処理を書きすぎないようにします。

Controllerで行うこと：

* 画面を表示する
* Requestを受け取る
* Serviceを呼び出す
* 成功時、失敗時の遷移を決める

---

## 12. Serviceの設計

Serviceには業務ロジックを書きます。  
※独自ロジックが増えるので、ここが単体テストの主な対象にもなります。

Serviceで行うこと：

* 登録、更新、削除などの処理の流れ
* パスワードHash化
* ファイルアップロード処理
* トランザクション管理
* Repository呼び出し

---

## 13. Repositoryの設計

RepositoryにはDB操作を書きます。

Repositoryで行うこと：

* 全件取得
* ID検索
* 登録
* 更新
* 削除

Repositoryに画面遷移やバリデーションは書きません。

---

## 14. インターフェースの設計

今回は一旦保留。  

主に以下の時に入れる

- 必須メソッドが決まってる、
- 大規模 
- 外部APIと連携する
- 本番とテストで処理を切り替えたい
- Repositoryの実装が複数ある


---

## 15. Modelの設計

Modelにはテーブル定義、fillable、リレーションを記載します。

例：

```php
class User extends Authenticatable
{
    protected $fillable = [
        'icon_path',
        'name',
        'email',
        'password',
        'profile',
    ];
}
```

Modelには複雑な処理を書きすぎないようにします。

---

## 16. バリデーション

バリデーションはControllerに直接書かず、FormRequestに分けます。

登録用：

```php
UserStoreRequest
```

更新用：

```php
UserUpdateRequest
```

例：

```php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8'],
        'profile' => ['nullable', 'string'],
        'icon' => ['nullable', 'image', 'max:2048'],
    ];
}
```

必須項目：

* name
* email
* password

任意項目：

* icon
* profile

---

## 17. Viewの命名

Viewファイルは機能ごとにフォルダを分けます。

```text
resources/views/users/index.blade.php
resources/views/users/create.blade.php
resources/views/users/edit.blade.php
```

※一旦Blade機能で実装し複雑なことは避けます  
※実務ではReactなど使われますが、今回はしません。

---

## 18. エラーハンドリング

Laravelでは、想定外の例外は原則としてLaravel標準の例外ハンドリングに任せます。  

Controller、Service層で成功時・失敗時の画面遷移、メッセージ表示、代替処理、追加ログ出力など、
その場で処理分岐が必要な場合のみ try-catch を使用します。  

処理分岐が不要な場合は try-catch 不要です。  

catch した例外を握りつぶす場合は、必要に応じて report($e) や Log::error() で記録します。  

バリデーションエラーは FormRequest、404は findOrFail() などLaravel標準の例外処理、
認可エラーは Middleware / Policy / Gate に任せます。  
---

## 19. テストしやすさ

テストしやすくするため、処理を分ける構成になっています。

* Controllerに処理を書きすぎない
* Serviceに業務ロジックをまとめる
* RepositoryにDB操作をまとめる
* バリデーションはFormRequestに分ける

### 単体(Unit)テスト
主に自作のServiceの機能を対象にします。  
テストメソッドは記載されてる機能を中心にテストします。  

* 正常系：想定通り成功するパターン
* 異常系：例外発生時の挙動
* 条件分岐：if分岐や対象データなし
* 外部連携：外部APIやDBアクセスのモックを使ったテスト

Laravel標準機能そのものは基本的にテスト対象外とします。

### 結合(Feature)テスト
主に、Controllerから実際のリクエストとして動かしたときの全体の流れを対象にします。  
テストメソッドは、認証・認可・バリデーション・DB・画面表示などを含む処理を中心にテストします。 
※単体テストでは確認していない部分

- コントローラ記載の認証やセキュリティ系を含むもの
- FormRequestのバリデーション結果が画面遷移やエラー表示に反映されるもの
- Repositoryで独自クエリを記載しており、モックではなくDBを使って確認したいもの
- ControllerからViewへ必要なデータが渡り、画面に期待する内容が表示されるもの
- 保存・更新・削除は結果確認のみとする

Laravel標準機能そのものは基本的にテスト対象外とします。　　

※今回ログイン機能は認証用Serviceを作成していないため、UnitテストではなくFeatureテストで確認する

---

## 20. Git運用

ブランチ名は作業内容がわかるようにします。

例：

```text
feature/user-create
feature/user-update
feature/user-delete
hotfix/user-validation
doc/coding-rule
test/user-service
```

基本方針：

* main：本番環境
* develop：開発環境。mainからできたものでfeatureが集約される
* feature：新機能。developから作られる
* hotfix：軽微な修正。developから作られる
* doc：ドキュメント修正
* test：個人用のテストなどを行う。マージはしない  

※GitHub flow(簡易)とGit flow(複雑)でPJによって良しなに改造して使うことが多かった

### リリース前の開発初期段階の流れ
ただリリース前は本番環境もないので、簡単な流れでも使うことも多いです。  

```
1. mainからpull  
↓
2. ブランチ作成  
↓
3. プルリク依頼  
↓
4. レビュー、マージ  
↓
5. 1に戻り改修  
```
---

## 21. プルリク(レビュー依頼)のフォーマット

```md

# 概要
[チケット(案件)番号] に対応しました。
[機能の概要を1-2文で簡潔に説明]

# 改修内容
- [主な変更点1]
- [主な変更点2]
- [主な変更点3]

## 動作確認方法
1. [確認手順1]
2. [確認手順2]
3. [確認手順3]

※ 必要に応じてスクリーンショット or 動画も検討  
※ レビュワーの負担にならないようにする

## レビューポイント
- [特に確認してほしい点1]
- [特に確認してほしい点2]

# その他補足

```

### コードレビューについて
コードに慣れてない場合、とりあえず以下の方針でレビューの時もあります。  

- 動くか確認
- コードについてはわかる範囲で確認

---

## 参考リンク

### PHP標準規約

#### laravel-best-practices
https://github.com/alexeymezenin/laravel-best-practices/blob/master/japanese.md

#### PSR-12（コーディングスタイル）
https://www.php-fig.org/psr/psr-12/

#### PSR-4（Autoload）
https://www.php-fig.org/psr/psr-4/

#### PSR-2（非推奨）
https://www.php-fig.org/psr/psr-2/

---

### Laravel公式

#### Laravel 13 Documentation
https://laravel.com/docs/13.x

#### Laravel Pint
https://laravel.com/docs/13.x/pint

---

### Git

#### GitHub Flow
https://docs.github.com/en/get-started/using-github/github-flow

#### Git
https://git-scm.com/doc