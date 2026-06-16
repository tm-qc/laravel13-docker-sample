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
            'name' => $data['name'],
            'email' => $data['email'],
            // 'password' => Hash::make($data['password']), //UserモデルでHash化するので不要
            'password' => $data['password'],
            // 画像の保存パスを保存する
            'icon_image_path' => $data['icon_image_path'],
            'profile' => $data['profile'],
        ]);
    }
}
