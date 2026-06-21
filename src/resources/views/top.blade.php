{{-- 共通テンプレート読み込み --}}
@extends('layouts.app')

{{-- ページタイトル --}}
@section('title', 'TOP')

{{-- ページ専用CSS --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('css/top.css') }}">
@endsection

{{-- ログイン中ユーザーを取得する --}}
@php
    $loginUser = request()->user();
@endphp

{{-- 個別メインコンテンツ --}}
@section('content')
    <div class="menu-card">
        <h1 class="page-title">
            Laravel13 サンプル
        </h1>

        <p class="page-description">
            ユーザー管理システム
        </p>

        <div class="top-menu-list">
            {{-- src/app/Policies/UserPolicy.php のポリシーで表示制御 --}}
            @can('viewAny', \App\Models\User::class)
                <a href="{{ route('users.index') }}" class="top-menu-item">
                    <h2>ユーザー一覧</h2>
                    <p>登録済みユーザーを確認します</p>
                </a>
            @endcan

            {{-- src/app/Policies/UserPolicy.php のポリシーで表示制御 --}}
            @can('create', \App\Models\User::class)
                <a href="{{ route('users.create') }}" class="top-menu-item">
                    <h2>ユーザー登録</h2>
                    <p>新しいユーザーを登録します</p>
                </a>
            @endcan

            {{--
                一般ユーザー用の編集導線

                管理者:
                - ユーザー一覧から対象ユーザーを編集するため、TOPには表示しない

                一般ユーザー:
                - ユーザー一覧を見られないため、TOPから自分の編集画面へ遷移できるようにする

                注意:
                - これは画面上の表示制御
                - 実際の更新可否は UserPolicy@update と UserUpdateRequest@authorize で判定する
            --}}
            @cannot('viewAny', \App\Models\User::class)
                @can('update', $loginUser)
                    <a href="{{ route('users.edit', $loginUser) }}" class="top-menu-item">
                        <h2>ユーザー編集</h2>
                        <p>自分のユーザデータを編集できます。</p>
                    </a>
                @endcan
            @endcannot
        </div>
    </div>
@endsection

{{-- ページ専用JS --}}
@section('js')
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
@endsection
