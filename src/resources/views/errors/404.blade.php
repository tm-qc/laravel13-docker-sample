{{--
    404 Not Found

    存在しないURLへアクセスした場合の画面。
    例：
    /not-found-page
--}}
@extends('errors.layout')

@section('code', '404')
@section('title', 'ページが見つかりません')
@section('message', 'お探しのページは削除されたか、URLが変更された可能性があります。')
