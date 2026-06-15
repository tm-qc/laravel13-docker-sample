<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 一覧画面を表示する
     */
    public function index()
    {
        //
    }

    /**
     * 登録画面を表示する
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * 新規登録する
     */
    public function store(UserStoreRequest $request)
    {
        // フォームリクエストからバリデーション済みデータを取得
        $validated = $request->validated();

        //
    }

    /**
     * 詳細画面を表示する
     */
    public function show(string $id)
    {
        //
    }

    /**
     * 編集画面を表示する
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * 更新する
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * 削除する
     */
    public function destroy(string $id)
    {
        //
    }
}
