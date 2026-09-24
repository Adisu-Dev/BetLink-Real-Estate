<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'BetLink', 'type' => 'string', 'group' => 'general', 'description' => 'Site name'],
            ['key' => 'site_tagline', 'value' => "Ethiopia's #1 Property Marketplace", 'type' => 'string', 'group' => 'general', 'description' => 'Tagline'],
            ['key' => 'site_email', 'value' => 'info@betlink.et', 'type' => 'string', 'group' => 'general', 'description' => 'Contact email'],
            ['key' => 'site_phone', 'value' => '+251911000000', 'type' => 'string', 'group' => 'general', 'description' => 'Contact phone'],
            ['key' => 'currency', 'value' => 'ETB', 'type' => 'string', 'group' => 'general', 'description' => 'Default currency'],
            ['key' => 'currency_symbol', 'value' => 'Br', 'type' => 'string', 'group' => 'general', 'description' => 'Currency symbol'],
            // Property
            ['key' => 'max_images_per_property', 'value' => '20', 'type' => 'integer', 'group' => 'property', 'description' => 'Max images per listing'],
            ['key' => 'property_approval_required', 'value' => '1', 'type' => 'boolean', 'group' => 'property', 'description' => 'Require admin approval before publishing'],
            ['key' => 'featured_properties_count', 'value' => '12', 'type' => 'integer', 'group' => 'property', 'description' => 'Number of featured properties on homepage'],
            ['key' => 'property_expiry_days', 'value' => '90', 'type' => 'integer', 'group' => 'property', 'description' => 'Days before listing expires'],
            // Booking
            ['key' => 'service_fee_percentage', 'value' => '5', 'type' => 'integer', 'group' => 'booking', 'description' => 'Short rental service fee %'],
            ['key' => 'appointment_duration_default', 'value' => '30', 'type' => 'integer', 'group' => 'booking', 'description' => 'Default appointment duration (minutes)'],
            // SEO
            ['key' => 'meta_title', 'value' => 'BetLink — Find Your Dream Property in Ethiopia', 'type' => 'string', 'group' => 'seo', 'description' => 'Default meta title'],
            ['key' => 'meta_description', 'value' => 'Buy, sell, and rent properties across Ethiopia on BetLink — the #1 property marketplace.', 'type' => 'string', 'group' => 'seo', 'description' => 'Default meta description'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        $this->command->info('System settings seeded.');
    }
}
