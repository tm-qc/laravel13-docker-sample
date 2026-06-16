{{--
    共通ページネーションコンポーネント

    $items:
    Controllerから paginate() で取得したデータを受け取る

    例:
    $users = User::latest()->paginate(10);

    Blade側:
    <x-pagination :items="$users" />
--}}

{{-- ページが2ページ以上ある場合だけページネーションを表示する --}}
@if ($items->lastPage() > 1)
    <div class="pagination">
        {{-- 前のページがある場合だけ「前へ」をリンクにする --}}
        @if ($items->previousPageUrl())
            <a href="{{ $items->previousPageUrl() }}" class="pagination__link">
                前へ
            </a>
        @else
            <span class="pagination__disabled">
                前へ
            </span>
        @endif

        {{-- 現在のページ番号と最後のページ番号を表示する --}}
        <span class="pagination__current">
            {{ $items->currentPage() }} / {{ $items->lastPage() }} ページ
        </span>

        {{-- 次のページがある場合だけ「次へ」をリンクにする --}}
        @if ($items->nextPageUrl())
            <a href="{{ $items->nextPageUrl() }}" class="pagination__link">
                次へ
            </a>
        @else
            <span class="pagination__disabled">
                次へ
            </span>
        @endif
    </div>
@endif
