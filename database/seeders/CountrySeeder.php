<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Manufacturer;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Country::truncate();
        $manufacturer = array('Китай','Германия','Япония','Корея','Франция','Россия','Англия');
        //
        foreach ($manufacturer as $manu){
            Country::create([
                'title' => $manu,
            ]);
        }
    }
}
