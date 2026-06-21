{{-- 共通ヘッダー --}}
<header class="site-header">
    <div class="site-header-inner">
        <a href="{{ route('top') }}" class="site-logo">
            Laravel13 サンプル
        </a>

        {{-- ログイン中だけ表示 --}}
        @auth
            @php
                $loginUser = auth()->user();
            @endphp
            {{-- 不要だと思うが、一般で入ったときにポリシーが聞いてるか確認になるので、いったんこのまま --}}
            <nav class="site-nav">
                <a href="{{ route('users.index') }}">ユーザー一覧</a>
                <a href="{{ route('users.create') }}">ユーザー登録</a>
            </nav>

            {{-- ログインユーザー情報 --}}
            <div class="login-user-info">
                <span class="login-user-label">【ログイン中】：</span>

                {{--
                    ユーザー名を表示する
                    {{ }} はHTMLエスケープされるため、XSS対策として安全に表示できる。
                --}}
                <span class="login-user-name">{{ $loginUser->name }}</span>

                <span class="login-user-label">【ロール】：</span>
                {{-- ロール名 --}}
                {{-- ほぼないが念のため 'ロール未設定' --}}
                <span class="login-user-role">
                    {{ $loginUser->role?->name ?? 'ロール未設定' }}
                </span>
            </div>

            <form action="{{ route('logout') }}" method="post" class="logout-form">
                @csrf

                <button type="submit" class="logout-button">
                    ログアウト
                </button>
            </form>
        @endauth

        @guest
            {{-- 未ログイン時、かつログイン画面以外で表示 --}}
            @unless (request()->routeIs('login'))
                <a href="{{ route('login') }}">ログイン</a>
            @endunless
        @endguest
    </div>
</header>
