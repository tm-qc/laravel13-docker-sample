{{--
    エラー画面専用レイアウト

    通常画面の layouts.app は使わない。
    理由：
    ・共通ヘッダーや共通処理が壊れている場合でも、エラー画面だけは表示したい
    ・本番環境では「最低限の案内」を安定して表示するのが目的
--}}
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">

    {{-- スマホでも崩れにくくするための基本設定 --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- 各エラー画面から渡されるタイトル --}}
    <title>@yield('title') | Laravel13 サンプル</title>

    {{-- エラー画面専用CSS --}}
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
</head>

<body>
    <main class="error-page">
        <section class="error-card">
            {{-- HTTPステータスコード --}}
            <p class="error-card__code">
                @yield('code')
            </p>

            {{-- ユーザー向けの見出し --}}
            <h1 class="error-card__title">
                @yield('title')
            </h1>

            {{-- ユーザー向けの説明文 --}}
            <p class="error-card__message">
                @yield('message')
            </p>

            {{--
                TOPへ戻るリンク

                route名に依存させず url('/') にしておく。
                理由：
                ・route名変更の影響を受けにくい
                ・最低限TOPへ戻せれば十分
            --}}
            <a href="{{ url('/') }}" class="error-card__button">
                TOPへ戻る
            </a>
        </section>
    </main>
</body>

</html>
