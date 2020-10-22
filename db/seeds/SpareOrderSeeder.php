<?php

use Phinx\Seed\AbstractSeed;

class SpareOrderSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'id'      => $i + 1,
                'document'      => 'Договор №' . ($i + 1),
                'seller_id'      => $faker->numberBetween(1,9),
                'date'         => $faker->dateTimeThisMonth()->format('Y-m-d'),
                'expected_date'         => $faker->dateTimeThisMonth()->format('Y-m-d'),
                'status_id'    => $faker->numberBetween(1,3),
                'payment_status_id'    => $faker->numberBetween(1,3),
            ];
        }

        $posts = $this->table('spare_order');
        $posts->insert($data)
            ->save();
    }
}
