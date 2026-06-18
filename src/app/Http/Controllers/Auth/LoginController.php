<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * 認証コントローラー
 *
 * メソッド名はRESTfulのCRUD(storeやdestroy)ではなく、
 * Laravel公式ドキュメントの手動認証例に準じて authenticate と logout にする。
 *
 * 認証処理はLaravel標準のAuth機能を呼び出す処理のため、
 * 今回はService、Repositoryまでは作成しない。
 *
 * @see https://laravel.com/docs/13.x/authentication#authenticating-users
*/
class LoginController extends Controller
{
    /**
     * ログイン画面を表示する
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * ログイン認証処理を実行する
     */
    public function authenticate(LoginRequest $request): RedirectResponse
    {

        $credentials = $request->validated();

        /*
         * Auth::attempt():Laravel標準の認証処理
         *
         * ※ここで Hash::make() はしない。
         * 　Laravel側が入力パスワードとDBのハッシュ値を比較してくれる。
         */
        if (Auth::attempt($credentials)) {
            /*
             * ログイン成功後はセッションIDを再生成(regenerate)する。
             * セッション固定攻撃を防ぐ
             */
            $request->session()->regenerate();

            /*
             * intended():ログイン成功後の遷移先を決める
             *
             * - セッションに「本来アクセスしたかったURL」があればそこへ遷移
             * - セッションに遷移先がない場合は、指定したページへ遷移
             */
            return redirect()
                ->intended(route('top'))
                ->with('success', 'ログインしました。');
        }

        // ログイン失敗時
        return back()
            ->withErrors([
                'email' => 'メールアドレスまたはパスワードが正しくありません。',
            ])
            ->onlyInput('email');//入力値を残すoldと同じような役割
    }

    // ログアウト処理
    public function logout(Request $request): RedirectResponse
    {
        // Laravel標準のログアウト処理
        Auth::logout();

        // セッションを無効化する
        $request->session()->invalidate();

        // CSRFトークンを再生成
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'ログアウトしました。');
    }
}
