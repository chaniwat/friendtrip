<?php

use App\EventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EventTypeSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $types = array(
            ['id' => 1, 'name' => 'สายรับประทาน', 'detail' => ''],
            ['id' => 2, 'name' => 'สายแอคชั่น', 'detail' => ''],
            ['id' => 3, 'name' => 'สายหรู', 'detail' => ''],
            ['id' => 4, 'name' => 'สายต่างประเทศ', 'detail' => ''],
            ['id' => 5, 'name' => 'สายถ่ายรูป', 'detail' => ''],
            ['id' => 6, 'name' => 'สายธรรมชาติ', 'detail' => ''],
            ['id' => 7, 'name' => 'สายทำบุญ', 'detail' => ''],
            ['id' => 8, 'name' => 'สายแคมป์', 'detail' => ''],
            ['id' => 9, 'name' => 'สายเดินชิล', 'detail' => ''],
            ['id' => 10, 'name' => 'สายสุขภาพ', 'detail' => ''],
        );

        // Loop through each user above and create the record for them in the database
        foreach ($types as $type)
        {
            $eventType = EventType::find($type['id']);
            if(!$eventType) {
                // Check if not exists, create a new one
                EventType::create($type);
            } else {
                // Check if exists, update
                $eventType->name = $type['name'];
                $eventType->save();
            }
        }

        Model::reguard();
    }
}
