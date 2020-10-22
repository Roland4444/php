<?php

use Phinx\Seed\AbstractSeed;

class SpareMainOtherExpensesSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];

        $bank = $this->query("SELECT id FROM bank_account WHERE def=1")->fetch();

        $categories = $this->query("SELECT id FROM cost_category")->fetchAll();
        $categoriesIds = array_map(function ($row) {
            return intval($row['id']);
        }, $categories);

        $innList = ['1000000', '2000000', '3000000', '4000000', '5000000', '6000000', '7000000', '8000000', '9000000'];

        for ($i = 0; $i < 100; $i++) {
            $data[] = [
                'date'         => $faker->dateTimeThisMonth()->format('Y-m-d'),
                'realdate'     => $faker->dateTimeThisMonth()->format('Y-m-d'),
                'recipient'    => $faker->text(50),
                'comment'      => $faker->text(50),
                'money'        => $faker->numberBetween(1000, 1000000),
                'bank_id'      => $bank['id'],
                'category_id'  => $faker->randomElement($categoriesIds),
                'inn'    => $faker->randomElement($innList),
            ];
        }

        $posts = $this->table('main_other_expenses');
        $posts->insert($data)
            ->save();
    }
}
