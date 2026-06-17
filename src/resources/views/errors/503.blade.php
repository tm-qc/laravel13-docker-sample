{{--
    503 Service Unavailable

    メンテナンス中や、一時的にサービスが利用できない場合の画面。
    php artisan down 実行時にも使われる。
--}}
@extends('errors.layout')

@section('code', '503')
@section('title', 'ただいまメンテナンス中です')
@section('message', '現在サービスを一時停止しています。しばらく時間をおいてから再度アクセスしてください。')
