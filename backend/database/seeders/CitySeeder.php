<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\SubCity;
use App\Models\Neighborhood;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            [
                'name'   => 'Addis Ababa',
                'slug'   => 'addis-ababa',
                'region' => 'Addis Ababa City Administration',
                'sort_order' => 1,
                'sub_cities' => [
                    ['name' => 'Bole', 'slug' => 'bole', 'neighborhoods' => ['Bole Michael', 'Bole Bulbula', 'CMC', 'Bambis', 'Gerji']],
                    ['name' => 'Kirkos', 'slug' => 'kirkos', 'neighborhoods' => ['Kazanchis', 'Sarbet', 'Lideta', 'Mexico']],
                    ['name' => 'Yeka', 'slug' => 'yeka', 'neighborhoods' => ['Kotebe', 'Jemo', 'Ayat', 'Summit']],
                    ['name' => 'Arada', 'slug' => 'arada', 'neighborhoods' => ['Piassa', 'Arat Kilo', 'Sidist Kilo']],
                    ['name' => 'Addis Ketema', 'slug' => 'addis-ketema', 'neighborhoods' => ['Merkato', 'Gotera']],
                    ['name' => 'Gulele', 'slug' => 'gulele', 'neighborhoods' => ['Shiro Meda', 'Kolfe']],
                    ['name' => 'Kolfe Keranio', 'slug' => 'kolfe-keranio', 'neighborhoods' => ['Kolfe', 'Keranio', 'Gofa']],
                    ['name' => 'Lideta', 'slug' => 'lideta', 'neighborhoods' => ['Lideta', 'Megenagna']],
                    ['name' => 'Nifas Silk-Lafto', 'slug' => 'nifas-silk-lafto', 'neighborhoods' => ['Lafto', 'Silk', 'Lebu']],
                    ['name' => 'Akaki Kaliti', 'slug' => 'akaki-kaliti', 'neighborhoods' => ['Akaki', 'Kaliti', 'Kality']],
                ],
            ],
            ['name' => 'Dire Dawa', 'slug' => 'dire-dawa', 'region' => 'Dire Dawa City Administration', 'sort_order' => 2, 'sub_cities' => [
                ['name' => 'Dire Dawa Central', 'slug' => 'dire-dawa-central', 'neighborhoods' => ['Kezira', 'Sabian']],
            ]],
            ['name' => 'Adama', 'slug' => 'adama', 'region' => 'Oromia', 'sort_order' => 3, 'sub_cities' => [
                ['name' => 'Adama Central', 'slug' => 'adama-central', 'neighborhoods' => ['Adama 01', 'Adama 02']],
            ]],
            ['name' => 'Hawassa', 'slug' => 'hawassa', 'region' => 'Sidama', 'sort_order' => 4, 'sub_cities' => [
                ['name' => 'Hawassa Central', 'slug' => 'hawassa-central', 'neighborhoods' => ['Hawassa 01']],
            ]],
            ['name' => 'Bahir Dar', 'slug' => 'bahir-dar', 'region' => 'Amhara', 'sort_order' => 5, 'sub_cities' => [
                ['name' => 'Bahir Dar Central', 'slug' => 'bahir-dar-central', 'neighborhoods' => ['Bahir Dar 01']],
            ]],
            ['name' => 'Mekelle', 'slug' => 'mekelle', 'region' => 'Tigray', 'sort_order' => 6, 'sub_cities' => [
                ['name' => 'Mekelle Central', 'slug' => 'mekelle-central', 'neighborhoods' => ['Mekelle 01']],
            ]],
        ];

        foreach ($cities as $cityData) {
            $subCitiesData = $cityData['sub_cities'] ?? [];
            unset($cityData['sub_cities']);

            $city = City::firstOrCreate(['slug' => $cityData['slug']], array_merge($cityData, ['is_active' => true]));

            foreach ($subCitiesData as $scData) {
                $neighborhoods = $scData['neighborhoods'] ?? [];
                unset($scData['neighborhoods']);

                $sc = SubCity::firstOrCreate(['slug' => $scData['slug']], array_merge($scData, ['city_id' => $city->id, 'is_active' => true]));

                foreach ($neighborhoods as $nName) {
                    $nSlug = \Illuminate\Support\Str::slug($nName . '-' . $sc->id);
                    Neighborhood::firstOrCreate(['slug' => $nSlug], [
                        'sub_city_id' => $sc->id,
                        'name'        => $nName,
                        'is_active'   => true,
                    ]);
                }
            }
        }

        $this->command->info('Cities, sub-cities, and neighborhoods seeded.');
    }
}
