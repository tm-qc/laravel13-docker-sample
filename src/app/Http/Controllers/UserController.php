<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
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
                ->with('success', __('messages.users.create.success'));
        } catch (\Throwable $e) {

            return redirect()
                ->back()
                // withInput() はリダイレクト後に入力値を復元するための Laravel 標準の仕組み
                // パスワードまで保持する必要はないので、実務では except() で除外しておく
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('error', __('messages.users.create.error'));
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
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * 更新する
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        try {
            // フォームリクエストからバリデーション済みデータを取得
            $validated = $request->validated();

            // 更新処理はServiceへ任せる
            $this->userService->updateUser(
                $user,
                $validated,
                $request->file('icon_image')
            );

            // 編集画面へ戻る
            return redirect()
                ->route('users.edit', $user)
                ->with('success', __('messages.users.update.success'));
        } catch (\Throwable $e) {

            return redirect()
                ->back()
                // パスワードは入力値として復元しない
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('error', __('messages.users.update.error'));
        }
    }

    /**
     * 削除する
     */
    public function destroy(string $id)
    {
        //
    }
}
