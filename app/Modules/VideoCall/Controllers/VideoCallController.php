<?php

namespace App\Modules\VideoCall\Controllers;

use App\Modules\VideoCall\Requests\AcceptCallRequest;
use App\Modules\VideoCall\Requests\InitiateCallRequest;
use App\Modules\VideoCall\Requests\RejectCallRequest;
use App\Modules\VideoCall\Requests\SignalRequest;
use App\Modules\VideoCall\Services\VideoCallService;
use Illuminate\Http\JsonResponse;

class VideoCallController
{
    protected VideoCallService $videoCallService;

    public function __construct(VideoCallService $videoCallService)
    {
        $this->videoCallService = $videoCallService;
    }

    public function initiate(InitiateCallRequest $request): JsonResponse
    {
        try {
            $call = $this->videoCallService->initiateCall(
                auth()->id(),
                $request->validated('receiver_id')
            );
            return response()->json(['message' => 'Call initiated', 'call' => $call]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function accept(AcceptCallRequest $request, string $callId): JsonResponse
    {
        try {
            $call = $this->videoCallService->acceptCall($callId, auth()->id());
            return response()->json(['message' => 'Call accepted', 'call' => $call]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function reject(RejectCallRequest $request, string $callId): JsonResponse
    {
         try {
            $call = $this->videoCallService->rejectCall($callId, auth()->id());
            return response()->json(['message' => 'Call rejected', 'call' => $call]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function end(string $callId): JsonResponse
    {
        try {
            $call = $this->videoCallService->endCall($callId, auth()->id());
            return response()->json(['message' => 'Call ended', 'call' => $call]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
    
    public function signal(SignalRequest $request): JsonResponse
    {
        try {
            $this->videoCallService->sendSignal(
                $request->validated('call_id'),
                auth()->id(),
                $request->validated('receiver_id'),
                $request->validated('signal_data'),
                $request->validated('type')
            );
            return response()->json(['message' => 'Signal sent']);
        } catch (\Exception $e) {
             return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
