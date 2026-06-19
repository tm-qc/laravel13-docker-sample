<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
         * Seederの実行順を管理する。
         *
         * roles は users から参照される親データなので、
         * 今後 UserSeeder を作る場合も RoleSeeder を先に実行する。
         */
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
