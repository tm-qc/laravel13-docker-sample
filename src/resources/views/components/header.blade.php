{{-- 共通ヘッダー --}}
<header class="site-header">
    <div class="site-header-inner">
        <a href="{{ route('top') }}" class="site-logo">
            Laravel13 サンプル
        </a>

        {{-- ログイン中だけ表示 --}}
        @auth
            <nav class="site-nav">
                <a href="{{ route('users.index') }}">ユーザー一覧</a>
                <a href="{{ route('users.create') }}">ユーザー登録</a>
            </nav>

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
