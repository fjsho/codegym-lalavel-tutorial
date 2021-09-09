## テーブル名

- 論理名: 課題
- 物理名: tasks

## テーブル定義

| 論理(項目)名          | 物理(カラム)名    | データ型         | Null許可 | Key | デフォルト値 | その他設定     | 備考                |
|-----------------------|-------------------|------------------|:--------:|:---:|--------------|----------------|---------------------|
| ID                    | id                | int(11)          | NO       | PK  | NULL         | auto_increment |                     |
| プロジェクトID        | project_id        | int(11)          | NO       | FK  | NULL         |                | project.id          |
| 名前                  | name              | varchar(255)     | NO       |     | NULL         |                |                     |
| 課題種別ID            | task_kind_id      | int(11)            NO       | FK  | NULL         |                | task_kinds.id       |
| 詳細                  | detail            | text             | YES      |     | NULL         |                |                     |
| 課題状態ID            | task_status_id    | int(11)          | NO       | FK  | NULL         |                | task_statuses.id    |
| 登録ユーザーID        | created_user_id   | int(11)          | NO       | FK  | NULL         |                | users.id            |
| 更新ユーザーID        | updated_user_id   | int(11)          | YES      | FK  | NULL         |                | users.id            |
| 担当ユーザーID        | assigner_id       | int(11)          | YES      | FK  | NULL         |                | users.id            |
| カテゴリーID          | task_category_id  | int(11)          | YES      | FK  | NULL         |                | task_categories.id  |
| 期限日                | due_date          | date             | YES      |     | NULL         |                |                     |
| 完了理由ID            | task_resolution_id| int(11)          | YES      | FK  | NULL         |                | task_resolutions.id |
| 作成日時              | created_at        | timestamp        | YES      |     | NULL         |                |                     |
| 更新日時              | updated_at        | timestamp        | YES      |     | NULL         |                |                     |
| 削除日時              | deleted_at        | timestamp        | YES      |     | NULL         |                |                     |

## 備考

- 作成・更新日時はアプリケーションで設定する