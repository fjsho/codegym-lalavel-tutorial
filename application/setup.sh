#!/usr/bin/env bash

# 環境変数ファイルの作成
cp .env.example .env

# パッケージインストール
docker run --rm \
  -v $(pwd):/opt \
  -w /opt \
  laravelsail/php80-composer:latest \
  bash -c "composer install"

# Dockerコンテナの起動
./vendor/bin/sail up -d

# LaravelのAPP_KEY生成
./vendor/bin/sail artisan key:generate

# マイグレーション
./vendor/bin/sail artisan migrate:fresh --seed

# セットアップ
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev