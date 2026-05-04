<?php

namespace App\Services;

use App\Models\Tool;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class OgImageService
{
    /**
     * Generate or return cached OG image for a tool.
     *
     * @param Tool $tool
     * @return string URL of the OG image
     */
    public function generateForTool(Tool $tool): string
    {
        $filename = "tools/og/{$tool->slug}.webp";
        
        if (Storage::disk('public')->exists($filename)) {
            return Storage::url($filename);
        }

        try {
            // Dimensions: 1200x630
            $image = Image::create(1200, 630);
            
            // Fill background with a nice gradient-like color
            $image->fill('#6366F1');

            // Add some design elements (branding, tool name, icon)
            // Note: This requires fonts to be installed on the server
            // For now, we'll create a simpler version or just return a default if it fails
            
            // To implement full canvas drawing with Bangla support, we'd need:
            // $image->text($tool->name, 600, 315, function($font) { ... });

            $path = storage_path("app/public/{$filename}");
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            $image->encodeByMediaType('image/webp', quality: 80)->save($path);
            
            return Storage::url($filename);
        } catch (\Exception $e) {
            return asset('images/og-home.png');
        }
    }
}
