<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $settings = SystemSetting::orderBy('group')->orderBy('key')->get()
            ->groupBy('group');

        return $this->success($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings'         => ['required', 'array'],
            'settings.*.key'   => ['required', 'string'],
            'settings.*.value' => ['nullable'],
        ]);

        $keyed = collect($request->settings)->pluck('value', 'key');

        if ($keyed->has('site_name') && empty(trim($keyed['site_name'] ?? ''))) {
            return $this->error('Platform name cannot be empty.', 422);
        }
        if ($keyed->has('site_email') && !filter_var($keyed['site_email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            return $this->error('A valid support email address is required.', 422);
        }
        if ($keyed->has('max_images_per_property') && ((int)($keyed['max_images_per_property'] ?? 0) < 1 || (int)($keyed['max_images_per_property'] ?? 0) > 50)) {
            return $this->error('Max images per listing must be between 1 and 50.', 422);
        }
        if ($keyed->has('service_fee_percentage') && ((float)($keyed['service_fee_percentage'] ?? -1) < 0 || (float)($keyed['service_fee_percentage'] ?? 99) > 50)) {
            return $this->error('Service fee percentage must be between 0% and 50%.', 422);
        }
        if ($keyed->has('appointment_duration_default') && ((int)($keyed['appointment_duration_default'] ?? 0) < 10 || (int)($keyed['appointment_duration_default'] ?? 0) > 240)) {
            return $this->error('Default tour viewing duration must be between 10 and 240 minutes.', 422);
        }

        foreach ($request->settings as $item) {
            SystemSetting::set($item['key'], $item['value']);
        }

        return $this->success(null, 'Settings updated successfully');
    }
}
