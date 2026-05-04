<?php

namespace App\Http\Controllers\Admin\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiVoice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AiVoiceController extends Controller
{
    public function index()
    {
        $voices = AiVoice::orderBy('language')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Ai/Voices', [
            'voices' => $voices,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|in:elevenlabs,openai,google',
            'provider_voice_id' => 'required|string|max:100',
            'name' => 'required|string|max:100',
            'language' => 'required|in:bangla,english,hindi,arabic,urdu',
            'gender' => 'required|in:male,female',
            'accent' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'is_pro_only' => 'boolean',
            'preview_url' => 'nullable|url|max:500',
        ]);

        AiVoice::create($validated);

        return back()->with('success', 'Voice added successfully.');
    }

    public function update(Request $request, AiVoice $voice)
    {
        $validated = $request->validate([
            'provider' => 'required|in:elevenlabs,openai,google',
            'provider_voice_id' => 'required|string|max:100',
            'name' => 'required|string|max:100',
            'language' => 'required|in:bangla,english,hindi,arabic,urdu',
            'gender' => 'required|in:male,female',
            'accent' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'is_pro_only' => 'boolean',
            'preview_url' => 'nullable|url|max:500',
        ]);

        $voice->update($validated);

        return back()->with('success', 'Voice updated successfully.');
    }

    public function destroy(AiVoice $voice)
    {
        $voice->delete();
        return back()->with('success', 'Voice deleted.');
    }
}
