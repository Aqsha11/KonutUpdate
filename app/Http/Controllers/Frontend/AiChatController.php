<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\AiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AiChatController extends Controller
{
    public function __construct(
        protected AiChatService $ai,
    ) {}

    public function ask(Request $request): JsonResponse
    {
        if (! $this->ai->enabled()) {
            return response()->json([
                'message' => 'Fitur Tanya Berita belum tersedia saat ini.',
            ], 503);
        }

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:300'],
        ], [
            'question.required' => 'Silakan tulis pertanyaan Anda.',
        ]);

        $isGuest = ! $request->user();
        $guestLimit = (int) config('services.gemini.guest_limit', 0);
        $guestKey = 'ai_chat:guest:'.$request->ip();
        $guestUsed = $isGuest ? (int) Cache::get($guestKey, 0) : 0;

        if ($isGuest && ($guestLimit <= 0 || $guestUsed >= $guestLimit)) {
            return response()->json([
                'message' => 'Batas percobaan gratis sudah habis. Buat akun atau masuk untuk melanjutkan Tanya Berita.',
                'requires_auth' => true,
            ], 403);
        }

        $result = $this->ai->answer(trim($validated['question']));

        if (! ($result['ok'] ?? false)) {
            $message = match ($result['error'] ?? null) {
                'quota' => 'Layanan tanya berita sedang penuh untuk hari ini. Coba lagi besok.',
                'upstream' => 'Asisten sedang tidak dapat dihubungi. Silakan coba beberapa saat lagi.',
                default => 'Fitur Tanya Berita belum tersedia saat ini.',
            };

            return response()->json(['message' => $message], 503);
        }

        $guestRemaining = null;

        if ($isGuest && $guestLimit > 0) {
            $guestUsed++;
            Cache::put($guestKey, $guestUsed, now()->addDay());
            $guestRemaining = max(0, $guestLimit - $guestUsed);
        }

        return response()->json([
            'answer' => $result['answer'],
            'sources' => $result['sources'] ?? [],
            'guest_remaining' => $guestRemaining,
        ]);
    }
}
