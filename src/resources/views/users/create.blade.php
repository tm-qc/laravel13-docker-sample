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

        @if (session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data" class="user-form">
            @csrf

            <div class="form-group">
                <label for="icon_image" class="form-label">アイコン画像</label>
                {{-- oldは画像には使わない --}}
                <input type="file" id="icon_image" name="icon_image"
                    class="form-control @error('icon_image') is-invalid @enderror">

                @error('icon_image')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="name" class="form-label">名前</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}">

                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">メールアドレス</label>
                <input type="email" id="email" name="email"
                    class="form-control  @error('password') is-invalid @enderror" value="{{ old('email') }}">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">パスワード</label>
                <input type="password" id="password" name="password"
                    class="form-control  @error('password') is-invalid @enderror">
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">パスワード確認</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
            </div>

            <div class="form-group">
                <label for="profile" class="form-label">プロフィール</label>
                <textarea id="profile" name="profile" rows="5" class="form-control  @error('profile') is-invalid @enderror">{{ old('profile') }}</textarea>
                @error('profile')
                    <p class="form-error">{{ $message }}</p>
                @enderror
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
