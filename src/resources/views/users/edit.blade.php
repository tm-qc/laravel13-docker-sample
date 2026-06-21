{{-- 共通テンプレートを読み込む --}}
@extends('layouts.app')

{{-- ページタイトルを設定する --}}
@section('title', 'ユーザ編集')

{{-- この画面専用のCSSを読み込む --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endsection

{{-- メインコンテンツ --}}
@section('content')
    <div class="users-page">
        <div class="users-page__header">
            <div>
                <h1 class="users-page__title">ユーザ編集</h1>
                <p class="users-page__lead">
                    登録済みのユーザ情報を変更できます。
                </p>
            </div>

            {{-- ユーザ一覧画面へ戻るリンク --}}
            <a href="{{ route('users.index') }}" class="users-page__create-link">
                ユーザ一覧へ戻る
            </a>
        </div>
        {{--
            ユーザ更新フォーム

            method="POST":
            HTMLフォームは PUT/PATCH を直接送信できないため POST を指定する。

            @method('PUT'):
            Laravel側で PUT リクエストとして扱う。

            enctype="multipart/form-data":
            画像アップロードを行うため必要。
        --}}
        <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="user-form">
            @csrf
            @method('PUT')

            {{-- 現在のアイコン画像 --}}
            <div class="form-group">
                <label class="form-label">現在のアイコン画像</label>

                <div class="user-icon">
                    <img src="{{ $user->icon_image_url }}" alt="{{ $user->name }}のアイコン画像" class="user-icon__image">
                </div>
            </div>

            {{-- アイコン画像 --}}
            <div class="form-group">
                <label for="icon_image" class="form-label">アイコン画像</label>

                <input type="file" id="icon_image" name="icon_image" class="form-control"
                    accept="image/jpeg,image/png,image/webp">

                @error('icon_image')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 名前 --}}
            <div class="form-group">
                <label for="name" class="form-label">
                    名前 <span class="required">*</span>
                </label>

                <input type="text" id="name" name="name" class="form-control"
                    value="{{ old('name', $user->name) }}">

                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- ロール --}}
            {{-- TODO:現状管理者のみ表示。最終的に一般ユーザは表示のみで編集不可がいいかも --}}
            @can('create', \App\Models\User::class)
                <div class="form-group">
                    <label for="role_id" class="form-label">ロール</label>

                    <select id="role_id" name="role_id" class="form-control @error('role_id') is-invalid @enderror">
                        <option value="">選択してください</option>

                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) old('role_id', $user->role_id) === (string) $role->id)>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('role_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            @endcan

            {{-- メールアドレス --}}
            <div class="form-group">
                <label for="email" class="form-label">
                    メールアドレス <span class="required">*</span>
                </label>

                <input type="email" id="email" name="email" class="form-control"
                    value="{{ old('email', $user->email) }}">

                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- パスワード --}}
            <div class="form-group">
                <label for="password" class="form-label">パスワード</label>

                <input type="password" id="password" name="password" class="form-control" autocomplete="new-password">

                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- パスワード確認 --}}
            <div class="form-group">
                <label for="password_confirmation" class="form-label">パスワード確認</label>

                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                    autocomplete="new-password">

                @error('password_confirmation')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- プロフィール --}}
            <div class="form-group">
                <label for="profile" class="form-label">プロフィール</label>

                <textarea id="profile" name="profile" class="form-control" rows="5">{{ old('profile', $user->profile) }}</textarea>

                @error('profile')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 更新ボタン --}}
            <div class="form-group">
                <button type="submit" class="button-primary">
                    更新する
                </button>
            </div>
        </form>

        {{-- 論理削除フォーム --}}
        <form action="{{ route('users.soft-destroy', $user) }}" method="POST" class="delete-form">
            @csrf
            @method('DELETE')

            <button type="submit" class="button-danger" onclick="return confirm('このユーザーを論理削除しますか？\nDBにはデータが残ります。');">
                論理削除する
            </button>
        </form>


        {{--
            ユーザ物理削除フォーム
            ※サンプルなので論理と物理双方の削除機能を作ってます

            物理削除はDBから完全に削除する処理。
            復元できないため、ボタン名と確認メッセージで明確にする。

            method="POST":
            HTMLフォームは GET / POST しか直接送信できないためPOSTで設定

            @method('DELETE'):
            Laravel側に「このPOSTはDELETEとして扱ってください」と指示
        --}}
        <form action="{{ route('users.force-destroy', $user) }}" method="POST" class="delete-form"
            onsubmit="return confirm('本当にこのユーザーを物理削除しますか？この操作は元に戻せません。');">
            @csrf
            @method('DELETE')

            <button type="submit" class="button-danger">
                物理削除する
            </button>
        </form>
    </div>
@endsection
