<?php

namespace Tests\Unit\Services\Users;

use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
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
 */


/**
 * ユーザー一覧取得のテスト
 */
class UserServiceGetPaginatedUsersTest extends TestCase
{
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
     * ユーザ一覧をページネーション付きで取得できること
     *
     * 確認内容:
     * - config の users.pagination.per_page を設定し取得されてるか
     * - モックリポジトリで返ってくる想定の $expectedPaginator と サービスの実行結果 $actualPaginator が一致するか
     *
     * テスト引っかかった場合
     * - userRepository->getPaginatedUsers($perPage)に固定値を渡してないか確認する
     */
    public function test_get_paginated_users_uses_config_per_page(): void
    {
        /**
         * テスト後にRepositoryから返される想定のページネーション結果
         *
         * モックなので返される想定のデータを答え合わせのために作成しておく。
         * DBから本物のユーザーを取らずに、「Repositoryが返したことにするページネーション結果」
         */

        // config or 初期値 3 以外でエラーとするための想定値
        $expectedPaginatorFirst = new LengthAwarePaginator(
            items: [],//「リポジトリで標準機能で取得してるだけ」 + 「サービスでデータを加工、取得していない」ので中身の確認は不要なので今回は空にしている。
            total: 0,// itemsが今回は空なので0
            perPage: 3,// 設定値が 3 = config or 初期値の想定
            currentPage: 1,// 現ページ位置は1
        );

        // 上記の場合 config 使わずに初期値の固定値 3 でも通るのでここで 6 として初期値 3 の固定値でもエラーとして、結果 config を使ってる場合しかテストが通らないようにする
        $expectedPaginatorSecond = new LengthAwarePaginator(
            items: [],
            total: 0,
            perPage: 6,
            currentPage: 1,
        );

        /**
         * UserRepositoryのモックを作成する
         *
         * Serviceの単体テストなので、
         * Repositoryの中身、DBアクセス、Eloquentの動きはここでは確認しない。
         */
        $userRepository = Mockery::mock(UserRepository::class);

        /**
         * モックがリポジトリなので想定される動きを決める
         * ※getPaginatedUsers(設定値) が1回呼ばれることを期待する
         */

        // 1回目は config or 初期値 3 ()の想定で 3 が渡されることを確認する
        $userRepository
            ->shouldReceive('getPaginatedUsers')// getPaginatedUsersが呼ばれるはず
            ->once()// 一回呼ばれるはず
            ->with(3)// 引数は設定値が渡されるはず
            ->andReturn($expectedPaginatorFirst);// getPaginatedUsers(3) が1回呼ばれた場合、$expectedPaginatorFirstが返ってくるはず

        // 2回目は config を 6 に変えるので、6 が渡されることを確認する
        // ※2回目は 初期値3 では通らないようにするため config に 6を渡す
        $userRepository
            ->shouldReceive('getPaginatedUsers')
            ->once()
            ->with(6)
            ->andReturn($expectedPaginatorSecond);

        // モックしたRepositoryを渡してServiceを作成する
        $userService = new UserService($userRepository);

        // config値をテスト用に固定する(config or 初期値 3 で通る)
        config(['users.pagination.per_page' => 3]);
        // サービスのテスト対象メソッドを実行する
        $actualPaginatorFirst = $userService->getPaginatedUsers();

        // config値をテスト用に固定する(初期値 3 で通らないので、config使ってるか確認になる)
        config(['users.pagination.per_page' => 6]);
        $actualPaginatorSecond = $userService->getPaginatedUsers();

        // Repositoryが返したページネーション結果が、そのまま返っていることを確認する
        $this->assertSame($expectedPaginatorFirst, $actualPaginatorFirst);
        $this->assertSame($expectedPaginatorSecond, $actualPaginatorSecond);
    }
}
