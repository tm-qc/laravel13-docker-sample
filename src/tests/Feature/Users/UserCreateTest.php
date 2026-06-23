<?php

namespace Tests\Feature\Users;

use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use RuntimeException;
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
 * ユーザー登録 Feature テスト
 *
 * 確認対象:
 * - 認証済み管理者が登録画面を表示できること
 * - 正常な入力でユーザー登録できること
 * - 画像ありで登録できること
 * - FormRequestのバリデーションエラーが画面に戻ること
 * - 一般ユーザーは登録できないこと
 *
 * Service単体テストではRepositoryをモックしたが、
 * Featureテストでは実際にDBを使って全体の流れを確認する。
 */
class UserCreateTest extends TestCase
{
    use RefreshDatabase;

    private string $disk;

    /**
     * 各テスト実行前の共通準備
     */
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のStorage diskを固定する
        $this->disk = 'public';

        /**
         * 実ファイルを保存しないようにStorageをfake化する
         *
         * Service内で $iconImage->store(...) を使っているため、
         * filesystems.default も public に合わせる。
         */
        Storage::fake($this->disk);

        config([
            'filesystems.default' => $this->disk,
            'users.icon_image.directory' => 'users',
        ]);

        /**
         * roles はマスタデータなのでSeederで作成する
         *
         * Featureテストでは、実際のDB状態に近づけるため、
         * テスト内で必要なマスタを用意する。
         */
        $this->seed(RoleSeeder::class);
    }

    /**
     * 正常系：管理者はユーザー登録画面を表示できること
     */
    public function test_admin_can_view_user_create_page(): void
    {
        // 管理者ユーザーを作成する
        $adminUser = $this->createAdminUser();

        // 管理者としてログインした状態で登録画面へアクセスする
        $response = $this
            ->actingAs($adminUser)
            ->get(route('users.create'));

        // 登録画面が正常表示(HTTP 200)されることを確認する
        $response->assertOk();

        // 画面に登録フォームで使う文言が表示されていることを確認する
        $response->assertSeeText('ユーザー登録');
        $response->assertSeeText('名前');
        $response->assertSeeText('メールアドレス');
        $response->assertSeeText('パスワード');
        $response->assertSeeText('プロフィール');
    }

    /**
     * 異常系：一般ユーザーはユーザー登録画面を表示できないこと
     */
    public function test_general_user_cannot_view_user_create_page(): void
    {
        // 一般ユーザーを作成する
        $generalUser = $this->createGeneralUser();

        // 一般ユーザーとしてログインした状態で登録画面へアクセスする
        $response = $this
            ->actingAs($generalUser)
            ->get(route('users.create'));

        // 権限がないため403 Forbiddenになることを確認する
        $response->assertForbidden();
    }


    /**
     * 正常系：管理者は画像なしでユーザー登録できること
     */
    public function test_admin_can_create_user_without_icon_image(): void
    {
        // 管理者ユーザーを作成する
        $adminUser = $this->createAdminUser();

        // 登録フォームから送信する入力値を用意する
        $data = $this->validUserData([
            'name' => 'Feature登録ユーザー',
            'email' => 'feature-create@example.com',
            'profile' => 'Feature登録プロフィール',
        ]);

        // 管理者としてログインし、ユーザー登録処理へPOSTする
        $response = $this
            ->actingAs($adminUser)
            ->from(route('users.create'))
            ->post(route('users.store'), $data);

        // 登録成功後、登録画面へリダイレクトされることを確認する
        $response->assertRedirectToRoute('users.create');

        // バリデーションエラーがないことを確認する
        $response->assertSessionHasNoErrors();

        // 成功メッセージがセッションに入っていることを確認する
        $response->assertSessionHas('success', __('messages.users.create.success'));

        // DBにユーザーが登録されていることを確認する
        $this->assertDatabaseHas('users', [
            'role_id' => $data['role_id'],
            'name' => 'Feature登録ユーザー',
            'email' => 'feature-create@example.com',
            'icon_image_path' => '',
            'profile' => 'Feature登録プロフィール',
        ]);

        // 登録されたユーザーを取得する
        $createdUser = User::query()
            ->where('email', 'feature-create@example.com')
            ->firstOrFail();

        // パスワードが平文のまま保存されていないことを確認する
        $this->assertNotSame('password123', $createdUser->password);

        // 入力したパスワードでHashチェックが通ることを確認する
        $this->assertTrue(Hash::check('password123', $createdUser->password));

        // 画像なしなので、usersディレクトリにファイルが保存されていないことを確認する
        $this->assertCount(0, Storage::disk($this->disk)->files('users'));
    }

    /**
     * 正常系：管理者は画像ありでユーザー登録できること
     */
    public function test_admin_can_create_user_with_icon_image(): void
    {
        // 管理者ユーザーを作成する
        $adminUser = $this->createAdminUser();

        /**
         * 登録フォームから送信する入力値を用意する
         *
         * FeatureテストではFormRequestの image バリデーションも通すため、
         * UploadedFile::fake()->image() を使う。
         */
        $data = $this->validUserData([
            'name' => '画像ありFeatureユーザー',
            'email' => 'feature-create-with-icon@example.com',
            'profile' => '画像ありFeatureプロフィール',
            'icon_image' => UploadedFile::fake()
                ->image('icon.jpg')
                ->size(100),
        ]);

        // 管理者としてログインし、ユーザー登録処理へPOSTする
        $response = $this
            ->actingAs($adminUser)
            ->from(route('users.create'))
            ->post(route('users.store'), $data);

        // 登録成功後、登録画面へリダイレクトされることを確認する
        $response->assertRedirectToRoute('users.create');

        // バリデーションエラーがないことを確認する
        $response->assertSessionHasNoErrors();

        // DBにユーザーが登録されていることを確認する
        $this->assertDatabaseHas('users', [
            'name' => '画像ありFeatureユーザー',
            'email' => 'feature-create-with-icon@example.com',
            'profile' => '画像ありFeatureプロフィール',
        ]);

        // 登録されたユーザーを取得する
        $createdUser = User::query()
            ->where('email', 'feature-create-with-icon@example.com')
            ->firstOrFail();

        // 画像パスがDBに保存されていることを確認する
        $this->assertNotEmpty($createdUser->icon_image_path);

        // 画像が users ディレクトリ配下に保存されていることを確認する
        $this->assertTrue(str_starts_with($createdUser->icon_image_path, 'users/'));

        // fake Storage上に実際にファイルが存在することを確認する
        $this->assertTrue(
            Storage::disk($this->disk)->exists($createdUser->icon_image_path)
        );
    }

    /**
     * 異常系：必須項目や形式が不正な場合、登録できずエラーになること
     */
    public function test_admin_cannot_create_user_with_invalid_input(): void
    {
        // 管理者ユーザーを作成する
        $adminUser = $this->createAdminUser();

        // 不正な入力値を用意する
        $data = $this->validUserData([
            'name' => '',
            'email' => 'not-email',
            'password' => 'short',
            'password_confirmation' => 'different',
            'profile' => '入力値保持プロフィール',
        ]);

        // 管理者としてログインし、ユーザー登録処理へPOSTする
        $response = $this
            ->actingAs($adminUser)
            ->from(route('users.create'))
            ->post(route('users.store'), $data);

        // バリデーションエラー時は、元の登録画面へ戻ることを確認する
        $response->assertRedirect(route('users.create'));

        // FormRequestのバリデーションエラーがセッションに入ることを確認する
        $response->assertSessionHasErrors([
            'name',
            'email',
            'password',
        ]);

        // 入力値が保持されていることを確認する
        $response->assertSessionHasInput('email', 'not-email');
        $response->assertSessionHasInput('profile', '入力値保持プロフィール');

        // 不正なデータがDBに登録されていないことを確認する
        $this->assertDatabaseMissing('users', [
            'email' => 'not-email',
        ]);
    }

    /**
     * 異常系：一般ユーザーはユーザー登録できないこと
     */
    public function test_general_user_cannot_create_user(): void
    {
        // 一般ユーザーを作成する
        $generalUser = $this->createGeneralUser();

        // 登録フォームから送信する入力値を用意する
        $data = $this->validUserData([
            'email' => 'forbidden-create@example.com',
        ]);

        // 一般ユーザーとしてログインし、ユーザー登録処理へPOSTする
        $response = $this
            ->actingAs($generalUser)
            ->post(route('users.store'), $data);

        // PolicyまたはControllerの認可により403になることを確認する
        $response->assertForbidden();

        // 権限がないためDBに登録されていないことを確認する
        $this->assertDatabaseMissing('users', [
            'email' => 'forbidden-create@example.com',
        ]);
    }

    /**
     * 異常系：登録処理で例外が発生した場合、登録画面へ戻りエラーメッセージが表示されること
     */
    public function test_create_user_redirects_back_with_error_when_service_throws_exception(): void
    {
        // 登録処理を実行できる管理者ユーザーを作成する
        $adminUser = $this->createAdminUser();

        // 登録フォームから送信する入力値を用意する
        $data = $this->validUserData([
            'name' => 'Feature登録エラーユーザー',
            'email' => 'feature-create-error@example.com',
            'profile' => 'Feature登録エラープロフィール',
        ]);

        // UserServiceをモックに差し替え、登録処理で例外が発生した状態を作る
        $this->mock(UserService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('createUser')
                ->once()
                ->andThrow(new RuntimeException('テスト用例外'));
        });

        // 管理者ユーザーとしてログインした状態で、ユーザー登録処理へPOSTする
        $response = $this
            ->actingAs($adminUser)
            ->from(route('users.create'))
            ->post(route('users.store'), $data);

        // 例外発生時は、直前の登録画面へ戻ることを確認する
        $response->assertRedirect(route('users.create'));

        // エラーメッセージがセッションに入っていることを確認する
        $response->assertSessionHas('error', __('messages.users.create.error'));

        // 入力値がセッションに保持されていることを確認する
        $response->assertSessionHasInput('name', 'Feature登録エラーユーザー');
        $response->assertSessionHasInput('email', 'feature-create-error@example.com');
        $response->assertSessionHasInput('profile', 'Feature登録エラープロフィール');

        // パスワードはセキュリティ上、入力値として保持されていないことを確認する
        $this->assertArrayNotHasKey('password', session('_old_input', []));
        $this->assertArrayNotHasKey('password_confirmation', session('_old_input', []));

        // 登録処理に失敗したため、DBにユーザーが保存されていないことを確認する
        $this->assertDatabaseMissing('users', [
            'email' => 'feature-create-error@example.com',
        ]);
    }





    // private

    /**
     * 管理者ユーザーを作成する
     *
     * @return User
     */
    private function createAdminUser(): User
    {
        return User::query()->create([
            'role_id' => $this->adminRole()->id,
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'icon_image_path' => '',
            'profile' => '管理者プロフィール',
        ]);
    }

    /**
     * 一般ユーザーを作成する
     *
     * @return User
     */
    private function createGeneralUser(): User
    {
        return User::query()->create([
            'role_id' => $this->generalRole()->id,
            'name' => '一般ユーザー',
            'email' => 'general@example.com',
            'password' => Hash::make('password123'),
            'icon_image_path' => '',
            'profile' => '一般ユーザープロフィール',
        ]);
    }

    /**
     * 登録処理で使う正常な入力値を作成する
     *
     * overrideを渡すことで、テストごとに一部の値だけ変更できる。
     *
     * @param array<string, mixed> $overrides 上書きしたい入力値
     * @return array<string, mixed>
     */
    private function validUserData(array $overrides = []): array
    {
        return array_merge([
            'role_id' => $this->generalRole()->id,
            'name' => '登録ユーザー',
            'email' => 'create-user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'profile' => '登録プロフィール',
        ], $overrides);
    }

    /**
     * 管理者ロールを取得する
     *
     * @return Role
     */
    private function adminRole(): Role
    {
        return Role::query()
            ->where('code', Role::CODE_ADMIN)
            ->firstOrFail();
    }

    /**
     * 一般ユーザーロールを取得する
     *
     * @return Role
     */
    private function generalRole(): Role
    {
        return Role::query()
            ->where('code', Role::CODE_GENERAL)
            ->firstOrFail();
    }
}
