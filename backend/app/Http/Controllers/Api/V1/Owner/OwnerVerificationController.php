<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OwnerVerificationController extends Controller
{
    use ApiResponse;

    /**
     * Get verification requests for authenticated owner
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $requests = VerificationRequest::where('user_id', $user->id)
            ->latest()
            ->get();

        $docsMap = [];
        foreach ($requests as $req) {
            if (is_array($req->documents)) {
                foreach ($req->documents as $typeKey => $docData) {
                    if (is_array($docData)) {
                        $payload = array_merge($docData, [
                            'status'           => $req->status,
                            'request_id'       => $req->id,
                            'admin_notes'      => $req->notes,
                            'reviewed_at'      => $req->reviewed_at,
                        ]);
                        $docsMap[$typeKey] = $payload;

                        // Normalize common aliases
                        if ($typeKey === 'national_id' && !isset($docsMap['id_card'])) {
                            $docsMap['id_card'] = $payload;
                        }
                        if ($typeKey === 'id_card' && !isset($docsMap['national_id'])) {
                            $docsMap['national_id'] = $payload;
                        }
                        if ($typeKey === 'deed' && !isset($docsMap['title_deed'])) {
                            $docsMap['title_deed'] = $payload;
                        }
                    }
                }
            }

            // Fallback for requests that might have type specified without explicit typeKey in documents
            if ($req->type === 'property' && empty($docsMap['title_deed']) && is_array($req->documents) && !empty($req->documents)) {
                $firstDoc = reset($req->documents);
                if (is_array($firstDoc)) {
                    $docsMap['title_deed'] = array_merge($firstDoc, [
                        'status'      => $req->status,
                        'request_id'  => $req->id,
                        'admin_notes' => $req->notes,
                    ]);
                }
            }

            if ($req->type === 'identity' && empty($docsMap['id_card']) && is_array($req->documents) && !empty($req->documents)) {
                $firstDoc = reset($req->documents);
                if (is_array($firstDoc)) {
                    $docsMap['id_card'] = array_merge($firstDoc, [
                        'status'      => $req->status,
                        'request_id'  => $req->id,
                        'admin_notes' => $req->notes,
                    ]);
                }
            }
        }

        return $this->success([
            'requests'            => $requests,
            'documents'           => $docsMap,
            'is_profile_verified' => (bool) $user->profile?->is_verified,
        ], 'Verification requests retrieved successfully');
    }

    /**
     * Submit ownership document for verification
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'property_id'    => ['nullable', 'integer'],
            'document_type'  => ['required', 'string'],
            'file'           => ['nullable', 'file', 'max:10240'],
            'document_file'  => ['nullable', 'file', 'max:10240'],
            'document_url'   => ['nullable', 'string'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ]);

        $file = $request->file('file') ?? $request->file('document_file');
        $fileName = 'document_' . time() . '.pdf';
        $fileSize = null;

        if ($file) {
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $path = $file->store('verifications', 'public');
            $docUrl = asset('storage/' . $path);
        } elseif ($request->filled('document_url')) {
            $docUrl = $request->document_url;
            $fileName = basename(parse_url($docUrl, PHP_URL_PATH)) ?: 'external_document.pdf';
        } else {
            $docUrl = 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=600&q=80';
        }

        $docPayload = [
            'document_type' => $request->document_type,
            'file_name'     => $fileName,
            'file_size'     => $fileSize,
            'url'           => $docUrl,
            'document_url'  => $docUrl,
            'notes'         => $request->notes,
            'submitted_at'  => now()->toIso8601String(),
        ];

        // Find existing verification or create a new one
        $verification = VerificationRequest::where('user_id', $request->user()->id)
            ->where('type', 'property')
            ->first();

        if ($verification) {
            $docs = is_array($verification->documents) ? $verification->documents : [];
            $docs[$request->document_type] = $docPayload;

            $verification->update([
                'documents' => $docs,
                'status'    => 'pending',
                'notes'     => $request->notes ?: $verification->notes,
            ]);
        } else {
            $verification = VerificationRequest::create([
                'user_id'   => $request->user()->id,
                'type'      => 'property',
                'documents' => [$request->document_type => $docPayload],
                'status'    => 'pending',
                'notes'     => $request->notes,
            ]);
        }

        return $this->created([
            'verification' => $verification,
            'document'     => $docPayload,
        ], 'Verification document submitted successfully for admin review');
    }
}
