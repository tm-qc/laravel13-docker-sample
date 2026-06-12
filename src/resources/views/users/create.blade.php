@extends('layouts.app')

@section('title', 'ユーザー登録')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endsection

@section('content')
    <div class="form-card">
        <h1 class="page-title">
            ユーザー登録
        </h1>

        <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data" class="user-form">
            @csrf

            <div class="form-group">
                <label for="icon_image" class="form-label">アイコン画像</label>
                <input type="file" id="icon_image" name="icon_image" class="form-control">
            </div>

            <div class="form-group">
                <label for="name" class="form-label">名前</label>
                <input type="text" id="name" name="name" class="form-control">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">メールアドレス</label>
                <input type="email" id="email" name="email" class="form-control">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">パスワード</label>
                <input type="password" id="password" name="password" class="form-control">
            </div>

            <div class="form-group">
                <label for="profile" class="form-label">プロフィール</label>
                <textarea id="profile" name="profile" rows="5" class="form-control"></textarea>
            </div>

            <button type="submit" class="button-primary">
                登録
            </button>
        </form>
    </div>
@endsection

{{-- 共通テンプレートに渡すjs --}}
@section('js')
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
@endsection
