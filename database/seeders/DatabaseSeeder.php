<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Database\Factories\HospitalFactory;
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
        $this->call([
//            HospitalSeeder::class,
//            BedSeeder::class
        ]);
    }
}
