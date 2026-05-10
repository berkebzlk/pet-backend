<?php

namespace App\Modules\VideoCall\Services;

use App\Modules\VideoCall\Events\CallAcceptedEvent;
use App\Modules\VideoCall\Events\CallEndedEvent;
use App\Modules\VideoCall\Events\CallInitiatedEvent;
use App\Modules\VideoCall\Events\WebRTCSignalEvent;
use App\Modules\VideoCall\Models\VideoCall;
use Illuminate\Support\Str;

class VideoCallService
{
    public function initiateCall(int $callerId, int $receiverId): VideoCall
    {
        // Prevent calling self
        if ($callerId === $receiverId) {
            throw new \Exception('You cannot call yourself.');
        }

        // Check for active calls
        $activeCall = VideoCall::whereIn('status', ['pending', 'accepted'])
            ->where(function ($query) use ($callerId, $receiverId) {
                $query->where('caller_id', $callerId)
                    ->orWhere('receiver_id', $callerId)
                    ->orWhere('caller_id', $receiverId)
                    ->orWhere('receiver_id', $receiverId);
            })->first();

        if ($activeCall) {
            throw new \Exception('One of the users is already in a call.');
        }

        $call = VideoCall::create([
            'caller_id' => $callerId,
            'receiver_id' => $receiverId,
            'status' => 'pending',
            'room_name' => 'room_' . Str::random(12),
        ]);

        broadcast(new CallInitiatedEvent($call))->toOthers();

        return $call;
    }

    public function acceptCall(string $callId, int $userId): VideoCall
    {
        $call = VideoCall::findOrFail($callId);

        if ($call->receiver_id !== $userId) {
            throw new \Exception('Unauthorized.');
        }

        $call->update([
            'status' => 'accepted',
            'started_at' => now(),
        ]);

        broadcast(new CallAcceptedEvent($call))->toOthers();

        return $call;
    }

    public function endCall(string $callId, int $userId): VideoCall
    {
        $call = VideoCall::findOrFail($callId);

        if ($call->caller_id !== $userId && $call->receiver_id !== $userId) {
            throw new \Exception('Unauthorized.');
        }

        $call->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        $targetUserId = ($call->caller_id === $userId) ? $call->receiver_id : $call->caller_id;
        broadcast(new CallEndedEvent($call, $targetUserId))->toOthers();

        return $call;
    }

    public function sendSignal(string $callId, int $senderId, int $receiverId, array $signalData, string $type): void
    {
        // Simple relay
        broadcast(new WebRTCSignalEvent($callId, $senderId, $receiverId, $signalData, $type))->toOthers();
    }
}
