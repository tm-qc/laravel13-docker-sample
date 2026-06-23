<?php

namespace Tests\Unit\Services\Users;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Tests\TestCase;

/**
 * 単体テストサンプル
 *
 * 作成方針
 *
 * 主に自作のServiceの機能を対象にします。
 * テストメソッドは記載されてる機能を中心にテストします。
 *
 * ‐ 正常系：想定通り成功するパターン
 * ‐ 異常系：例外発生時の挙動
 * ‐ 条件分岐：if分岐や対象データなし
 * ‐ 外部連携：外部APIやDBアクセスのモックを使ったテスト
 *
 * Laravel標準機能そのものは基本的にテスト対象外とします。
 *
 * ※ サンプルなので一旦登録のみテスト作成
 *    編集、論理、物理、画像削除などは時間あるときに作成予定
 */

/**
 * ユーザー新規登録のテスト
 */
class UserServiceCreateUserTest extends TestCase
{
    private string $disk;

    /**
     * テスト後の後片付け
     *
     * Mockeryで作成したモックを閉じる。
     * これを入れないと、モックの検証が正しく終了しない場合がある。
     */
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * 各テスト実行前の共通準備
     */
    protected function setUp(): void
    {
        parent::setUp();

        // テストで使うdiskを固定する
        $this->disk = 'public';

        /**
         * テスト用のStorageを作成する
         *
         * Storage::fake('public') により、
         * 実際の storage/app/public にはファイルを保存しない。
         *
         * Service内では $iconImage->store(...) を使っており、
         * disk指定をしていないため、filesystems.default を public に固定する。
         */
        Storage::fake($this->disk);

        config([
            'filesystems.default' => $this->disk,
            'users.icon_image.directory' => 'users',
        ]);
    }

    /**
     * 正常系：画像ありでユーザーを新規登録できること
     *
     * 確認内容:
     * - 画像がアップロードされた場合、Storageへ画像保存されること
     * - 保存された画像パスが icon_image_path としてRepositoryへ渡されること
     * - validated の値がRepositoryへ渡されること
     * - Repositoryから返されたUserがServiceの戻り値として返ること
     *
     */
    public function test_create_user_with_icon_image_returns_created_user(): void
    {
        /**
         * FormRequestでバリデーション済みになった想定のデータ
         *
         * Serviceの単体テストなので、
         * FormRequestのバリデーション自体はここでは確認しない。
         */
        $validated = [
            'role_id' => 1,
            'name' => '画像ありユーザー',
            'email' => 'with-icon@example.com',
            'password' => 'password123',
            'profile' => '画像ありプロフィール',
        ];

        /**
         * アップロードされた画像の代わりになるテスト用ファイル
         *
         * Serviceでは画像のバリデーションは行わず、保存処理だけを行う。
         * そのため、ここではUploadedFileとして扱えるテスト用ファイルを用意する。
         */
        $iconImage = UploadedFile::fake()->create(
            name: 'icon.jpg',
            kilobytes: 100,
            mimeType: 'image/jpeg'
        );

        /**
         * Repositoryから返される想定のUser
         *
         * DBは使わないため、Userモデルのインスタンスだけ作成する。
         * ※ icon_image_path は Mockery::on() の $data 側で確認している + サービス内生成でランダム文字列なので不要とする
         */
        $expectedUser = new User([
            'role_id' => $validated['role_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'profile' => $validated['profile'],
        ]);

        /**
         * UserRepositoryのモックを作成する
         *
         * Serviceの単体テストなので、
         * Repositoryの中身、DB登録、Eloquentのcreate処理はここでは確認しない。
         */
        $userRepository = Mockery::mock(UserRepository::class);

        /**
         * Repositoryのcreate()が1回正しく呼ばれることを期待する
         */
        $userRepository
            ->shouldReceive('create')// リポジトリのcreateが呼ばれるはず
            ->once()// 一回呼ばれるはず
            // ServiceからRepositoryへ渡されるデータ($data)が、
            // テストからServiceへ渡したバリデーション済みデータ($validated)を元に正しく作られていることを確認する
            // ※ icon_image_path はService内で作られるため、空でないことと users/ 配下であることを確認する
            // ※データの中身が違ってたら異常
            ->with(Mockery::on(function (array $data) use ($validated): bool {
                return $data['role_id'] === $validated['role_id']
                && $data['name'] === $validated['name']
                && $data['email'] === $validated['email']
                // リポジトリのモックでモデルのハッシュ化までいかないのでチェック可能
                && $data['password'] === $validated['password']
                // pathが空じゃない & users 配下ならOK(ファイル名はランダム + ユーザからの送信値ではないため)
                && $data['icon_image_path'] !== '' && str_starts_with($data['icon_image_path'], 'users/')
                && $data['profile'] === $validated['profile'];
            }))
            ->andReturn($expectedUser); // create()が呼ばれたら、モックは$expectedUserを返すようにセット

        // モックしたRepositoryを渡してServiceを作成する
        $userService = new UserService($userRepository);

        // テスト対象メソッドを実行する
        $actualUser = $userService->createUser($validated, $iconImage);

        // Repositoryから返されたUserが、Serviceの戻り値としてそのまま返ることを確認する
        $this->assertSame($expectedUser, $actualUser);

        // usersディレクトリ配下に画像が1件保存されていることを確認する
        $this->assertCount(1, Storage::disk($this->disk)->files('users'));
    }

    /**
     * 条件分岐：画像なしの場合のテスト
     *
     * 確認内容:
     * - 画像がアップロードされてなくても正しく保存されるか確認
     *
     * @return void
     */
    public function test_create_user_without_icon_image_returns_created_user(): void
    {
        // FormRequestでバリデーション済みになった想定のデータ
        $validated = [
            'role_id' => 1,
            'name' => '画像なしユーザー',
            'email' => 'without-icon@example.com',
            'password' => 'password123',
            'profile' => '画像なしプロフィール',
        ];

        // Repositoryから返される想定のUser
        $expectedUser = new User([
            'role_id' => $validated['role_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'icon_image_path' => '',
            'profile' => $validated['profile'],
        ]);

        // UserRepositoryのモックを作成する
        $userRepository = Mockery::mock(UserRepository::class);

        // Repositoryのcreate()が1回正しく呼ばれることを期待する
        $userRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data) use ($validated): bool {
                return $data['role_id'] === $validated['role_id']
                    && $data['name'] === $validated['name']
                    && $data['email'] === $validated['email']
                    && $data['password'] === $validated['password']
                    && $data['icon_image_path'] === '' //画像なしのパターン。画像ない場合はこれが空のままになる
                    && $data['profile'] === $validated['profile'];
            }))
            ->andReturn($expectedUser);

