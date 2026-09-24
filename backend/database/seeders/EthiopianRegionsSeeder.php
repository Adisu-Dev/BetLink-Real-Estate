<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\SubCity;
use App\Models\Neighborhood;
use Illuminate\Database\Seeder;

class EthiopianRegionsSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            // Chartered Cities
            [
                'name' => 'Addis Ababa',
                'slug' => 'addis-ababa',
                'region' => 'Addis Ababa City Administration',
                'sort_order' => 1,
                'sub_cities' => [
                    ['name' => 'Addis Ketema', 'slug' => 'addis-ketema', 'kebeles' => ['Merkato', 'Gotera', 'Sefere Selam']],
                    ['name' => 'Akaki Kaliti', 'slug' => 'akaki-kaliti', 'kebeles' => ['Akaki', 'Kaliti', 'Dukem']],
                    ['name' => 'Arada', 'slug' => 'arada', 'kebeles' => ['Piassa', 'Arat Kilo', 'Sidist Kilo']],
                    ['name' => 'Bole', 'slug' => 'bole', 'kebeles' => ['Bole Michael', 'Bole Bulbula', 'CMC', 'Bambis', 'Gerji']],
                    ['name' => 'Gulele', 'slug' => 'gulele', 'kebeles' => ['Shiro Meda', 'Entoto', 'Kolfe']],
                    ['name' => 'Kirkos', 'slug' => 'kirkos', 'kebeles' => ['Kazanchis', 'Sarbet', 'Mexico', 'Aware']],
                    ['name' => 'Kolfe Keranio', 'slug' => 'kolfe-keranio', 'kebeles' => ['Kolfe', 'Keranio', 'Gofa']],
                    ['name' => 'Lideta', 'slug' => 'lideta', 'kebeles' => ['Lideta', 'Megenagna', '22 Mazoria']],
                    ['name' => 'Nifas Silk-Lafto', 'slug' => 'nifas-silk-lafto', 'kebeles' => ['Lafto', 'Silk', 'Lebu']],
                    ['name' => 'Yeka', 'slug' => 'yeka', 'kebeles' => ['Kotebe', 'Jemo', 'Ayat', 'Summit', 'Bole Arabsa']],
                    ['name' => 'Lemi Kura', 'slug' => 'lemi-kura', 'kebeles' => ['Lemi Kura 01', 'Lemi Kura 02']],
                ],
            ],
            [
                'name' => 'Dire Dawa',
                'slug' => 'dire-dawa',
                'region' => 'Dire Dawa City Administration',
                'sort_order' => 2,
                'sub_cities' => [
                    ['name' => 'Dire Dawa City', 'slug' => 'dire-dawa-city', 'kebeles' => ['Kezira', 'Sabian', 'Magala', 'Legehare']],
                    ['name' => 'Gurgura', 'slug' => 'gurgura', 'kebeles' => ['Gurgura 01', 'Gurgura 02']],
                ],
            ],

            // Regional States
            [
                'name' => 'Tigray',
                'slug' => 'tigray',
                'region' => 'Tigray Region',
                'sort_order' => 3,
                'sub_cities' => [
                    ['name' => 'Mekelle', 'slug' => 'mekelle', 'kebeles' => ['Hawelti', 'Hadnet', 'Ayder', 'Kedamay Weyane']],
                    ['name' => 'Adigrat', 'slug' => 'adigrat', 'kebeles' => ['Adigrat 01', 'Adigrat 02']],
                    ['name' => 'Axum', 'slug' => 'axum', 'kebeles' => ['Axum Town']],
                    ['name' => 'Shire', 'slug' => 'shire', 'kebeles' => ['Shire Town']],
                ],
            ],
            [
                'name' => 'Afar',
                'slug' => 'afar',
                'region' => 'Afar Region',
                'sort_order' => 4,
                'sub_cities' => [
                    ['name' => 'Semera', 'slug' => 'semera', 'kebeles' => ['Semera Town']],
                    ['name' => 'Asayita', 'slug' => 'asayita', 'kebeles' => ['Asayita Town']],
                    ['name' => 'Awash', 'slug' => 'awash', 'kebeles' => ['Awash Town']],
                ],
            ],
            [
                'name' => 'Amhara',
                'slug' => 'amhara',
                'region' => 'Amhara Region',
                'sort_order' => 5,
                'sub_cities' => [
                    ['name' => 'Bahir Dar', 'slug' => 'bahir-dar', 'kebeles' => ['Belay Zeleke', 'Tana', 'Shimbet', 'Shumabo']],
                    ['name' => 'Gondar', 'slug' => 'gondar', 'kebeles' => ['Arada', 'Fasil', 'Maraki']],
                    ['name' => 'Dessie', 'slug' => 'dessie', 'kebeles' => ['Dessie Town']],
                    ['name' => 'Debre Birhan', 'slug' => 'debre-birhan', 'kebeles' => ['Debre Birhan Town']],
                    ['name' => 'Debre Markos', 'slug' => 'debre-markos', 'kebeles' => ['Debre Markos Town']],
                ],
            ],
            [
                'name' => 'Oromia',
                'slug' => 'oromia',
                'region' => 'Oromia Region',
                'sort_order' => 6,
                'sub_cities' => [
                    ['name' => 'Adama (Nazret)', 'slug' => 'adama', 'kebeles' => ['Adama 01', 'Adama 02', 'Adama 03']],
                    ['name' => 'Bishoftu (Debre Zeyit)', 'slug' => 'bishoftu', 'kebeles' => ['Bishoftu 01', 'Bishoftu 02']],
                    ['name' => 'Jimma', 'slug' => 'jimma', 'kebeles' => ['Jimma Town']],
                    ['name' => 'Nekemte', 'slug' => 'nekemte', 'kebeles' => ['Nekemte Town']],
                    ['name' => 'Ambo', 'slug' => 'ambo', 'kebeles' => ['Ambo Town']],
                    ['name' => 'Shashamane', 'slug' => 'shashamane', 'kebeles' => ['Shashamane Town']],
                    ['name' => 'Bale Robe', 'slug' => 'bale-robe', 'kebeles' => ['Robe Town']],
                ],
            ],
            [
                'name' => 'Somali',
                'slug' => 'somali',
                'region' => 'Somali Region',
                'sort_order' => 7,
                'sub_cities' => [
                    ['name' => 'Jigjiga', 'slug' => 'jigjiga', 'kebeles' => ['Jigjiga Town']],
                    ['name' => 'Gode', 'slug' => 'gode', 'kebeles' => ['Gode Town']],
                    ['name' => 'Degehabur', 'slug' => 'degehabur', 'kebeles' => ['Degehabur Town']],
                ],
            ],
            [
                'name' => 'Benishangul-Gumuz',
                'slug' => 'benishangul-gumuz',
                'region' => 'Benishangul-Gumuz Region',
                'sort_order' => 8,
                'sub_cities' => [
                    ['name' => 'Asosa', 'slug' => 'asosa', 'kebeles' => ['Asosa Town']],
                    ['name' => 'Pawe', 'slug' => 'pawe', 'kebeles' => ['Pawe Town']],
                ],
            ],
            [
                'name' => 'Southern Nations, Nationalities, and Peoples (SNNP)',
                'slug' => 'snnp',
                'region' => 'SNNP Region',
                'sort_order' => 9,
                'sub_cities' => [
                    ['name' => 'Hawassa', 'slug' => 'hawassa', 'kebeles' => ['Hawassa 01', 'Hawassa 02', 'Menaharia']],
                    ['name' => 'Arba Minch', 'slug' => 'arba-minch', 'kebeles' => ['Arba Minch Town']],
                    ['name' => 'Wolaita Sodo', 'slug' => 'wolaita-sodo', 'kebeles' => ['Sodo Town']],
                ],
            ],
            [
                'name' => 'Gambela',
                'slug' => 'gambela',
                'region' => 'Gambela Region',
                'sort_order' => 10,
                'sub_cities' => [
                    ['name' => 'Gambela City', 'slug' => 'gambela-city', 'kebeles' => ['Gambela Town']],
                ],
            ],
            [
                'name' => 'Harari',
                'slug' => 'harari',
                'region' => 'Harari Region',
                'sort_order' => 11,
                'sub_cities' => [
                    ['name' => 'Harar', 'slug' => 'harar', 'kebeles' => ['Jugol', 'Erer', 'Argoba Bari']],
                ],
            ],
            [
                'name' => 'Sidama',
                'slug' => 'sidama',
                'region' => 'Sidama Region',
                'sort_order' => 12,
                'sub_cities' => [
                    ['name' => 'Hawassa', 'slug' => 'hawassa-sidama', 'kebeles' => ['Hawassa Sidama 01']],
                    ['name' => 'Yirgalem', 'slug' => 'yirgalem', 'kebeles' => ['Yirgalem Town']],
                ],
            ],
            [
                'name' => 'South West Ethiopia Peoples',
                'slug' => 'south-west-ethiopia',
                'region' => 'South West Ethiopia Region',
                'sort_order' => 13,
                'sub_cities' => [
                    ['name' => 'Bonga', 'slug' => 'bonga', 'kebeles' => ['Bonga Town']],
                    ['name' => 'Mizan Teferi', 'slug' => 'mizan-teferi', 'kebeles' => ['Mizan Town']],
                ],
            ],
            [
                'name' => 'Central Ethiopia',
                'slug' => 'central-ethiopia',
                'region' => 'Central Ethiopia Region',
                'sort_order' => 14,
                'sub_cities' => [
                    ['name' => 'Gelan', 'slug' => 'gelan', 'kebeles' => ['Gelan Town']],
                ],
            ],
            [
                'name' => 'South Ethiopia',
                'slug' => 'south-ethiopia',
                'region' => 'South Ethiopia Region',
                'sort_order' => 15,
                'sub_cities' => [
                    ['name' => 'Jinka', 'slug' => 'jinka', 'kebeles' => ['Jinka Town']],
                ],
            ],
        ];

        foreach ($regions as $regionData) {
            $subCitiesData = $regionData['sub_cities'] ?? [];
            unset($regionData['sub_cities']);

            $city = City::firstOrCreate(['slug' => $regionData['slug']], array_merge($regionData, ['is_active' => true]));

            foreach ($subCitiesData as $scData) {
                $kebeles = $scData['kebeles'] ?? [];
                unset($scData['kebeles']);

                $sc = SubCity::firstOrCreate(['slug' => $scData['slug']], array_merge($scData, ['city_id' => $city->id, 'is_active' => true]));

                foreach ($kebeles as $kebeleName) {
                    $kebeleSlug = \Illuminate\Support\Str::slug($kebeleName . '-' . $sc->id);
                    Neighborhood::firstOrCreate(['slug' => $kebeleSlug], [
                        'sub_city_id' => $sc->id,
                        'name'        => $kebeleName,
                        'is_active'   => true,
                    ]);
                }
            }
        }

        $this->command->info('All 14 Ethiopian regions + 2 chartered cities seeded with zones/towns and kebeles.');
    }
}
