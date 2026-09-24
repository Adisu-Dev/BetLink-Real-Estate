<?php

namespace App\Http\Controllers\Api\V1\Message;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use ApiResponse;

    public function index(Request $request, Conversation $conversation): JsonResponse
    {
        $this->ensureParticipant($request, $conversation);

        $messages = $conversation->messages()
            ->with('sender:id,name,avatar')
            ->latest()
            ->paginate($request->per_page ?? 30);

        return $this->paginated($messages);
    }

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $this->ensureParticipant($request, $conversation);

        $request->validate([
            'body'           => ['required_without:attachment', 'nullable', 'string', 'max:5000'],
            'attachment'     => ['nullable', 'file', 'max:10240'],
            'type'           => ['nullable', 'in:text,image,file'],
        ]);

        $attachmentUrl = null;
        if ($request->hasFile('attachment')) {
            $attachmentUrl = $request->file('attachment')->store('messages', 'public');
        }

        $message = $conversation->messages()->create([
            'sender_id'      => $request->user()->id,
            'body'           => $request->body ?? '',
            'type'           => $request->type ?? 'text',
            'attachment_url' => $attachmentUrl,
        ]);

        $conversation->touch();

        // Notify other participants
        $otherParticipants = $conversation->participants()
            ->where('user_id', '!=', $request->user()->id)
            ->get();

        foreach ($otherParticipants as $participant) {
            try {
                $participant->notify(new \App\Notifications\NewMessageReceived($message));
            } catch (\Throwable $e) {}
        }

        return $this->created($message->load('sender'), 'Message sent');
    }

    public function update(Request $request, Conversation $conversation, Message $message): JsonResponse
    {
        $this->ensureParticipant($request, $conversation);
        if ($message->sender_id !== $request->user()->id) {
            return $this->error('Unauthorized to edit this message', 403);
        }

        $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message->update([
            'body' => $request->body,
        ]);

        return $this->success($message->load('sender'), 'Message updated successfully');
    }

    public function destroy(Request $request, Conversation $conversation, Message $message): JsonResponse
    {
        $this->ensureParticipant($request, $conversation);
        if ($message->sender_id !== $request->user()->id) {
            return $this->error('Unauthorized to delete this message', 403);
        }

        $message->delete();

        return $this->success(null, 'Message deleted successfully');
    }

    private function ensureParticipant(Request $request, Conversation $conversation): void
    {
        if (!$conversation->participants()->where('user_id', $request->user()->id)->exists()) {
            abort(403, 'Not a participant');
        }
    }
}
