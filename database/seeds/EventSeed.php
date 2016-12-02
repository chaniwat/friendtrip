<?php

use App\EventSetting;
use App\Event;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EventSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $event = Event::create(
            [
                "owner_id" => User::where('email', 'admin@friendtrip.dev')->first()->id,
                "name" => "ทดสอบกิจกรรม",
                "destination_place" => "ลาดกระบัง",
                "start_date" => Carbon::now()->addDays(7),
                "end_date" => Carbon::now()->addDays(12),
                "appointment_place" => "ลาดกระบัง",
                "appointment_time" => Carbon::now()->addDays(7),
                "details" => "<h1>best place to learn</h1>",
                "type" => "LEARNING",
                "approximate_cost" => 4999.99
            ]
        );

        $setting = EventSetting::find(EventSetting::ALLOW_RELIGION);
        $event->settings()->attach($setting, ['value' => '*']);

        $setting = EventSetting::find(EventSetting::ALLOW_AGE);
        $event->settings()->attach($setting, ['value' => '*']);

        $setting = EventSetting::find(EventSetting::ALLOW_GENDER);
        $event->settings()->attach($setting, ['value' => '*']);

        $setting = EventSetting::find(EventSetting::MAX_PARTICIPANT);
        $event->settings()->attach($setting, ['value' => 150]);

        Model::reguard();
    }
}
