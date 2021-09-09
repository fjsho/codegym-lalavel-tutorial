## テーブル名

- 論理名: ユーザー
- 物理名: users

## テーブル定義

| 論理(項目)名         | 物理(カラム)名   | データ型         | Null許可 | Key | デフォルト値 | その他設定     | 備考        |
|----------------------|------------------|------------------|:--------:|:---:|--------------|----------------|-------------|
| ID                   | id               | int(11)          | NO       | PK  | NULL         | auto_increment |             |
| 名前                 | name             | varchar(255)     | NO       |     | NULL         |                |             |
| Eメールアドレス      | email            | varchar(255)     | NO       |     | NULL         | UNIQUE         |             |
| パスワード           | password         | varchar(60)      | NO       |     | NULL         |                |             |
| 持続ログイントークン | remember_token   | varchar(100)     | YES      |     | NULL         |                |             |
| 作成日時             | created_at       | timestamp        | YES      |     | NULL         |                |             |
| 更新日時             | updated_at       | timestamp        | YES      |     | NULL         |                |             |
| 削除日時             | deleted_at       | timestamp        | YES      |     | NULL         |                |             |

## 備考

- Laravel Authで自動生成されるテーブルに準拠