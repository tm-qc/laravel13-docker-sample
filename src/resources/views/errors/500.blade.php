{{--
    500 Internal Server Error

    アプリケーション側で予期しないエラーが発生した場合の画面。

    本番環境では、
    ・例外メッセージ
    ・ファイルパス
    ・SQL
    ・スタックトレース
    などは絶対に表示しない。
--}}
@extends('errors.layout')

@section('code', '500')
@section('title', 'エラーが発生しました')
@section('message', '申し訳ありません。処理中に問題が発生しました。時間をおいて再度お試しください。')
