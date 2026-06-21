<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
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
     *
     * @param Request $request 利用ユーザー情報 + リクエスト値
     * @return View
     */
    public function index(Request $request): View
    {
        /**
         * 認可チェック
         *
         * $request->user():ログイン中ユーザー
         *
         * cannot('viewAny', User::class):UserPolicy の viewAny() を呼び出す。
         *
         * 今回の仕様では、ユーザー一覧を表示できるのは管理者だけ
         * 引数 User::class について：今回は表示権限の確認で、編集など特定の個別ユーザはいないので、参照するPolicy特定のためにUserクラスを渡す
         */
        if ($request->user()->cannot('viewAny', User::class)) {
            abort(403);
        }

        // ユーザ一覧取得はServiceへ任せる
        $users = $this->userService->getPaginatedUsers(3);

        // users/index.blade.php にユーザ一覧データを渡して表示する
        return view('users.index', compact('users'));
    }

    /**
     * 登録画面を表示する
     *
     * @param Request $request 利用ユーザー情報 + リクエスト値
     * @return View
     */
    public function create(Request $request): View
    {
        /**
         * 認可チェック
         *
         * 管理者のみ参照可能
        */
        if ($request->user()->cannot('create', User::class)) {
            abort(403);
        }

        return view('users.create');
    }

    /**
     * 新規登録する
     *
     * @param UserStoreRequest $request 利用ユーザー情報 + バリデーション + リクエスト値
     * @return RedirectResponse
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        /**
         * 認可チェック
         *
         * 管理者のみユーザ登録可能
         *
         * try の外に書く理由:
         * 認可NGは登録処理の失敗ではなく、アクセス権限なしとして403で止めるため。
        */
        if ($request->user()->cannot('create', User::class)) {
            abort(403);
        }

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
     *
     * @param Request $request 利用ユーザー情報 + リクエスト値
     * @param User $user 編集対象ユーザー
     *                   ルートモデルバインディングにより、URLの users/{user} から取得される。
     * @return View
     */
    public function edit(Request $request, User $user): View
    {
        /**
         * 認可チェック
         *
         * - 管理者は全ユーザーを編集できる
         * - 一般ユーザーは自分自身だけ編集できる
        */
        if ($request->user()->cannot('update', $user)) {
            abort(403);
        }

        return view('users.edit', compact('user'));
    }

    /**
     * 更新する
     *
     * @param UserUpdateRequest $request 利用ユーザー情報 + バリデーション + リクエスト値
     * @param User $user 編集対象ユーザー
     *                   ルートモデルバインディングにより、URLの users/{user} から取得される。
     * @return RedirectResponse
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        /**
         * 認可チェック
         *
         * - 管理者は全ユーザーを更新できる
         * - 一般ユーザーは自分自身だけ更新できる
         *
         * try の外に書く理由:認可NGは更新処理の失敗ではなく、アクセス権限なしとして403で止めるため。
         */
        if ($request->user()->cannot('update', $user)) {
            abort(403);
        }

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
    // public function destroy(User $user): RedirectResponse
    // {
    // }

    /**
     * ユーザーを論理削除する
     *
     * @param Request $request 利用ユーザー情報 + リクエスト値
     * @param User $user 編集対象ユーザー
     *                   ルートモデルバインディングにより、URLの users/{user} から取得される。
     * @return RedirectResponse
     */
    public function softDestroy(Request $request, User $user): RedirectResponse
    {
        /**
         * 認可チェック
         *
         * - 管理者だけユーザー削除できる
         * - 一般ユーザーは削除できない
         *
         * SoftDeleteを使っているため、
         * ここでの delete は論理削除の認可として扱う。
         *
         * try の外に書く理由:認可NGは削除処理の失敗ではなく、アクセス権限なしとして403で止めるため。
         */
        if ($request->user()->cannot('delete', $user)) {
            abort(403);
        }

        try {
            // Serviceに論理削除処理を依頼する。
            $this->userService->softDeleteUser($user);

            /*
             * 論理削除後は一覧画面へ戻す。
             * 一覧ではSoftDeletesにより、削除済みユーザーは自動的に表示されない。
             */
            return redirect()
                ->route('users.index')
                ->with('success', 'ユーザーを論理削除しました。');

        } catch (\Throwable $e) {

            return redirect()
                ->route('users.edit', $user)
                ->with('error', 'ユーザーの論理削除に失敗しました。');
        }
    }

    /**
     * 物理削除する
     *
     * @param Request $request 利用ユーザー情報 + リクエスト値
     * @param User $user 編集対象ユーザー
     *                   ルートモデルバインディングにより、URLの users/{user} から取得される。
     * @return RedirectResponse
     */
    public function forceDestroy(Request $request, User $user): RedirectResponse
    {

        /**
         * 認可チェック
         *
         * - 管理者だけユーザー削除できる
         * - 一般ユーザーは削除できない
         *
         * SoftDeleteを使っているため、
         * forceDelete はDBから完全に削除する物理削除の認可として扱う。
         *
         * try の外に書く理由：認可NGは削除処理の失敗ではなく、アクセス権限なしとして403で止めるため。
        */
        if ($request->user()->cannot('forceDelete', $user)) {
            abort(403);
        }

        try {
            $this->userService->forceDeleteUser($user);

            return redirect()
                ->route('users.index')
                ->with('success', 'ユーザを物理削除しました。');

        } catch (\Throwable $e) {

            return redirect()
                ->route('users.edit', $user)
                ->with('error', 'ユーザの物理削除に失敗しました。');
        }
    }
}
