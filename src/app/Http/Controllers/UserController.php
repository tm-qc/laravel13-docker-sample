<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * UserServiceをControllerで使えるようにする
     *
     * Laravelのサービスコンテナが自動でUserServiceを生成してくれるため、
     * new UserService() は不要。
     */
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * 一覧画面を表示する
     */
    public function index(): View
    {
        // ユーザ一覧取得はServiceへ任せる
        $users = $this->userService->getPaginatedUsers(3);

        // users/index.blade.php にユーザ一覧データを渡して表示する
        return view('users.index', compact('users'));
    }

    /**
     * 登録画面を表示する
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * 新規登録する
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        try {
            // フォームリクエストからバリデーション済みデータを取得
            $validated = $request->validated();

            // 登録処理はServiceへ任せる
            $this->userService->createUser(
                $validated,
                $request->file('icon_image')
            );

            // 登録画面へ戻る
            return redirect()
                ->route('users.create')
                ->with('success', 'ユーザーを登録しました。');
        } catch (\Throwable $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'ユーザー登録に失敗しました。');
        }
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
