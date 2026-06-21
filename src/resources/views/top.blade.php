{{-- 共通テンプレート読み込み --}}
@extends('layouts.app')

{{-- ページタイトル --}}
@section('title', 'TOP')

{{-- ページ専用CSS --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('css/top.css') }}">
@endsection

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
        </div>
    </div>
@endsection

{{-- ページ専用JS --}}
@section('js')
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
@endsection
