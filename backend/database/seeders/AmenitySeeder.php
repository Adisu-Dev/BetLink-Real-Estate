<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            // Indoor
            ['name' => 'WiFi', 'icon' => 'wifi', 'category' => 'indoor'],
            ['name' => 'Air Conditioning', 'icon' => 'wind', 'category' => 'indoor'],
            ['name' => 'Heating', 'icon' => 'thermometer', 'category' => 'indoor'],
            ['name' => 'Fully Furnished', 'icon' => 'sofa', 'category' => 'indoor'],
            ['name' => 'Kitchen', 'icon' => 'chef-hat', 'category' => 'indoor'],
            ['name' => 'Washing Machine', 'icon' => 'shirt', 'category' => 'indoor'],
            ['name' => 'Refrigerator', 'icon' => 'box', 'category' => 'indoor'],
            ['name' => 'TV', 'icon' => 'tv', 'category' => 'indoor'],
            ['name' => 'Study Room', 'icon' => 'book', 'category' => 'indoor'],
            ['name' => 'Storage Room', 'icon' => 'archive', 'category' => 'indoor'],
            // Outdoor
            ['name' => 'Swimming Pool', 'icon' => 'waves', 'category' => 'outdoor'],
            ['name' => 'Garden', 'icon' => 'tree', 'category' => 'outdoor'],
            ['name' => 'Balcony', 'icon' => 'building-2', 'category' => 'outdoor'],
            ['name' => 'Terrace', 'icon' => 'sun', 'category' => 'outdoor'],
            ['name' => 'Parking', 'icon' => 'car', 'category' => 'outdoor'],
            ['name' => 'Gym', 'icon' => 'dumbbell', 'category' => 'outdoor'],
            ['name' => 'Playground', 'icon' => 'playground', 'category' => 'outdoor'],
            // Security
            ['name' => 'CCTV', 'icon' => 'camera', 'category' => 'security'],
            ['name' => 'Security Guard', 'icon' => 'shield', 'category' => 'security'],
            ['name' => 'Gated Community', 'icon' => 'lock', 'category' => 'security'],
            ['name' => 'Intercom', 'icon' => 'phone', 'category' => 'security'],
            ['name' => 'Fire Safety', 'icon' => 'flame', 'category' => 'security'],
            // Utilities
            ['name' => 'Generator', 'icon' => 'zap', 'category' => 'utilities'],
            ['name' => 'Solar Power', 'icon' => 'sun', 'category' => 'utilities'],
            ['name' => 'Water Tank', 'icon' => 'droplet', 'category' => 'utilities'],
            ['name' => 'Borehole', 'icon' => 'droplets', 'category' => 'utilities'],
            ['name' => 'Elevator', 'icon' => 'arrow-up-down', 'category' => 'utilities'],
            ['name' => 'Disability Access', 'icon' => 'accessibility', 'category' => 'utilities'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::firstOrCreate(
                ['slug' => Str::slug($amenity['name'])],
                array_merge($amenity, ['is_active' => true])
            );
        }

        $this->command->info('Amenities seeded (' . count($amenities) . ' items).');
    }
}
