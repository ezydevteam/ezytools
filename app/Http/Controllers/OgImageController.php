<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Services\OgImageService;
use Illuminate\Http\Response;

class OgImageController extends Controller
{
    public function show(string $slug, OgImageService $service): Response
    {
        $tool = Tool::where('slug', $slug)->active()->firstOrFail();
        
        $imageUrl = $service->generateForTool($tool);
        
        // In a real production environment, we might redirect or stream the file
        // For now, we'll redirect to the generated storage URL
        return response()->redirectTo($imageUrl);
    }
}
