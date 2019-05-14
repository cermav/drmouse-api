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
        $this->call( WeekdaysTableSeeder::class );
        $this->call(OpeningHoursStatesTableSeeder::class);

        $this->call(PropertyCategoriesTableSeeder::class);
        $this->call(PropertiesTableSeeder::class);

        $this->call(ScoreItemsTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(CustomRolesTableSeeder::class);

        $this->call(DegreesTableSeeder::class);
        $this->call(CzechNamesTableSeeder::class);
        $this->call(ServicesTableSeeder::class);

        $this->call(DataTypesTableSeeder::class);
        $this->call(DataRowsTableSeeder::class);

        $this->call(MenusTableSeeder::class);
        $this->call(MenuItemsTableSeeder::class);

        $this->call(PermissionsTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);

        $this->call(SettingsTableSeeder::class);

        $this->call(TranslationsTableSeeder::class);

        // $this->call(DoctorSeeder::class);
        $this->call(WeekdaysTableSeeder::class);
    }
}