        // モックしたRepositoryを渡してServiceを作成する
        $userService = new UserService($userRepository);

        // テスト対象メソッドを実行する
        $actualUser = $userService->createUser($validated, null);

        // Repositoryから返されたUserが、Serviceの戻り値としてそのまま返ることを確認する
        $this->assertSame($expectedUser, $actualUser);

        // usersディレクトリ配下に画像が0件であることを確認する
        $this->assertCount(0, Storage::disk($this->disk)->files('users'));
    }

    /**
     * 異常系：画像保存に失敗した場合のテスト
     *
     * @return void
     */
    public function test_create_user_throws_exception_when_icon_image_store_failed(): void
    {
        // FormRequestでバリデーション済みになった想定のデータ
        $validated = [
            'role_id' => 1,
            'name' => '画像保存失敗ユーザー',
            'email' => 'store-failed@example.com',
            'password' => 'password123',
            'profile' => '画像保存失敗プロフィール',
        ];

        /**
         * UploadedFileをモックする
         *
         * store()がfalseを返す状態をモックで作る。
         */
        $iconImage = Mockery::mock(UploadedFile::class);

        // 登録メソッド store に保存先のフォルダ名が渡されて1回呼ばれて失敗 false を返す想定
        $iconImage
            ->shouldReceive('store')
            ->once()
            ->with(config('users.icon_image.directory'))
            ->andReturn(false);

        // UserRepositoryのモックを作成する
        $userRepository = Mockery::mock(UserRepository::class);

        // 画像保存に失敗した場合、DB登録処理は実行しないので create が呼ばれない想定
        $userRepository
            ->shouldNotReceive('create');

        // モックにしたRepositoryを渡してServiceを作成する
        $userService = new UserService($userRepository);

        // RuntimeExceptionが発生することを確認する準備
        $this->expectException(RuntimeException::class);

        // 例外メッセージが想定通りであることを確認する準備
        $this->expectExceptionMessage(__('messages.users.exceptions.icon_store_failed'));

        // テスト対象メソッドを実行する
        // $iconImage->store() が false を返すようにモックしているため、
        // createUser() 実行中に RuntimeException が発生する想定
        $userService->createUser($validated, $iconImage);
    }


    /**
     * 異常系：例外catchに行く場合のテスト
     *
     * 画像保存後にRepository登録で失敗した場合、先に保存した画像が削除されることを確認する
     *
     * @return void
     */
    public function test_create_user_deletes_stored_icon_image_when_repository_throws_exception(): void
    {
        // FormRequestでバリデーション済みになった想定のデータ
        $validated = [
            'role_id' => 1,
            'name' => '登録失敗ユーザー',
            'email' => 'repository-error@example.com',
            'password' => 'password123',
            'profile' => '登録失敗プロフィール',
        ];

        // アップロードされた画像の代わりになるテスト用ファイル
        $iconImage = UploadedFile::fake()->create(
            name: 'icon.jpg',
            kilobytes: 100,
            mimeType: 'image/jpeg',
        );

        // UserRepositoryのモックを作成する
        $userRepository = Mockery::mock(UserRepository::class);

        // リポジトリで create が一回呼ばれ失敗を想定
        $userRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::type('array'))
            ->andThrow(new RuntimeException('ユーザー登録エラー'));

        // モックにしたRepositoryを渡してServiceを作成する
        $userService = new UserService($userRepository);

        // 例外発生後に、画像削除まで確認する
        try {
            // RuntimeExceptionが発生する想定で実行
            $userService->createUser($validated, $iconImage);

            $this->fail('RuntimeExceptionが発生する想定です。');
        } catch (RuntimeException $e) {
            $this->assertSame('ユーザー登録エラー', $e->getMessage());

            // Repository登録で失敗した場合、先に保存した画像が削除されていることを確認する
            $this->assertCount(0, Storage::disk($this->disk)->files('users'));
        }
    }
}
