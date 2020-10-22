<?php

use Phinx\Seed\AbstractSeed;

class SpareSellerSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = 1; $i < 10; $i++) {
            $data[] = [
                'id' => $i,
                'name' => $faker->text(20),
                'inn' => $i . '000000',
            ];
        }

        $table = $this->table('spare_seller');
        $table->insert($data)
            ->save();
    }
}
