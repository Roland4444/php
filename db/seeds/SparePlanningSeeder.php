<?php

use Phinx\Seed\AbstractSeed;

class SparePlanningSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'id'      => $i + 1,
                'comment'      => $faker->text(30),
                'date'         => $faker->dateTimeThisMonth()->format('Y-m-d'),
                'status_id'    => $faker->numberBetween(1,4),
            ];
        }

        $posts = $this->table('spare_planning');
        $posts->insert($data)
            ->save();
    }
}
