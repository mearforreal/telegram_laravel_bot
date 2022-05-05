<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Manufacturer;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Country::truncate();
        $manufacturer = array('Алматы','Актау','Шымкент','Тараз','Нурсултан','Орал');
        //
        foreach ($manufacturer as $manu){
            City::create([
                'title' => $manu,
            ]);
        }
    }
}
