| 画面名            | URL                                                      | メソッド |
|-------------------|----------------------------------------------------------|----------|
| サイトトップ      | /                                                        | GET      |
| アカウント作成    | /register                                                | GET      |
|                   |                                                          | POST     |
| ログイン          | /login                                                   | GET      |
|                   |                                                          | POST     |
| プロジェクト作成  | /projects/create                                         | GET      |
|                   |                                                          | POST     |
| プロジェクト一覧  | /projects                                                | GET      |
| プロジェクト詳細  | /projects/{project_key}                                  | GET      |
|                   |                                                          | POST     |
| 課題作成          | /projects/{project_key}/tasks/create                     | GET      |
|                   |                                                          | POST     |
| 課題一覧          | /projects/{project_key}/tasks                            | GET      |
| 課題詳細          | /projects/{project_key}/tasks/{task_key}                 | GET      |
|                   |                                                          | POST     |
| 課題コメント      | /projects/{project_key}/tasks/{task_key}/comments/create | POST     |