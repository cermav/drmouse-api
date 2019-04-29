<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('property_categories')->delete();

        DB::table('property_categories')->insert([
            ['name' => 'Vybavení ordinace', 'form_section_title' => 'Můžete popsat vybavení vaší ordinace.', 'form_section_description' => 'Někteří pacienti hledají konkrétní vybavení / vyšetření. Co jim nabízíte vy?', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Hlavní zaměření', 'form_section_title' => 'Vyplňte vaše zaměření.', 'form_section_description' => 'Na ošetření jakých druhů zvířat se zaměřujete?', 'created_at' => date("Y-m-d H:i:s")],
            ['name' => 'Specializace', 'form_section_title' => 'Popište vaši specializaci.', 'form_section_description' => 'Na jaká onemocnění se specializujete?', 'created_at' => date("Y-m-d H:i:s")]
        ]);
    }
}
