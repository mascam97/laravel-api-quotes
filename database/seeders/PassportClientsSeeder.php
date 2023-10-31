<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PassportClientsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('oauth_clients')->insert([
            [
                'id' => '994a950e-5d45-4a1e-bbc4-913eb8e1c1dc',
                'user_id' => null,
                'name' => 'api',
                'secret' => 'obDi3CT2tbgdecxrcXIsRS1Zhydyv1rTba2T0tQJ',
                'provider' => 'users',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2023-05-28 14:14:23',
                'updated_at' => '2023-05-28 14:14:23',
            ],
            [
                'id' => '994a9507-396f-44c6-a66e-5a8fe42a5bea',
                'user_id' => null,
                'name' => 'api-admin',
                'secret' => 'B47IuVfr6N8iJzbmuYWx1e7f556dqtZCpgo4nKrK',
                'provider' => 'users',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2023-05-28 14:15:23',
                'updated_at' => '2023-05-28 14:15:23',
            ],
            [
                'id' => '994a9507-396f-44c6-a66e-5a8fe42a5be1',
                'user_id' => null,
                'name' => 'api-analytics',
                'secret' => 'B47IuVfr6N8iJzbmuYWx1e7f556dqtZCpgo4nKrK',
                'provider' => 'users',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2023-05-28 14:15:43',
                'updated_at' => '2023-05-28 14:15:43',
            ],
            [
                'id' => '994a94fe-c117-4d33-81f3-ae9145e899c4',
                'user_id' => null,
                'name' => 'external-api',
                'secret' => 'h13Sd6JyaArK2earEWhRsicNxO6cFJudh5wmWG0P',
                'provider' => 'users',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2023-05-28 14:16:23',
                'updated_at' => '2023-05-28 14:16:23',
            ],
        ]);
    }
}
