<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(WeekdaysTableSeeder::class);
         $this->call(PropertyCategoriesTableSeeder::class);
         $this->call(ScoreItemsTableSeeder::class);
         $this->call(StatesTableSeeder::class);
         $this->call(CustomRolesTableSeeder::class);
         $this->call(OpeningHoursStatesTableSeeder::class);
         $this->call(DegreesTableSeeder::class);
    }
}
