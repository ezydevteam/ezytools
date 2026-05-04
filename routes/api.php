<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// UddoktaPay Webhook
Route::post('/payment/webhook', [\App\Http\Controllers\SubscriptionController::class, 'webhook'])->name('api.payment.webhook');

// Global Search
Route::get('/search', function (\Illuminate\Http\Request $request) {
    $query = $request->get('q');
    if (!$query) return response()->json([]);

    $tools = \App\Models\Tool::with('category')
        ->where('is_active', true)
        ->where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('short_description', 'LIKE', "%{$query}%");
        })
        ->take(10)
        ->get()
        ->map(function ($tool) {
            return [
                'id' => $tool->id,
                'name' => $tool->name,
                'icon' => $tool->icon,
                'url' => route('tools.show', ['category' => $tool->category->slug, 'slug' => $tool->slug]),
                'category_name' => $tool->category->name
            ];
        });

    return response()->json($tools);
});


// AI Tools Route (generic handler)
Route::prefix('ai')->middleware(['web', 'throttle:ai'])->group(function () {
    // Dedicated endpoints for advanced tools
    Route::post('/content-detector', [\App\Http\Controllers\Api\AiToolController::class, 'contentDetector']);
    Route::post('/detector-humanizer', [\App\Http\Controllers\Api\AiToolController::class, 'detectorHumanizer']);
    Route::post('/voice-generator', [\App\Http\Controllers\Api\AiToolController::class, 'voiceGenerator']);
    Route::post('/seo-auditor', [\App\Http\Controllers\Api\AiToolController::class, 'seoAuditor']);
    Route::get('/seo-auditor/report/{uuid}', [\App\Http\Controllers\Api\AiToolController::class, 'getSeoReport']);

    // Generic AI tool handler (must be last — catches all slugs)
    Route::post('/{toolSlug}', [\App\Http\Controllers\Api\AiToolController::class, 'handle']);
});

// Voice download route
Route::get('/voice/download/{uuid}', [\App\Http\Controllers\Api\AiToolController::class, 'voiceDownload'])
    ->name('api.voice.download');

// YouTube Video Info
Route::post('/youtube/info', [\App\Http\Controllers\Api\YoutubeInfoController::class, 'info'])
    ->middleware(['throttle:30,1']);

// PDF Tools Routes
Route::prefix('pdf')->middleware(['throttle:30,1'])->group(function () {
    Route::post('/upload',           [\App\Http\Controllers\Api\PdfController::class, 'upload']);
    Route::post('/merge',            [\App\Http\Controllers\Api\PdfController::class, 'merge']);
    Route::post('/split',            [\App\Http\Controllers\Api\PdfController::class, 'split']);
    Route::post('/compress',         [\App\Http\Controllers\Api\PdfController::class, 'compress']);
    Route::post('/password-protect', [\App\Http\Controllers\Api\PdfController::class, 'passwordProtect']);
    Route::post('/remove-password',  [\App\Http\Controllers\Api\PdfController::class, 'removePassword']);
    Route::post('/watermark',        [\App\Http\Controllers\Api\PdfController::class, 'watermark']);
    Route::post('/rotate',           [\App\Http\Controllers\Api\PdfController::class, 'rotate']);
    Route::post('/extract-pages',    [\App\Http\Controllers\Api\PdfController::class, 'extractPages']);
    Route::post('/pdf-to-word',      [\App\Http\Controllers\Api\PdfController::class, 'pdfToWord']);
    Route::post('/pdf-to-images',    [\App\Http\Controllers\Api\PdfController::class, 'pdfToImages']);
    Route::post('/images-to-pdf',    [\App\Http\Controllers\Api\PdfController::class, 'imagesToPdf']);
    Route::post('/add-page-numbers', [\App\Http\Controllers\Api\PdfController::class, 'addPageNumbers']);
    Route::post('/edit',             [\App\Http\Controllers\Api\PdfController::class, 'edit']);
    Route::post('/reorder-pages',    [\App\Http\Controllers\Api\PdfController::class, 'reorderPages']);
    Route::get('/download/{uuid}',   [\App\Http\Controllers\Api\PdfController::class, 'download']);
    Route::get('/preview/{uuid}',    [\App\Http\Controllers\Api\PdfController::class, 'preview']);
});

// Web Tools Routes
Route::prefix('web-tools')->middleware(['throttle:20,1'])->group(function () {
    Route::post('/dns-lookup',          [\App\Http\Controllers\Api\WebToolController::class, 'dnsLookup']);
    Route::post('/ip-lookup',           [\App\Http\Controllers\Api\WebToolController::class, 'ipLookup']);
    Route::post('/whois-lookup',        [\App\Http\Controllers\Api\WebToolController::class, 'whoisLookup']);
    Route::post('/meta-tags-checker',   [\App\Http\Controllers\Api\WebToolController::class, 'metaTagsChecker']);
    Route::post('/ping',                [\App\Http\Controllers\Api\WebToolController::class, 'ping']);
    Route::post('/hosting-checker',     [\App\Http\Controllers\Api\WebToolController::class, 'hostingChecker']);
    Route::post('/google-cache-checker',[\App\Http\Controllers\Api\WebToolController::class, 'googleCacheChecker']);
});

