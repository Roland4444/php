<?php

use Phinx\Seed\AbstractSeed;

class SpareSeeder extends AbstractSeed
{
    public function getDependencies()
    {
        return [
            'SparePlanningSeeder',
            'SparePlanningItemsSeeder',
            'SpareSellerSeeder',
            'SpareOrderSeeder',
            'SparePlanningItemsSeeder',
            'SpareMainOtherExpensesSeeder'
        ];
    }

    public function run()
    {

    }
}
