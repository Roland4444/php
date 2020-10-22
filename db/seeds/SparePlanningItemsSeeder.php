<?php

use Phinx\Seed\AbstractSeed;

class SparePlanningItemsSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            for ($j = 0; $j < 10; $j++) {
                $data[] = [
                    'id' => $i + $j * 10 + 1,
                    'planning_id' => $i + 1,
                    'spare_id' => $faker->numberBetween(4, 28),
                    'quantity' => $faker->numberBetween(1, 100),
                ];
            }
        }

        $posts = $this->table('spare_planning_items');
        $posts->insert($data)
            ->save();
    }
}
