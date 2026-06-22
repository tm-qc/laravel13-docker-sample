# ==========================================
# PHP 8.5 FPM(PHPのバージョンイメージを指定)
# ==========================================
#
# PHP8.5 + PHP-FPM がインストール済み
# Nginxと組み合わせて使う
#
FROM php:8.5.6-fpm



# ==========================================
# Laravelでよく使うツール
# ==========================================
#
# git
#   Git操作で利用
#
# unzip
#   ZIP解凍コマンド
#
# zip
#   ZIP圧縮コマンド
#
# curl
#   URLアクセス確認などで利用
#
# libzip-dev
#   PHPのzip拡張をインストールするために必要
#
# libonig-dev
#   PHPのmbstring拡張をインストールするために必要
#
# libpng-dev / libjpeg-dev / libwebp-dev / libfreetype6-dev
#   PHPのgd拡張をインストールするために必要
#   画像アップロードのテストで UploadedFile::fake()->image() を使う場合にも必要
#
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev


# ==========================================
# PHP拡張追加
# ==========================================
#
# pdo_mysql
#   Laravel→MySQL接続
#
# zip
#   PHPでZIPファイルを扱うために必要
#
# mbstring
#   Laravelでマルチバイト文字を扱うために必要
#
# bcmath
#   小数計算を正確に行うために必要
#   金額計算などで利用
#
# gd
#   PHPで画像を扱うために必要
#   UploadedFile::fake()->image() でテスト用画像を生成する場合にも必要
#
RUN docker-php-ext-configure gd \
    --with-jpeg \
    --with-webp \
    --with-freetype \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    mbstring \
    bcmath \
    gd

# ==========================================
# Xdebug導入
# ==========================================
#
# Xdebug
#   VS Codeなどからブレイクポイントデバッグするために利用
#
# pecl install xdebug
#   PHP拡張としてXdebugをインストール
#
# docker-php-ext-enable xdebug
#   PHPでXdebugを有効化
#
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# ==========================================
# Composer導入
# ==========================================
#
# Composer公式イメージから
# composerコマンドだけ取得
#
COPY --from=composer:2.10.1 /usr/bin/composer /usr/bin/composer

# ==========================================
# Xdebug設定ファイルコピー
# ==========================================
#
# Xdebugの接続先、ポートなどをPHP設定として読み込む
#
COPY ./docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# ==========================================
# 作業フォルダ
# ==========================================
# ※ymlのコンテナ内の作業ディレクトリと同じにする
WORKDIR /var/www