## テーブル名

- 論理名: 課題種別
- 物理名: task_kinds

## テーブル定義

| 論理(項目)名          | 物理(カラム)名    | データ型         | Null許可 | Key | デフォルト値 | その他設定     | 備考        |
|-----------------------|-------------------|------------------|:--------:|:---:|--------------|----------------|-------------|
| ID                    | id                | int(11)          | NO       | PK  | NULL         | auto_increment |             |
| 名前                  | name              | varchar(255)     | NO       |     | NULL         |                |             |
| 表示順                | display_order     | int(11)          | NO       |     | NULL         |                |             |
| 作成日時              | created_at        | timestamp        | YES      |     | NULL         |                |             |
| 更新日時              | updated_at        | timestamp        | YES      |     | NULL         |                |             |
| 削除日時              | deleted_at        | timestamp        | YES      |     | NULL         |                |             |

## 備考

- 作成・更新日時はアプリケーションで設定する