<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'House', 'slug' => 'house', 'icon' => 'house',
                'description' => 'Residential houses for sale or rent', 'sort_order' => 1,
                'categories' => ['Villa', 'Bungalow', 'Townhouse', 'Duplex', 'Semi-Detached'],
            ],
            [
                'name' => 'Apartment', 'slug' => 'apartment', 'icon' => 'building',
                'description' => 'Apartments and condominiums', 'sort_order' => 2,
                'categories' => ['Studio', 'Condo', '1 Bedroom', '2 Bedroom', '3 Bedroom', 'Penthouse'],
            ],
            [
                'name' => 'Land', 'slug' => 'land', 'icon' => 'map',
                'description' => 'Plots and land for sale', 'sort_order' => 3,
                'categories' => ['Residential Plot', 'Commercial Plot', 'Agricultural Land', 'Industrial Land'],
            ],
            [
                'name' => 'Commercial', 'slug' => 'commercial', 'icon' => 'store',
                'description' => 'Commercial properties', 'sort_order' => 4,
                'categories' => ['Shop', 'Showroom', 'Mall Space', 'Restaurant Space'],
            ],
            [
                'name' => 'Office', 'slug' => 'office', 'icon' => 'briefcase',
                'description' => 'Office spaces and buildings', 'sort_order' => 5,
                'categories' => ['Office Suite', 'Coworking Space', 'Office Building', 'Virtual Office'],
            ],
            [
                'name' => 'Warehouse', 'slug' => 'warehouse', 'icon' => 'warehouse',
                'description' => 'Warehouses and industrial spaces', 'sort_order' => 6,
                'categories' => ['Storage Warehouse', 'Distribution Center', 'Cold Storage', 'Industrial Unit'],
            ],
            [
                'name' => 'Hotel', 'slug' => 'hotel', 'icon' => 'hotel',
                'description' => 'Hotels and guest houses', 'sort_order' => 7,
                'categories' => ['Boutique Hotel', 'Guest House', 'Lodge', 'Resort'],
            ],
            [
                'name' => 'Short Rental', 'slug' => 'short-rental', 'icon' => 'calendar',
                'description' => 'Short-term rental properties (Airbnb-style)', 'sort_order' => 8,
                'categories' => ['Entire Home', 'Private Room', 'Shared Room', 'Serviced Apartment'],
            ],
        ];

        foreach ($types as $typeData) {
            $categories = $typeData['categories'];
            unset($typeData['categories']);

            $type = PropertyType::firstOrCreate(
                ['slug' => $typeData['slug']],
                array_merge($typeData, ['is_active' => true])
            );

            foreach ($categories as $catName) {
                $catSlug = \Illuminate\Support\Str::slug($catName . '-' . $type->id);
                Category::firstOrCreate(['slug' => $catSlug], [
                    'property_type_id' => $type->id,
                    'name'             => $catName,
                    'is_active'        => true,
                ]);
            }
        }

        $this->command->info('Property types and categories seeded.');
    }
}
