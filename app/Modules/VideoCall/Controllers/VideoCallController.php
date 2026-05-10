<?php

namespace App\Modules\VideoCall\Controllers;

use App\Modules\VideoCall\Services\VideoCallService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoCallController
{
    public function __construct(protected VideoCallService $videoCallService) {}

    public function initiate(Request $request): JsonResponse
    {
        $request->validate(['receiver_id' => 'required|exists:users,id']);

        try {
            $call = $this->videoCallService->initiateCall(auth()->id(), $request->receiver_id);
            return response()->json($call);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function accept(string $id): JsonResponse
    {
        try {
            $call = $this->videoCallService->acceptCall($id, auth()->id());
            return response()->json($call);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function end(string $id): JsonResponse
    {
        try {
            $call = $this->videoCallService->endCall($id, auth()->id());
            return response()->json($call);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function signal(Request $request): JsonResponse
    {
        $request->validate([
            'call_id' => 'required|uuid',
            'receiver_id' => 'required|exists:users,id',
            'signal_data' => 'required|array',
            'type' => 'required|string',
        ]);

        $this->videoCallService->sendSignal(
            $request->call_id,
            auth()->id(),
            $request->receiver_id,
            $request->signal_data,
            $request->type
        );

        return response()->json(['status' => 'signal_sent']);
    }
}
