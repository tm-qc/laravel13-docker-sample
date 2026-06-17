{{--
    4xx系の共通エラー画面

    403、419など、個別ファイルを作っていない4xx系エラーの受け皿。
    ただしLaravelでは、404は専用ファイルを作るのが基本。
--}}
@extends('errors.layout')

@section('code', '4xx')
@section('title', 'リクエストを処理できません')
@section('message', 'アクセス権限がない、またはリクエストが正しくない可能性があります。')
