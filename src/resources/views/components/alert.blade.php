{{-- resources/views/components/alert.blade.php --}}

{{-- 成功メッセージ --}}
@if (session('success'))
    <div class="alert-success">
        <span>✓</span>
        {{ session('success') }}
    </div>
@endif

{{-- 失敗メッセージ --}}
@if (session('error'))
    <div class="alert-error">
        <span>×</span>
        {{ session('error') }}
    </div>
@endif
