<?php

use Phinx\Seed\AbstractSeed;

class SpareOrderItemsSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            for ($j = 0; $j < 10; $j++) {
                $data[] = [
                    'id' => $i + $j * 10 + 1,
                    'order_id' => $i + 1,
                    'quantity' => $faker->numberBetween(1, 100),
                    'sub_quantity' => null,
                    'price' => $faker->numberBetween(1000, 15000),
                    'spare_id' => $faker->numberBetween(4, 28),
                ];
            }
        }

        $posts = $this->table('spare_order_items');
        $posts->insert($data)
            ->save();
    }
}
