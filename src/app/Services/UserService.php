<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class UserService
{
    /**
     * UserRepositoryをDIする
     *
     * Serviceは処理の流れを担当し、
     * DB操作はRepositoryへ任せる。
     */
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * ユーザ一覧をページネーション付きで取得する
     */
    public function getPaginatedUsers(int $perPage = 3): LengthAwarePaginator
    {
        // paginate(3)
        // 業務では件数が増える前提なので、all() ではなくページネーションを使う
        //
        // 3件ずつ表示する。
        // ユーザ一覧取得のDB操作はRepositoryへ任せる。
        return $this->userRepository->getPaginatedUsers($perPage);
    }

    /**
     * ユーザを新規登録する
     *
     * @param  array<string, mixed>  $validated
     */
    public function createUser(array $validated, ?UploadedFile $iconImage): User
    {
        // 画像未選択の場合は空文字をDBへ保存する
        $iconImagePath = '';

        try {
            // 画像がアップロードされている場合のみ保存する
            if ($iconImage !== null) {

                // 画像保存
                // - 開発(public)：storage/app/public/users 配下に画像を保存する
                // - DBには users/xxxx.jpg のような相対パスだけ保存する
                // - disk指定は .env で public / s3 などを切り替える想定なので省略する
                $storedPath = $iconImage->store('users');

                // 画像保存に失敗した場合はDB登録せずにエラーにする
                if ($storedPath === false) {
                    throw new RuntimeException('画像の保存に失敗しました。');
                }

                //画像保存成功ならパスを保持
                $iconImagePath = $storedPath;
            }

            // ユーザ登録のDB操作はRepositoryへ任せる
            return $this->userRepository->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'icon_image_path' => $iconImagePath,
                // フォームリクエストでケアしてるので  ?? '' は不要
                'profile' => $validated['profile'],
            ]);
        }
        // Throwable：Exception に加えて、TypeError などのPHPエラー系も捕まえる
        catch (\Throwable $e) {

            // 画像保存後にDB登録で失敗した場合、画像だけ残るのを防ぐ
            if ($iconImagePath !== '') {
                Storage::delete($iconImagePath);
            }

            // エラーログ出力
            // 出力先：storage/logs/laravel.log
            Log::error('ユーザー登録エラー', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // エラーを上層に流す
            throw $e;
        }
    }

    /**
         * ユーザを更新する
         *
         * @param  array<string, mixed>  $validated
         */
    public function updateUser(User $user, array $validated, ?UploadedFile $iconImage): User
    {
        // 新しい画像パスの初期値
        // 画像がアップロードされなければ空文字のまま
        $newIconImagePath = '';

        // 更新前の古い画像パスを先に保持しておく
        $oldIconImagePath = $user->icon_image_path;

        try {
            // 更新データを作成する
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                // フォームリクエストでケアしてるので  ?? '' は不要
                'profile' => $validated['profile'],
            ];

            // パスワードが入力されている場合のみ更新する
            //
            // Userモデル側で password => hashed の cast を設定しているため、
            // ここで Hash::make() は不要。
            if (! empty($validated['password'])) {
                $updateData['password'] = $validated['password'];
            }

            // 新しいアイコン画像がアップロードされている場合のみ差し替える
            if ($iconImage !== null) {

                $storedPath = $iconImage->store('users');

                // 画像保存に失敗した場合はDB更新せずにエラーにする
                if ($storedPath === false) {
                    throw new RuntimeException('画像の保存に失敗しました。');
                }

                // 新しい画像パスを保持する
                $newIconImagePath = $storedPath;

                // DB更新用データに新しい画像パスを入れる
                $updateData['icon_image_path'] = $newIconImagePath;
            }

            // ユーザ更新のDB操作はRepositoryへ任せる
            $updatedUser = $this->userRepository->update($user, $updateData);

            // DB更新が成功した後で、古い画像を削除する
            if ($newIconImagePath !== '' && $oldIconImagePath !== '') {
                Storage::delete($oldIconImagePath);
            }

            return $updatedUser;
        } catch (\Throwable $e) {
            /*
               新しい画像を保存した後にDB更新で失敗した場合、
               新しい画像だけ残るのを防ぐ。
            */
            if ($newIconImagePath !== '') {
                Storage::delete($newIconImagePath);
            }

            // エラーログ出力
            // 出力先：storage/logs/laravel.log
            Log::error('ユーザ更新エラー', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // エラーを上層に流す
            throw $e;
        }
    }


}
