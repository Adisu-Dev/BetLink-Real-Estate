<?php

namespace App\Http\Controllers\Api\V1\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Faq;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ApiResponse;

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        Contact::create($request->only('name', 'email', 'phone', 'subject', 'message'));

        return $this->created(null, 'Message sent. We\'ll get back to you within 24 hours.');
    }

    public function faqs(Request $request): JsonResponse
    {
        $faqs = Faq::active()
            ->when($request->category, fn($q, $v) => $q->where('category', $v))
            ->get();

        return $this->success($faqs);
    }
}
