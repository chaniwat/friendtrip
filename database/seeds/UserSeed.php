<?php

use App\Gender;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        User::create(
            [
                "email" => "admin@friendtrip.dev",
                "password" => Hash::make("friendtrip"),
                "first_name" => "แอดมิน",
                "last_name" => "ผู้ดูแลระบบ",
                "display_name" => "administrator",
                "birthdate" => Carbon::now(),
                "gender" => Gender::MALE,
                "religion" => "BUDDHISM",
                "phone" => "0xxxxxxxxx"
            ]
        );

        Model::reguard();
    }
}
