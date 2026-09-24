<?php

namespace App\Http\Controllers\Api\V1\Message;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $conversations = $request->user()
            ->conversations()
            ->with([
                'messages' => function ($q) {
                    $q->with('sender:id,name,avatar')->orderBy('created_at', 'asc');
                },
                'latestMessage.sender:id,name,avatar',
                'property' => function ($q) {
                    $q->select('id', 'title', 'slug', 'price', 'currency', 'user_id')
                      ->with(['images:id,property_id,url,is_primary']);
                },
                'participants:id,name,avatar'
            ])
            ->orderByDesc('updated_at')
            ->paginate($request->per_page ?? 20);

        $conversations->getCollection()->transform(function ($c) use ($userId) {
            $other = $c->participants->firstWhere('id', '!=', $userId) ?? $c->participants->first();
            $c->other_user = $other ? [
                'id'     => $other->id,
                'name'   => $other->name,
                'avatar' => $other->avatar_url ?? $other->avatar,
                'role'   => 'Property Host',
                'online' => true,
            ] : [
                'id'     => 1,
                'name'   => 'Property Landlord',
                'avatar' => null,
                'role'   => 'Landlord',
                'online' => false,
            ];
            $c->unread_count = $c->getUnreadCountForUser($userId);
            return $c;
        });

        return $this->paginated($conversations);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'property_id'    => ['required', 'exists:properties,id'],
            'recipient_id'   => ['required', 'exists:users,id'],
            'initial_message'=> ['required', 'string', 'max:2000'],
        ]);

        $property = Property::findOrFail($request->property_id);

        if ($property->user_id === $request->user()->id || (int)$request->recipient_id === (int)$request->user()->id) {
            return $this->error('You cannot send an inquiry on your own property listing.', 422);
        }

        // Prevent duplicate conversations
        $existing = Conversation::where('property_id', $property->id)
            ->whereHas('participants', fn($q) => $q->where('user_id', $request->user()->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $request->recipient_id))
            ->first();

        if ($existing) {
            return $this->success($existing->load(['messages', 'participants']), 'Existing conversation');
        }

        $conversation = Conversation::create([
            'property_id' => $property->id,
            'type'        => 'inquiry',
        ]);

        $conversation->participants()->attach([
            $request->user()->id      => ['last_read_at' => now()],
            $request->recipient_id    => ['last_read_at' => null],
        ]);

        $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'body'      => $request->initial_message,
            'type'      => 'text',
        ]);

        return $this->created($conversation->load(['messages.sender', 'participants', 'property']), 'Conversation started');
    }

    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $this->ensureParticipant($request, $conversation);

        // Mark as read
        $conversation->participants()->updateExistingPivot(
            $request->user()->id,
            ['last_read_at' => now()]
        );

        return $this->success($conversation->load(['messages.sender', 'participants', 'property']));
    }

    public function markRead(Request $request, Conversation $conversation): JsonResponse
    {
        $this->ensureParticipant($request, $conversation);
        $conversation->participants()->updateExistingPivot($request->user()->id, ['last_read_at' => now()]);
        return $this->success(null, 'Marked as read');
    }

    public function destroy(Request $request, Conversation $conversation): JsonResponse
    {
        $this->ensureParticipant($request, $conversation);
        $conversation->messages()->delete();
        $conversation->participants()->detach();
        $conversation->delete();
        return $this->success(null, 'Conversation deleted successfully');
    }

    private function ensureParticipant(Request $request, Conversation $conversation): void
    {
        $isParticipant = $conversation->participants()->where('user_id', $request->user()->id)->exists();
        if (!$isParticipant) abort(403, 'Not a participant');
    }
}
