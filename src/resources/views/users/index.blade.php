{{-- 共通テンプレートを読み込む --}}
@extends('layouts.app')

{{-- ページタイトルを設定する --}}
@section('title', 'ユーザ一覧')

{{-- この画面専用のCSSを読み込む --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endsection

{{-- メインコンテンツ --}}
@section('content')
    <div class="users-page">
        <div class="users-page__header">
            <div>
                <h1 class="users-page__title">ユーザ一覧</h1>
                <p class="users-page__lead">
                    登録されているユーザ情報を確認できます。
                </p>
            </div>

            {{-- ユーザ登録画面へのリンク --}}
            <a href="{{ route('users.create') }}" class="users-page__create-link">
                ユーザ登録
            </a>
        </div>

        {{-- ユーザが1件以上ある場合 --}}
        @if ($users->count() > 0)
            <div class="users-list">
                @foreach ($users as $user)
                    <article class="user-card">
                        <div class="user-card__image-wrap">
                            {{--
                                アイコン画像を表示する

                                icon_image_path が空の場合:
                                public/images/users/default-user-icon.png を表示する

                                icon_image_path がある場合:
                                storage/app/public 配下の画像を /storage 経由で表示する

                                ※ public/storage のシンボリックリンクが必要
                                php artisan storage:link
                            --}}
                            <img src="{{ $user->icon_image_url }}" alt="{{ $user->name }}のアイコン画像"
                                class="user-card__image">
                        </div>

                        <div class="user-card__body">
                            <h2 class="user-card__name">
                                {{ $user->name }}
                            </h2>

                            <dl class="user-card__info">
                                <div class="user-card__info-row">
                                    <dt>メールアドレス</dt>
                                    <dd>{{ $user->email }}</dd>
                                </div>

                                <div class="user-card__info-row">
                                    <dt>パスワード</dt>
                                    <dd class="user-card__password">
                                        セキュリティ上非表示です。
                                    </dd>
                                </div>

                                <div class="user-card__info-row">
                                    <dt>プロフィール</dt>
                                    <dd>
                                        {{ $user->profile !== '' ? $user->profile : '未入力' }}
                                    </dd>
                                </div>
                                <div class="user-card__info-row">
                                    <dt>登録日</dt>
                                    <dd>
                                        {{ $user->created_at->format('Y年m月d日 H:i') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- ページネーション --}}
            {{-- Tailwind CSS前提なら自作しないで ↓ でいける --}}
            {{-- <div class="users-pagination">
                {{ $users->links() }}
            </div> --}}

            {{-- 自作コンポーネントのページネーションを表示する --}}
            <x-pagination :items="$users" />
        @else
            {{-- ユーザが0件の場合 --}}
            <div class="users-empty">
                <p>登録されているユーザはまだいません。</p>

                <a href="{{ route('users.create') }}" class="users-empty__link">
                    最初のユーザを登録する
                </a>
            </div>
        @endif
    </div>
@endsection
