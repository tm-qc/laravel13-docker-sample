{{-- 共通ヘッダー --}}
<header class="site-header">
    <div class="site-header-inner">
        <a href="{{ route('top') }}" class="site-logo">
            Laravel13 サンプル
        </a>

        <nav class="site-nav">
            <a href="{{ route('users.index') }}">ユーザー一覧</a>
            <a href="{{ route('users.create') }}">ユーザー登録</a>
        </nav>
    </div>
</header>
