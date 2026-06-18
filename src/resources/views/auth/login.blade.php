{{-- 共通レイアウトを読み込む --}}
@extends('layouts.app')

{{-- ページタイトルを設定する --}}
@section('title', 'ログイン')

{{-- メインコンテンツ --}}
@section('content')
    <div class="form-card">
        <h1 class="page-title">ログイン</h1>

        <form action="{{ route('login.store') }}" method="post">
            @csrf

            <div class="form-group">
                <label for="email">メールアドレス</label>

                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control">

                {{-- メールアドレスのバリデーションエラーを表示する --}}
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>

                <input type="password" id="password" name="password" class="form-control">

                {{-- パスワードのバリデーションエラーを表示する --}}
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions form-actions--center">
                <button type="submit" class="button-primary">
                    ログイン
                </button>
            </div>
        </form>
    </div>
@endsection
