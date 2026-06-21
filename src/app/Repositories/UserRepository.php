<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * ユーザ一覧をページネーション付きで取得する
     *
     * RepositoryはDB操作を担当する。
     * 一覧取得のEloquent処理をServiceから分離する。
     */
    public function getPaginatedUsers(int $perPage): LengthAwarePaginator
    {
        // latest():
        // 新しく登録したユーザを上に表示するため、作成日時の降順にする。
        //
        // paginate():
        // 件数が増える前提なので、all()ではなくページネーションで取得する。
        return User::latest()->paginate($perPage);
    }

    /**
     * ユーザを新規登録する
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        // usersテーブルへ新規登録する。
        // TODO：複数テーブルの場合はトランザクション利用する
        // 成功したらオブジェクト、失敗したらエラーになる
        return User::create([
            'role_id' => $data['role_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            // 'password' => Hash::make($data['password']), //UserモデルでHash化するので不要
            'password' => $data['password'],
            // 画像の保存パスを保存する
            'icon_image_path' => $data['icon_image_path'],
            'profile' => $data['profile'],
        ]);
    }

    /**
     * ユーザを更新する
     *
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        // usersテーブルを更新する。
        //
        // update():
        // 成功したら true、失敗したら false が返る。
        // ただし通常のDBエラーは例外になる。
        $user->update($data);

        // 更新後の最新データを返す
        return $user->refresh();
    }

    /**
     * ユーザーを論理削除する
     */
    public function softDelete(User $user): void
    {
        /*
         * SoftDeletesを使っているため、delete() は物理削除ではなく論理削除になる。
         *
         * 実際には users.deleted_at に現在日時が入る。
         * DBのレコード自体は残る。
         *
         * 論理削除だけを取得したいときは取得時に onlyTrashed() を使う
         * 通常、論理削除両方取得したいときは withTrashed() を使う
         */
        $user->delete();
    }

    /**
     * ユーザーを物理削除する
     */
    public function forceDelete(User $user): void
    {
        /*
         * delete():
         * 対象のユーザーレコードをDBから削除する。
         *
         * 注意:
         * delete() にすると論理削除になるため、物理削除にはならない。
        */
        $user->forceDelete();
    }
}
