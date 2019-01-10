<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Role;

class CustomRolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $role = Role::firstOrNew(['name' => 'doctor']);
        if (!$role->exists) {
            $role->fill([
                    'display_name' => 'Doctor',
                ])->save();
        }
    }
}
