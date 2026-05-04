<?php

namespace App\Services;

use App\DataObjects\VoiceResult;
use App\Models\AiVoice;
use App\Models\AiVoiceJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\AiSetting;

class AiVoiceService
{
    /**
     * Generate voice from text.
     */
    public function generate(
        string $text,
        int $voiceId,
        string $language,
        float $speed = 1.0,
        float $pitch = 1.0,
        string $format = 'mp3',
    ): VoiceResult {
        $voice = AiVoice::findOrFail($voiceId);
        $provider = $voice->provider;

        $audioContent = match ($provider) {
            'elevenlabs' => $this->generateElevenLabs($text, $voice, $speed),
            'openai' => $this->generateOpenAI($text, $voice, $speed),
            'google' => $this->generateGoogle($text, $voice, $language, $speed, $pitch),
            default => throw new \Exception("Unknown voice provider: {$provider}"),
        };

        // Store audio file
        $uuid = (string) Str::uuid();
        $filename = "voice-jobs/{$uuid}/output.{$format}";
        Storage::disk('local')->put($filename, $audioContent);

        $expiresAt = now()->addMinutes(60);

        // Create job record
        AiVoiceJob::create([
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'text_input' => $text,
            'language' => $language,
            'voice_id' => $voiceId,
            'speed' => $speed,
            'pitch' => $pitch,
            'output_path' => $filename,
            'file_size' => Storage::disk('local')->size($filename),
            'duration_seconds' => $this->estimateDuration($text, $speed),
            'status' => 'done',
            'provider' => $provider,
            'expires_at' => $expiresAt,
        ]);

        return new VoiceResult(
            uuid: $uuid,
            downloadUrl: route('api.voice.download', $uuid),
            duration: $this->estimateDuration($text, $speed),
            expiresAt: $expiresAt,
        );
    }

    /**
     * ElevenLabs Multilingual v2.
     */
    protected function generateElevenLabs(string $text, AiVoice $voice, float $speed): string
    {
        $apiKey = AiSetting::getValue('elevenlabs_api_key');
        if (!$apiKey) {
            throw new \Exception('ElevenLabs API key not configured.');
        }

        $response = Http::withHeaders([
            'xi-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post(
            "https://api.elevenlabs.io/v1/text-to-speech/{$voice->provider_voice_id}",
            [
                'text' => $text,
                'model_id' => 'eleven_multilingual_v2',
                'voice_settings' => [
                    'stability' => 0.5,
                    'similarity_boost' => 0.8,
                    'style' => 0.0,
                    'use_speaker_boost' => true,
                ],
            ]
        );

        if ($response->failed()) {
            throw new \Exception('ElevenLabs API error: ' . $response->body());
        }

        return $response->body();
    }

    /**
     * OpenAI TTS HD.
     */
    protected function generateOpenAI(string $text, AiVoice $voice, float $speed): string
    {
        $apiKey = AiSetting::getValue('openai_tts_api_key') ?: config('services.openai.api_key');
        if (!$apiKey) {
            throw new \Exception('OpenAI API key not configured.');
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/audio/speech', [
            'model' => 'tts-1-hd',
            'input' => $text,
            'voice' => $voice->provider_voice_id,
            'speed' => $speed,
            'response_format' => 'mp3',
        ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI TTS error: ' . $response->body());
        }

        return $response->body();
    }

    /**
     * Google Cloud TTS.
     */
    protected function generateGoogle(
        string $text,
        AiVoice $voice,
        string $language,
        float $speed,
        float $pitch,
    ): string {
        $apiKey = AiSetting::getValue('google_tts_api_key');
        if (!$apiKey) {
            throw new \Exception('Google TTS API key not configured.');
        }

        $langCode = match ($language) {
            'bangla' => 'bn-BD',
            'hindi' => 'hi-IN',
            'arabic' => 'ar-XA',
            'urdu' => 'ur-PK',
            default => 'en-US',
        };

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(30)->post(
            "https://texttospeech.googleapis.com/v1/text:synthesize?key={$apiKey}",
            [
                'input' => ['text' => $text],
                'voice' => [
                    'languageCode' => $langCode,
                    'name' => $voice->provider_voice_id,
                    'ssmlGender' => strtoupper($voice->gender),
                ],
                'audioConfig' => [
                    'audioEncoding' => 'MP3',
                    'speakingRate' => $speed,
                    'pitch' => ($pitch - 1.0) * 20,
                    'sampleRateHertz' => 24000,
                ],
            ]
        );

        if ($response->failed()) {
            throw new \Exception('Google TTS error: ' . $response->body());
        }

        $data = $response->json();
        return base64_decode($data['audioContent']);
    }

    /**
     * Estimate audio duration in seconds.
     */
    protected function estimateDuration(string $text, float $speed): float
    {
        // ~130 words/min average speaking rate
        $wordCount = count(preg_split('/\s+/u', trim($text)));
        return round(($wordCount / 130) * 60 / $speed, 1);
    }

    /**
     * Clean up expired voice jobs and their files.
     */
    public function cleanupExpired(): int
    {
        $expired = AiVoiceJob::expired()->get();
        $count = 0;

        foreach ($expired as $job) {
            if ($job->output_path && Storage::disk('local')->exists($job->output_path)) {
                // Delete the file and its directory
                $dir = dirname($job->output_path);
                Storage::disk('local')->deleteDirectory($dir);
            }
            $job->delete();
            $count++;
        }

        return $count;
    }
}
