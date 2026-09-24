<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Appointment;
use App\Models\City;
use App\Models\Conversation;
use App\Models\Favorite;
use App\Models\Message;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyType;
use App\Models\SubCity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RealEstateDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or Find Property Owner Users
        $owner1 = User::firstOrCreate(
            ['email' => 'owner@betlink.et'],
            [
                'name'              => 'Abebe Kebede',
                'phone'             => '+251 91 123 4567',
                'password'          => Hash::make('Owner@123456'),
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
        if (!$owner1->hasRole('owner')) {
            $owner1->assignRole('owner');
        }

        // 2. Resolve All Property Types
        $houseType     = PropertyType::firstOrCreate(['slug' => 'house'], ['name' => 'House', 'icon' => 'house', 'sort_order' => 1]);
        $aptType       = PropertyType::firstOrCreate(['slug' => 'apartment'], ['name' => 'Apartment', 'icon' => 'building', 'sort_order' => 2]);
        $landType      = PropertyType::firstOrCreate(['slug' => 'land'], ['name' => 'Land', 'icon' => 'map', 'sort_order' => 3]);
        $commType      = PropertyType::firstOrCreate(['slug' => 'commercial'], ['name' => 'Commercial', 'icon' => 'store', 'sort_order' => 4]);
        $officeType    = PropertyType::firstOrCreate(['slug' => 'office'], ['name' => 'Office', 'icon' => 'briefcase', 'sort_order' => 5]);
        $warehouseType = PropertyType::firstOrCreate(['slug' => 'warehouse'], ['name' => 'Warehouse', 'icon' => 'warehouse', 'sort_order' => 6]);
        $hotelType     = PropertyType::firstOrCreate(['slug' => 'hotel'], ['name' => 'Hotel', 'icon' => 'hotel', 'sort_order' => 7]);
        $shortRentType = PropertyType::firstOrCreate(['slug' => 'short-rental'], ['name' => 'Short Rental', 'icon' => 'calendar', 'sort_order' => 8]);

        // Helper to find or create subcity for a city
        $getSubCityId = function ($citySlug, $subCitySlug, $subCityName) {
            $city = City::where('slug', $citySlug)->first();
            if (!$city) {
                $city = City::firstOrCreate(['slug' => $citySlug], ['name' => ucfirst(str_replace('-', ' ', $citySlug)), 'region' => 'Ethiopia']);
            }
            $sc = SubCity::where('city_id', $city->id)
                ->where(function ($q) use ($subCitySlug, $subCityName) {
                    $q->where('slug', $subCitySlug)
                      ->orWhere('name', 'like', "%{$subCityName}%");
                })->first();

            if (!$sc) {
                $sc = SubCity::create([
                    'city_id'   => $city->id,
                    'name'      => $subCityName,
                    'slug'      => $subCitySlug . '-' . $city->id,
                    'is_active' => true,
                ]);
            }
            return [$city->id, $sc->id];
        };

        // 3. Comprehensive Demo Properties Across ALL 15 Regions of Ethiopia
        $propertiesData = [
            // ── 1. Addis Ababa (city_id: 1) ───────────────────────────
            [
                'city_slug'        => 'addis-ababa',
                'sub_city_slug'    => 'bole',
                'sub_city_name'    => 'Bole',
                'property_type_id' => $aptType->id,
                'title'            => 'Modern Luxury Apartment in Bole Atlas',
                'slug'             => 'modern-luxury-apartment-bole-atlas',
                'description'      => 'Bright and spacious 3-bedroom apartment with scenic balcony views, backup generator, 24/7 security, and underground parking in Bole Atlas.',
                'listing_type'     => 'sale',
                'price'            => 4500000.00,
                'price_type'       => 'total',
                'bedrooms'         => 3,
                'bathrooms'        => 2,
                'area'             => 145.00,
                'is_featured'      => true,
                'street'           => 'Atlas Hotel Road, Bole',
                'image_url'        => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&q=80',
            ],
            [
                'city_slug'        => 'addis-ababa',
                'sub_city_slug'    => 'yeka',
                'sub_city_name'    => 'Yeka',
                'property_type_id' => $houseType->id,
                'title'            => 'Luxury Villa with Private Garden in CMC',
                'slug'             => 'luxury-villa-private-garden-cmc',
                'description'      => 'Prestigious 5-bedroom villa in a quiet gated community in CMC with a manicured garden, modern kitchen, and staff quarters.',
                'listing_type'     => 'sale',
                'price'            => 18500000.00,
                'price_type'       => 'total',
                'bedrooms'         => 5,
                'bathrooms'        => 4,
                'area'             => 380.00,
                'is_featured'      => true,
                'street'           => 'CMC Real Estate Phase 2',
                'image_url'        => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&q=80',
            ],
            [
                'city_slug'        => 'addis-ababa',
                'sub_city_slug'    => 'kirkos',
                'sub_city_name'    => 'Kirkos',
                'property_type_id' => $officeType->id,
                'title'            => 'Modern Executive Office Space in Kazanchis',
                'slug'             => 'modern-executive-office-space-kazanchis',
                'description'      => 'Fully partitioned modern office floor with conference rooms, optical fiber internet, centralized HVAC, and 24/7 security near UNECA.',
                'listing_type'     => 'rent',
                'price'            => 85000.00,
                'price_type'       => 'per_month',
                'bedrooms'         => 0,
                'bathrooms'        => 2,
                'area'             => 175.00,
                'is_featured'      => true,
                'street'           => 'ECA Conference Center Road, Kazanchis',
                'image_url'        => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&q=80',
            ],

            // ── 2. Dire Dawa (city_id: 2) ──────────────────────────────
            [
                'city_slug'        => 'dire-dawa',
                'sub_city_slug'    => 'dire-dawa-city',
                'sub_city_name'    => 'Kezira',
                'property_type_id' => $houseType->id,
                'title'            => 'Classic French-Colonial Style Villa in Kezira',
                'slug'             => 'classic-french-colonial-villa-kezira',
                'description'      => 'Spacious heritage residential home with high ceilings, private garden, wide verandas, and secure parking in prime Kezira, Dire Dawa.',
                'listing_type'     => 'sale',
                'price'            => 8900000.00,
                'price_type'       => 'total',
                'bedrooms'         => 4,
                'bathrooms'        => 3,
                'area'             => 320.00,
                'is_featured'      => true,
                'street'           => 'Railway Avenue, Kezira',
                'image_url'        => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&q=80',
            ],
            [
                'city_slug'        => 'dire-dawa',
                'sub_city_slug'    => 'dire-dawa-city',
                'sub_city_name'    => 'Sabian',
                'property_type_id' => $commType->id,
                'title'            => 'Prime Commercial Retail Space in Sabian',
                'slug'             => 'prime-commercial-retail-space-sabian',
                'description'      => 'High-foot-traffic commercial retail showroom suitable for banks, electronics distribution, or shopping center in Sabian.',
                'listing_type'     => 'rent',
                'price'            => 45000.00,
                'price_type'       => 'per_month',
                'bedrooms'         => 0,
                'bathrooms'        => 2,
                'area'             => 180.00,
                'is_featured'      => false,
                'street'           => 'Sabian Main Commercial Corridor',
                'image_url'        => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&q=80',
            ],

            // ── 3. Tigray (city_id: 3) ─────────────────────────────────
            [
                'city_slug'        => 'tigray',
                'sub_city_slug'    => 'mekelle',
                'sub_city_name'    => 'Mekelle',
                'property_type_id' => $houseType->id,
                'title'            => 'Modern 4-Bedroom Family Villa in Hawelti, Mekelle',
                'slug'             => 'modern-4-bedroom-villa-hawelti-mekelle',
                'description'      => 'Elegant newly constructed 4-bedroom villa with solar backup power, landscaped compound, paved driveway, and perimeter security in Hawelti.',
                'listing_type'     => 'sale',
                'price'            => 11500000.00,
                'price_type'       => 'total',
                'bedrooms'         => 4,
                'bathrooms'        => 3,
                'area'             => 280.00,
                'is_featured'      => true,
                'street'           => 'Hawelti Ring Road, Mekelle',
                'image_url'        => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&q=80',
            ],
            [
                'city_slug'        => 'tigray',
                'sub_city_slug'    => 'axum',
                'sub_city_name'    => 'Axum',
                'property_type_id' => $hotelType->id,
                'title'            => 'Historic Boutique Hotel & Lodge in Axum',
                'slug'             => 'historic-boutique-hotel-lodge-axum',
                'description'      => 'Charming 10-room operational hospitality property with traditional stone architecture, dining hall, and garden terrace close to Axum obelisks.',
                'listing_type'     => 'sale',
                'price'            => 28000000.00,
                'price_type'       => 'total',
                'bedrooms'         => 10,
                'bathrooms'        => 10,
                'area'             => 650.00,
                'is_featured'      => true,
                'street'           => 'Heritage Avenue, Axum',
                'image_url'        => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80',
            ],

            // ── 4. Afar (city_id: 4) ───────────────────────────────────
            [
                'city_slug'        => 'afar',
                'sub_city_slug'    => 'semera',
                'sub_city_name'    => 'Semera',
                'property_type_id' => $houseType->id,
                'title'            => 'Spacious Modern Villa Compound in Semera',
                'slug'             => 'spacious-modern-villa-compound-semera',
                'description'      => 'Thermally insulated modern 3-bedroom villa with full air conditioning, deep water well, solar power system, and large shaded compound in Semera.',
                'listing_type'     => 'sale',
                'price'            => 6500000.00,
                'price_type'       => 'total',
                'bedrooms'         => 3,
                'bathrooms'        => 2,
                'area'             => 350.00,
                'is_featured'      => true,
                'street'           => 'Semera University Road, Semera',
                'image_url'        => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800&q=80',
            ],
            [
                'city_slug'        => 'afar',
                'sub_city_slug'    => 'awash',
                'sub_city_name'    => 'Awash',
                'property_type_id' => $commType->id,
                'title'            => 'Highway Commercial Service & Logistics Hub in Awash',
                'slug'             => 'highway-commercial-service-hub-awash',
                'description'      => 'Strategic commercial property located along the Addis-Djibouti import/export corridor, featuring large parking apron and warehouses.',
                'listing_type'     => 'rent',
                'price'            => 75000.00,
                'price_type'       => 'per_month',
                'bedrooms'         => 0,
                'bathrooms'        => 2,
                'area'             => 800.00,
                'is_featured'      => false,
                'street'           => 'Addis-Djibouti Highway, Awash',
                'image_url'        => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&q=80',
            ],

            // ── 5. Amhara (city_id: 5) ─────────────────────────────────
            [
                'city_slug'        => 'amhara',
                'sub_city_slug'    => 'bahir-dar',
                'sub_city_name'    => 'Bahir Dar',
                'property_type_id' => $houseType->id,
                'title'            => 'Lake Tana Waterfront Luxury Villa in Bahir Dar',
                'slug'             => 'lake-tana-waterfront-villa-bahir-dar',
                'description'      => 'Scenic 4-bedroom lakefront luxury villa with panoramic water views, palm garden, private jetty access, and top-tier finishes in Bahir Dar.',
                'listing_type'     => 'sale',
                'price'            => 16500000.00,
                'price_type'       => 'total',
                'bedrooms'         => 4,
                'bathrooms'        => 4,
                'area'             => 420.00,
                'is_featured'      => true,
                'street'           => 'Lake Tana Shoreline Boulevard, Bahir Dar',
                'image_url'        => 'https://images.unsplash.com/photo-1613977257363-707ba9348227?w=800&q=80',
            ],
            [
                'city_slug'        => 'amhara',
                'sub_city_slug'    => 'gondar',
                'sub_city_name'    => 'Gondar',
                'property_type_id' => $aptType->id,
                'title'            => 'Modern Apartment with Castle View in Gondar',
                'slug'             => 'modern-apartment-castle-view-gondar',
                'description'      => 'Stylish 3-bedroom apartment overlooking the historic Fasil Ghebbi royal enclosure with modern kitchen, backup water, and secure parking.',
                'listing_type'     => 'rent',
                'price'            => 28000.00,
                'price_type'       => 'per_month',
                'bedrooms'         => 3,
                'bathrooms'        => 2,
                'area'             => 130.00,
                'is_featured'      => false,
                'street'           => 'Fasil Castle Viewpoint, Gondar',
                'image_url'        => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
            ],

            // ── 6. Oromia (city_id: 6) ─────────────────────────────────
            [
                'city_slug'        => 'oromia',
                'sub_city_slug'    => 'bishoftu',
                'sub_city_name'    => 'Bishoftu',
                'property_type_id' => $shortRentType->id,
                'title'            => 'Lake Kuriftu Vacation Villa & Resort in Bishoftu',
                'slug'             => 'lake-kuriftu-vacation-villa-bishoftu',
                'description'      => 'Stunning vacation retreat overlooking Lake Kuriftu with private swimming pool, barbecue deck, landscaped lawns, and 24/7 security.',
                'listing_type'     => 'short_rent',
                'price'            => 12500.00,
                'price_type'       => 'per_night',
                'bedrooms'         => 4,
                'bathrooms'        => 4,
                'area'             => 310.00,
                'is_featured'      => true,
                'street'           => 'Lake Kuriftu Promenade, Bishoftu',
                'image_url'        => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&q=80',
            ],
            [
                'city_slug'        => 'oromia',
                'sub_city_slug'    => 'adama',
                'sub_city_name'    => 'Adama',
                'property_type_id' => $houseType->id,
                'title'            => 'Contemporary 4-Bedroom Villa in Adama Express Zone',
                'slug'             => 'contemporary-4-bedroom-villa-adama',
                'description'      => 'Modern residence situated near the Addis-Adama Expressway tollway with private compound, fruit garden, and marble flooring.',
                'listing_type'     => 'sale',
                'price'            => 9800000.00,
                'price_type'       => 'total',
                'bedrooms'         => 4,
                'bathrooms'        => 3,
                'area'             => 260.00,
                'is_featured'      => true,
                'street'           => 'Expressway Access Road, Adama',
                'image_url'        => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
            ],

            // ── 7. Somali (city_id: 7) ─────────────────────────────────
            [
                'city_slug'        => 'somali',
                'sub_city_slug'    => 'jigjiga',
                'sub_city_name'    => 'Jigjiga',
                'property_type_id' => $houseType->id,
                'title'            => 'Executive Residential Villa in Jigjiga Central',
                'slug'             => 'executive-residential-villa-jigjiga-central',
                'description'      => 'Prestigious 5-bedroom villa compound with high security perimeter, solar power installation, modern ceramic finishes, and water storage in Jigjiga.',
                'listing_type'     => 'sale',
                'price'            => 10500000.00,
                'price_type'       => 'total',
                'bedrooms'         => 5,
                'bathrooms'        => 4,
                'area'             => 360.00,
                'is_featured'      => true,
                'street'           => 'Jigjiga University Boulevard, Jigjiga',
                'image_url'        => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&q=80',
            ],

            // ── 8. Benishangul-Gumuz (city_id: 8) ──────────────────────
            [
                'city_slug'        => 'benishangul-gumuz',
                'sub_city_slug'    => 'asosa',
                'sub_city_name'    => 'Asosa',
                'property_type_id' => $houseType->id,
                'title'            => 'Spacious Residential Compound & Villa in Asosa',
                'slug'             => 'spacious-residential-compound-villa-asosa',
                'description'      => 'Beautiful 3-bedroom residence with large green compound, water reserve tanks, solar backup, and title deed in Asosa capital zone.',
                'listing_type'     => 'sale',
                'price'            => 5800000.00,
                'price_type'       => 'total',
                'bedrooms'         => 3,
                'bathrooms'        => 2,
                'area'             => 400.00,
                'is_featured'      => true,
                'street'           => 'Asosa Central Hospital Road, Asosa',
                'image_url'        => 'https://images.unsplash.com/photo-1598228723793-52759bba239c?w=800&q=80',
            ],
            [
                'city_slug'        => 'benishangul-gumuz',
                'sub_city_slug'    => 'asosa',
                'sub_city_name'    => 'Asosa',
                'property_type_id' => $landType->id,
                'title'            => 'Prime Shovel-Ready Commercial Plot in Asosa',
                'slug'             => 'prime-commercial-plot-asosa',
                'description'      => '800 sqm prime corner plot with clean municipal title deed suitable for hotels, apartments, or commercial centers in Asosa.',
                'listing_type'     => 'sale',
                'price'            => 3800000.00,
                'price_type'       => 'total',
                'bedrooms'         => 0,
                'bathrooms'        => 0,
                'area'             => 800.00,
                'is_featured'      => false,
                'street'           => 'Asosa Airport Access Highway, Asosa',
                'image_url'        => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&q=80',
            ],

            // ── 9. SNNP (city_id: 9) ───────────────────────────────────
            [
                'city_slug'        => 'snnp',
                'sub_city_slug'    => 'hawassa',
                'sub_city_name'    => 'Hawassa',
                'property_type_id' => $houseType->id,
                'title'            => 'Lake Hawassa View Modern Villa Compound',
                'slug'             => 'lake-hawassa-view-modern-villa',
                'description'      => 'Luxurious 4-bedroom villa with Lake Hawassa sunset vistas, lush tropical garden, high perimeter walls, and double car garage.',
                'listing_type'     => 'sale',
                'price'            => 14200000.00,
                'price_type'       => 'total',
                'bedrooms'         => 4,
                'bathrooms'        => 3,
                'area'             => 320.00,
                'is_featured'      => true,
                'street'           => 'Piazza / Lake View Road, Hawassa',
                'image_url'        => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&q=80',
            ],

            // ── 10. Gambela (city_id: 10) ──────────────────────────────
            [
                'city_slug'        => 'gambela',
                'sub_city_slug'    => 'gambela-city',
                'sub_city_name'    => 'Gambela City',
                'property_type_id' => $houseType->id,
                'title'            => 'Baro Riverfront Guesthouse & Residence in Gambela',
                'slug'             => 'baro-riverfront-guesthouse-gambela',
                'description'      => 'Scenic 4-bedroom residence located along the Baro riverbank with cool verandas, solar energy system, and large shaded mango tree garden.',
                'listing_type'     => 'sale',
                'price'            => 6200000.00,
                'price_type'       => 'total',
                'bedrooms'         => 4,
                'bathrooms'        => 3,
                'area'             => 450.00,
                'is_featured'      => true,
                'street'           => 'Baro River Promenade, Gambela',
                'image_url'        => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&q=80',
            ],

            // ── 11. Harari (city_id: 11) ───────────────────────────────
            [
                'city_slug'        => 'harari',
                'sub_city_slug'    => 'harar',
                'sub_city_name'    => 'Harar',
                'property_type_id' => $houseType->id,
                'title'            => 'Historic Heritage Villa near Jugol in Harar',
                'slug'             => 'historic-heritage-villa-jugol-harar',
                'description'      => 'Authentic Harari architectural home featuring intricately decorated Gidír Gár living room, tranquil courtyard, and modern plumbing/electricity.',
                'listing_type'     => 'sale',
                'price'            => 8400000.00,
                'price_type'       => 'total',
                'bedrooms'         => 4,
                'bathrooms'        => 2,
                'area'             => 260.00,
                'is_featured'      => true,
                'street'           => 'Shoa Gate, Historic Jugol, Harar',
                'image_url'        => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&q=80',
            ],

            // ── 12. Sidama (city_id: 12) ───────────────────────────────
            [
                'city_slug'        => 'sidama',
                'sub_city_slug'    => 'hawassa',
                'sub_city_name'    => 'Hawassa',
                'property_type_id' => $aptType->id,
                'title'            => 'Modern Serviced Apartment in Hawassa Millennium',
                'slug'             => 'modern-serviced-apartment-hawassa-millennium',
                'description'      => 'Contemporary 3-bedroom apartment with elevator, backup generator, solar water heating, and proximity to Hawassa Industrial Park.',
                'listing_type'     => 'rent',
                'price'            => 32000.00,
                'price_type'       => 'per_month',
                'bedrooms'         => 3,
                'bathrooms'        => 2,
                'area'             => 140.00,
                'is_featured'      => true,
                'street'           => 'Millennium Subcity, Hawassa',
                'image_url'        => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&q=80',
            ],

            // ── 13. South West Ethiopia (city_id: 13) ──────────────────
            [
                'city_slug'        => 'south-west-ethiopia',
                'sub_city_slug'    => 'bonga',
                'sub_city_name'    => 'Bonga',
                'property_type_id' => $houseType->id,
                'title'            => 'Kaffa Highland Eco-Residence & Garden in Bonga',
                'slug'             => 'kaffa-highland-eco-residence-bonga',
                'description'      => 'Eco-friendly residence nestled in the lush Kaffa biosphere with mountain spring water, coffee plantation garden, and solar power.',
                'listing_type'     => 'sale',
                'price'            => 5200000.00,
                'price_type'       => 'total',
                'bedrooms'         => 3,
                'bathrooms'        => 2,
                'area'             => 380.00,
                'is_featured'      => true,
                'street'           => 'Kaffa Coffee Biosphere Road, Bonga',
                'image_url'        => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&q=80',
            ],

            // ── 14. Central Ethiopia (city_id: 14) ─────────────────────
            [
                'city_slug'        => 'central-ethiopia',
                'sub_city_slug'    => 'butajira',
                'sub_city_name'    => 'Butajira',
                'property_type_id' => $houseType->id,
                'title'            => 'Spacious Gurage Foothill Villa in Butajira',
                'slug'             => 'gurage-foothill-villa-butajira',
                'description'      => 'Newly built 4-bedroom villa with ensete garden, perimeter wall, asphalt road frontage, and clean title deed in Butajira.',
                'listing_type'     => 'sale',
                'price'            => 6900000.00,
                'price_type'       => 'total',
                'bedrooms'         => 4,
                'bathrooms'        => 3,
                'area'             => 300.00,
                'is_featured'      => true,
                'street'           => 'Mount Gurage Scenic Highway, Butajira',
                'image_url'        => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
            ],

            // ── 15. South Ethiopia (city_id: 15) ───────────────────────
            [
                'city_slug'        => 'south-ethiopia',
                'sub_city_slug'    => 'arba-minch',
                'sub_city_name'    => 'Arba Minch',
                'property_type_id' => $hotelType->id,
                'title'            => 'Abaya & Chamo Lake View Resort Villa in Arba Minch',
                'slug'             => 'abaya-chamo-lake-view-villa-arba-minch',
                'description'      => 'Magnificent villa overlooking the Bridge of God between Lake Abaya and Lake Chamo with infinity deck and tropical fruit garden.',
                'listing_type'     => 'sale',
                'price'            => 17800000.00,
                'price_type'       => 'total',
                'bedrooms'         => 5,
                'bathrooms'        => 5,
                'area'             => 480.00,
                'is_featured'      => true,
                'street'           => 'Nechisar National Park Road, Arba Minch',
                'image_url'        => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800&q=80',
            ],
        ];

        foreach ($propertiesData as $pData) {
            $imgUrl        = $pData['image_url'];
            $citySlug      = $pData['city_slug'];
            $subCitySlug   = $pData['sub_city_slug'];
            $subCityName   = $pData['sub_city_name'];
            $street        = $pData['street'];

            unset($pData['image_url'], $pData['city_slug'], $pData['sub_city_slug'], $pData['sub_city_name'], $pData['street']);

            [$cityId, $subCityId] = $getSubCityId($citySlug, $subCitySlug, $subCityName);

            $pData['user_id']     = $owner1->id;
            $pData['status']      = 'active';
            $pData['is_verified'] = true;
            $pData['currency']    = 'ETB';

            $slug = $pData['slug'];
            $property = Property::where('slug', $slug)->first();

            if (!$property) {
                $property = Property::create($pData);

                Address::create([
                    'addressable_type' => Property::class,
                    'addressable_id'   => $property->id,
                    'city_id'          => $cityId,
                    'sub_city_id'      => $subCityId,
                    'street'           => $street,
                    'full_address'     => $street . ', Ethiopia',
                ]);

                PropertyImage::create([
                    'property_id' => $property->id,
                    'url'         => $imgUrl,
                    'is_primary'  => true,
                    'sort_order'  => 1,
                ]);
            } else {
                $property->update($pData);

                Address::updateOrCreate(
                    [
                        'addressable_type' => Property::class,
                        'addressable_id'   => $property->id,
                    ],
                    [
                        'city_id'      => $cityId,
                        'sub_city_id'  => $subCityId,
                        'street'       => $street,
                        'full_address' => $street . ', Ethiopia',
                    ]
                );
            }
        }

        // 4. Seed Tour Appointments across multiple months
        $allProps = Property::all();
        $buyerUsers = User::all()->filter(function ($u) {
            return $u->hasAnyRole(['buyer', 'renter', 'buyer_tenant', 'Buyer / Tenant']) || $u->id >= 1;
        });

        $buyer = $buyerUsers->first() ?? User::first();

        if ($allProps->count() > 0 && $buyer) {
            $appointmentTimes = [
                ['days' => 2, 'subMonths' => 0, 'status' => 'pending', 'type' => 'in_person'],
                ['days' => 4, 'subMonths' => 0, 'status' => 'confirmed', 'type' => 'virtual'],
                ['days' => 10, 'subMonths' => 1, 'status' => 'completed', 'type' => 'in_person'],
                ['days' => 15, 'subMonths' => 2, 'status' => 'completed', 'type' => 'in_person'],
            ];

            foreach ($appointmentTimes as $idx => $at) {
                $prop = $allProps[$idx % $allProps->count()];
                Appointment::firstOrCreate(
                    [
                        'visitor_id'  => $buyer->id,
                        'property_id' => $prop->id,
                        'owner_id'    => $owner1->id,
                    ],
                    [
                        'scheduled_at' => now()->subMonths($at['subMonths'])->addDays($at['days'])->setHour(10)->setMinute(0),
                        'status'       => $at['status'],
                        'type'         => $at['type'],
                        'message'      => 'Viewing request for this property.',
                        'created_at'   => now()->subMonths($at['subMonths']),
                    ]
                );
            }
        }

        // 5. Seed Inquiry Conversations
        if ($allProps->count() > 0 && $buyer) {
            $conv = Conversation::firstOrCreate(
                ['property_id' => $allProps[0]->id],
                ['type' => 'inquiry', 'created_at' => now()]
            );

            $conv->participants()->syncWithoutDetaching([
                $buyer->id   => ['last_read_at' => now()],
                $owner1->id  => ['last_read_at' => null],
            ]);

            Message::firstOrCreate(
                [
                    'conversation_id' => $conv->id,
                    'sender_id'       => $buyer->id,
                ],
                [
                    'body'       => 'Hello! Is this property title deed ready and price negotiable?',
                    'type'       => 'text',
                    'created_at' => now()->subHours(2),
                ]
            );
        }

        $this->command->info('Real estate demo properties seeded across all 15 Ethiopian regions.');
    }
}
