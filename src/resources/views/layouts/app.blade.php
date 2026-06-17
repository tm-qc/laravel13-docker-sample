{{-- ツール全体の共通テンプレート --}}
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>@yield('title') | Laravel13 サンプル</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @yield('css')
</head>

<body>
    @include('components.header')

    <main class="page">
        {{-- 成功・失敗メッセージを共通表示する --}}
        @include('components.alert')

        {{-- 各画面の内容 --}}
        @yield('content')
    </main>

    @include('components.footer')

    @yield('js')
</body>

</html>
