<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Amenity;
use App\Models\Category;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyType;
use App\Models\SubCity;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\AmenitySeeder;
use Database\Seeders\EthiopianRegionsSeeder;
use Database\Seeders\PropertyTypeSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SystemSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $ownerUser;
    protected User $buyerUser;
    protected Property $saleProperty;
    protected Property $shortRentProperty;

    protected function setUp(): void
    {
        parent::setUp();

        // Run all core seeders
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(EthiopianRegionsSeeder::class);
        $this->seed(PropertyTypeSeeder::class);
        $this->seed(AmenitySeeder::class);
        $this->seed(SystemSettingsSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $this->adminUser = User::where('email', 'admin@betlink.et')->firstOrFail();
        $this->ownerUser = User::where('email', 'owner@betlink.et')->firstOrFail();
        $this->buyerUser = User::where('email', 'buyer@betlink.et')->firstOrFail();

        $city = City::first();
        $subCity = SubCity::where('city_id', $city->id)->first();
        $neighborhood = Neighborhood::where('sub_city_id', $subCity->id)->first();
        $propertyType = PropertyType::first();
        $category = Category::where('property_type_id', $propertyType->id)->first() ?? Category::first();

        // Create Sale Property
        $this->saleProperty = Property::create([
            'user_id'          => $this->ownerUser->id,
            'property_type_id' => $propertyType->id,
            'category_id'      => $category->id,
            'title'            => 'Luxury Villa in Bole',
            'slug'             => 'luxury-villa-in-bole',
            'description'      => 'A stunning luxury villa located in prime Bole area.',
            'listing_type'     => 'sale',
            'price'            => 15000000.00,
            'price_type'       => 'total',
            'currency'         => 'ETB',
            'bedrooms'         => 4,
            'bathrooms'        => 3,
            'area'             => 350.00,
            'area_unit'        => 'sqm',
            'status'           => 'active',
            'is_featured'      => true,
            'is_verified'      => true,
            'published_at'     => now(),
        ]);

        Address::create([
            'addressable_type' => Property::class,
            'addressable_id'   => $this->saleProperty->id,
            'city_id'          => $city->id,
            'sub_city_id'      => $subCity->id,
            'neighborhood_id'  => $neighborhood?->id,
            'street'           => 'Bole Road',
            'full_address'     => 'Bole, Addis Ababa, Ethiopia',
        ]);

        PropertyImage::create([
            'property_id' => $this->saleProperty->id,
            'url'         => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6',
            'is_primary'  => true,
            'sort_order'  => 1,
        ]);

        $amenities = Amenity::take(3)->pluck('id');
        $this->saleProperty->amenities()->attach($amenities);

        // Create Short Rent Property
        $this->shortRentProperty = Property::create([
            'user_id'          => $this->ownerUser->id,
            'property_type_id' => $propertyType->id,
            'category_id'      => $category->id,
            'title'            => 'Furnished Studio in Kazanchis',
            'slug'             => 'furnished-studio-in-kazanchis',
            'description'      => 'Comfortable short stay studio apartment.',
            'listing_type'     => 'short_rent',
            'price'            => 3500.00,
            'price_type'       => 'per_night',
            'currency'         => 'ETB',
            'bedrooms'         => 1,
            'bathrooms'        => 1,
            'area'             => 55.00,
            'area_unit'        => 'sqm',
            'status'           => 'active',
            'is_featured'      => false,
            'is_verified'      => true,
            'published_at'     => now(),
        ]);

        Address::create([
            'addressable_type' => Property::class,
            'addressable_id'   => $this->shortRentProperty->id,
            'city_id'          => $city->id,
            'sub_city_id'      => $subCity->id,
            'neighborhood_id'  => $neighborhood?->id,
            'street'           => 'Kazanchis Main',
            'full_address'     => 'Kazanchis, Addis Ababa, Ethiopia',
        ]);
    }

    public function test_users_and_roles_seeded_correctly(): void
    {
        $this->assertTrue($this->adminUser->hasRole('admin'));
        $this->assertTrue($this->ownerUser->hasRole('owner'));
        $this->assertTrue($this->buyerUser->hasRole('buyer'));
        $this->assertGreaterThanOrEqual(3, User::count());
    }

    public function test_property_relationships_integrity(): void
    {
        $property = Property::with(['owner', 'propertyType', 'category', 'address.city', 'images', 'amenities'])->find($this->saleProperty->id);
        $this->assertNotNull($property->owner);
        $this->assertNotNull($property->propertyType);
        $this->assertNotNull($property->category);
        $this->assertNotNull($property->address);
        $this->assertNotNull($property->address->city);
        $this->assertCount(1, $property->images);
        $this->assertCount(3, $property->amenities);
    }

    public function test_public_get_properties_list(): void
    {
        $response = $this->getJson('/api/v1/properties');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'slug', 'listing_type', 'price', 'status']
                ],
                'meta' => [
                    'current_page',
                    'total',
                ]
            ]);
    }

    public function test_public_get_featured_properties(): void
    {
        $response = $this->getJson('/api/v1/properties/featured');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'slug', 'is_featured']
                ]
            ]);
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }

    public function test_public_get_property_by_slug(): void
    {
        $response = $this->getJson('/api/v1/properties/' . $this->saleProperty->slug);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'title', 'slug', 'price', 'owner', 'address', 'images', 'amenities']
            ]);
    }

    public function test_public_get_property_not_found_returns_404(): void
    {
        $response = $this->getJson('/api/v1/properties/non-existent-property-slug-12345');
        $response->assertStatus(404);
    }

    public function test_public_search_properties(): void
    {
        $response = $this->getJson('/api/v1/search?q=Bole');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_public_search_suggestions(): void
    {
        $response = $this->getJson('/api/v1/search/suggestions?q=Bole');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_public_get_cities(): void
    {
        $response = $this->getJson('/api/v1/locations/cities');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'slug']
                ]
            ]);
    }

    public function test_public_property_reviews(): void
    {
        $response = $this->getJson('/api/v1/properties/' . $this->saleProperty->id . '/reviews');
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_public_property_availability(): void
    {
        $response = $this->getJson('/api/v1/properties/' . $this->shortRentProperty->id . '/availability');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['availability', 'booked_dates']
            ]);
    }

    public function test_buyer_login_and_auth_me(): void
    {
        $loginRes = $this->postJson('/api/v1/auth/login', [
            'email'    => 'buyer@betlink.et',
            'password' => 'Buyer@123456',
        ]);
        $loginRes->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['token', 'user' => ['id', 'name', 'email', 'roles']]
            ]);

        $token = $loginRes->json('data.token');

        $meRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');
        $meRes->assertStatus(200)
            ->assertJsonPath('data.email', 'buyer@betlink.et');
    }

    public function test_favorite_lifecycle_authenticated(): void
    {
        $token = $this->buyerUser->createToken('test_token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        // 1. Check favorite (false)
        $checkRes1 = $this->withHeaders($headers)
            ->getJson('/api/v1/favorites/' . $this->saleProperty->id . '/check');
        $checkRes1->assertStatus(200)
            ->assertJsonPath('data.is_favorited', false);

        // 2. Add favorite
        $addRes = $this->withHeaders($headers)
            ->postJson('/api/v1/favorites/' . $this->saleProperty->id);
        $addRes->assertStatus(201);

        // 3. Check favorite (true)
        $checkRes2 = $this->withHeaders($headers)
            ->getJson('/api/v1/favorites/' . $this->saleProperty->id . '/check');
        $checkRes2->assertStatus(200)
            ->assertJsonPath('data.is_favorited', true);

        // 4. List favorites
        $listRes = $this->withHeaders($headers)
            ->getJson('/api/v1/favorites');
        $listRes->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);

        // 5. Remove favorite
        $delRes = $this->withHeaders($headers)
            ->deleteJson('/api/v1/favorites/' . $this->saleProperty->id);
        $delRes->assertStatus(200);

        // 6. Check favorite (false)
        $checkRes3 = $this->withHeaders($headers)
            ->getJson('/api/v1/favorites/' . $this->saleProperty->id . '/check');
        $checkRes3->assertStatus(200)
            ->assertJsonPath('data.is_favorited', false);
    }

    public function test_review_submission_authenticated(): void
    {
        $token = $this->buyerUser->createToken('test_token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $res = $this->withHeaders($headers)
            ->postJson('/api/v1/properties/' . $this->saleProperty->id . '/reviews', [
                'rating' => 5,
                'title'  => 'Fantastic Villa',
                'body'   => 'Loved the spacious layout and prime location in Bole.',
                'pros'   => 'Spacious, secure',
                'cons'   => 'None',
            ]);

        $res->assertStatus(201)
            ->assertJsonPath('data.rating', 5)
            ->assertJsonPath('data.title', 'Fantastic Villa');
    }

    public function test_appointment_booking_authenticated(): void
    {
        $token = $this->buyerUser->createToken('test_token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $res = $this->withHeaders($headers)
            ->postJson('/api/v1/appointments', [
                'property_id'  => $this->saleProperty->id,
                'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'type'         => 'in_person',
                'notes'        => 'Looking forward to viewing the villa.',
            ]);

        $res->assertStatus(201)
            ->assertJsonStructure(['success', 'data' => ['id', 'property_id', 'status']]);
    }

    public function test_conversation_inquiry_authenticated(): void
    {
        $token = $this->buyerUser->createToken('test_token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $res = $this->withHeaders($headers)
            ->postJson('/api/v1/conversations', [
                'property_id'     => $this->saleProperty->id,
                'recipient_id'    => $this->ownerUser->id,
                'initial_message' => 'Hello, is the price negotiable for cash payment?',
            ]);

        $res->assertStatus(201)
            ->assertJsonStructure(['success', 'data' => ['id', 'property_id', 'messages']]);
    }

    public function test_short_stay_booking_authenticated(): void
    {
        $token = $this->buyerUser->createToken('test_token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $res = $this->withHeaders($headers)
            ->postJson('/api/v1/bookings', [
                'property_id'    => $this->shortRentProperty->id,
                'check_in_date'  => now()->addDays(1)->format('Y-m-d'),
                'check_out_date' => now()->addDays(4)->format('Y-m-d'),
                'guests_count'   => 2,
                'special_requests' => 'Late check-in requested.',
            ]);

        $res->assertStatus(201)
            ->assertJsonPath('data.nights', 3)
            ->assertJsonPath('data.status', 'pending');
    }

    public function test_protected_routes_reject_guests_with_401(): void
    {
        $this->postJson('/api/v1/favorites/1')->assertStatus(401);
        $this->getJson('/api/v1/favorites')->assertStatus(401);
        $this->postJson('/api/v1/properties/1/reviews', ['rating' => 5])->assertStatus(401);
        $this->postJson('/api/v1/appointments', ['property_id' => 1])->assertStatus(401);
        $this->postJson('/api/v1/conversations', ['property_id' => 1])->assertStatus(401);
        $this->postJson('/api/v1/bookings', ['property_id' => 1])->assertStatus(401);
    }
}
