<?php

namespace Tests\Feature\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 結合(Feature)テストサンプル
 *
 * 作成方針
 *
 * 主に、Controllerから実際のリクエストとして動かしたときの全体の流れを対象にします。
 * テストメソッドは、認証・認可・バリデーション・DB・画面表示などを含む処理を中心にテストします。
 * ※単体テストでは確認していない部分
 *
 * - コントローラ記載の認証やセキュリティ系を含むもの
 * - FormRequestのバリデーション結果が画面遷移やエラー表示に反映されるもの
 * - Repositoryで独自クエリを記載しており、モックではなくDBを使って確認したいもの
 * - ControllerからViewへ必要なデータが渡り、画面に期待する内容が表示されるもの
 * - 保存・更新・削除は結果確認のみとする
 *
 * Laravel標準機能そのものは基本的にテスト対象外とします。　　
 *
 * ※今回ログイン機能は認証用Serviceを作成していないため、UnitテストではなくFeatureテストで確認する
 */


/**
 * ユーザー一覧表示
 *
 * Featureテスト：Controller → Service → Repository の認証・認可・画面表示のテスト
 *               TestCase($this)クラスの機能を使いテスト
 */
class UserIndexTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;
    private Role $generalRole;

    /**
     * 各テスト実行前の共通準備
     *
     * RefreshDatabaseにより、各テストごとにDBは初期化される。
     * そのため、各テストで必要になるロールを毎回作成する。
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 管理者ロールを作成
        $this->adminRole = Role::create([
            'code' => Role::CODE_ADMIN,
            'name' => '管理者(テスト)',
            'is_system' => true,
            'is_active' => true,
        ]);

        // 一般ロールを作成
        $this->generalRole = Role::create([
            'code' => Role::CODE_GENERAL,
            'name' => '一般(テスト)',
            'is_system' => true,
            'is_active' => true,
        ]);
    }

    /**
     * 未ログインの場合、ユーザー一覧画面へアクセスできずログイン画面へリダイレクトされること
     */
    public function test_guest_cannot_view_user_index(): void
    {
        // 未ログイン状態でユーザー一覧画面へアクセスする
        $response = $this->get(route('users.index'));

        // authミドルウェアによりログイン画面へリダイレクトされることを確認する
        $response->assertRedirect(route('login'));
    }

    /**
     * 一般ユーザーの場合、ユーザー一覧画面へアクセスできず403になること
     */
    public function test_general_user_cannot_view_user_index(): void
    {

        // 一般ユーザーを作成する
        $generalUser = User::factory()->create([
            'role_id' => $this->generalRole->id,
        ]);

        // 一般ユーザーとしてログインした状態でユーザー一覧画面へアクセスする
        $response = $this
            ->actingAs($generalUser)
            ->get(route('users.index'));

        // UserPolicy::viewAny() により403になることを確認する
        $response->assertForbidden();
    }

    /**
     * 管理者の場合、ユーザー一覧画面を表示できること
     */
    public function test_admin_user_can_view_user_index(): void
    {

        // ログイン用の管理者ユーザーを作成する
        $adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
        ]);

        // 一覧に表示される確認用ユーザーを作成する
        $targetUser = User::factory()->create([
            'role_id' => $this->generalRole->id,
            'name' => '一覧表示確認ユーザー',
            'email' => 'index-user@example.com',
        ]);

        // 管理者としてログインした状態でユーザー一覧画面へアクセスする
        $response = $this
            ->actingAs($adminUser)
            ->get(route('users.index'));

        // 正常に画面表示できることを確認する
        $response->assertOk();

        // users.index のViewが返されていることを確認する
        $response->assertViewIs('users.index');

        // Controllerから users 変数がViewへ渡されていることを確認する
        $response->assertViewHas('users');

        // 一覧画面に確認用ユーザーの情報が表示されていることを確認する
        // assertSeeTextでhtmlではなく文字で確認する
        $response->assertSeeText($targetUser->name);
        $response->assertSeeText($targetUser->email);
    }
}
