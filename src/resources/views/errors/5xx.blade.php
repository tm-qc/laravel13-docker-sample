{{--
    5xx系の共通エラー画面

    500、503以外のサーバー系エラーの受け皿。
    ただしLaravelでは、500と503は専用ファイルを作るのが基本。
--}}
@extends('errors.layout')

@section('code', '5xx')
@section('title', 'サーバーエラーが発生しました')
@section('message', '申し訳ありません。サーバー側で問題が発生しました。時間をおいて再度お試しください。')
