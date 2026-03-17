<?php

namespace App\Modules\VideoCall\Services;

use App\Modules\VideoCall\Events\CallAcceptedEvent;
use App\Modules\VideoCall\Events\CallEndedEvent;
use App\Modules\VideoCall\Events\CallInitiatedEvent;
use App\Modules\VideoCall\Events\CallRejectedEvent;
use App\Modules\VideoCall\Events\WebRTCSignalEvent;
use App\Modules\VideoCall\Models\VideoCall;
use Illuminate\Support\Str;

class VideoCallService
{
    public function initiateCall(int $callerId, int $receiverId)
    {
        // Check if there is already an active call
        $existingCall = VideoCall::whereIn('status', ['pending', 'accepted'])
            ->where(function ($query) use ($callerId, $receiverId) {
                $query->where(function ($q) use ($callerId, $receiverId) {
                    $q->where('caller_id', $callerId)->where('receiver_id', $receiverId);
                })->orWhere(function ($q) use ($callerId, $receiverId) {
                    $q->where('caller_id', $receiverId)->where('receiver_id', $callerId);
                });
            })->first();
            
        if ($existingCall) {
            throw new \Exception('There is already an active call with this user.');
        }

        $call = VideoCall::create([
            'caller_id' => $callerId,
            'receiver_id' => $receiverId,
            'status' => 'pending',
            'room_name' => 'call_' . Str::random(10) . '_' . time(),
        ]);

        broadcast(new CallInitiatedEvent($call));

        return $call;
    }

    public function acceptCall(string $callId, int $userId)
    {
        $call = VideoCall::findOrFail($callId);

        if ($call->receiver_id !== $userId) {
            throw new \Exception('Unauthorized access to this call.');
        }

        $call->update([
            'status' => 'accepted',
            'started_at' => now(),
        ]);

        broadcast(new CallAcceptedEvent($call));

        return $call;
    }

    public function rejectCall(string $callId, int $userId)
    {
        $call = VideoCall::findOrFail($callId);

        if ($call->receiver_id !== $userId && $call->caller_id !== $userId) {
             throw new \Exception('Unauthorized access to this call.');
        }

        $call->update([
            'status' => 'rejected',
            'ended_at' => now(),
        ]);

        broadcast(new CallRejectedEvent($call));

        return $call;
    }

    public function endCall(string $callId, int $userId)
    {
        $call = VideoCall::findOrFail($callId);

        if ($call->receiver_id !== $userId && $call->caller_id !== $userId) {
             throw new \Exception('Unauthorized access to this call.');
        }

        $call->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        broadcast(new CallEndedEvent($call));

        return $call;
    }

    public function sendSignal(string $callId, int $senderId, int $receiverId, array $signalData, string $type)
    {
        $call = VideoCall::findOrFail($callId);
        
        // Ensure the sender is part of the call
        if ($call->caller_id !== $senderId && $call->receiver_id !== $senderId) {
            throw new \Exception('Unauthorized to send signals for this call.');
        }

        broadcast(new WebRTCSignalEvent($callId, $senderId, $receiverId, $signalData, $type));
        
        return true;
    }
}
