<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    protected PdfService $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function upload(Request $request) 
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,txt|max:10240',
        ]);

        $file = $request->file('file');
        $text = '';

        if ($file->getClientOriginalExtension() === 'pdf') {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($file->getPathname());
                $text = $pdf->getText();
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Failed to extract text from PDF: ' . $e->getMessage()], 500);
            }
        } else {
            $text = file_get_contents($file->getPathname());
        }

        return response()->json([
            'success' => true,
            'text' => $text,
        ]);
    }
    public function merge(Request $request) {
        $request->validate([
            'files' => 'required|array|min:2',
            'files.*' => 'required|file|mimes:pdf'
        ]);

        $paths = [];
        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $dir = "pdf-jobs/{$uuid}";

        foreach ($request->file('files') as $file) {
            $path = $file->store($dir, 'local');
            $paths[] = storage_path("app/private/{$path}");
        }

        try {
            $outputPath = $this->pdfService->mergePdfs($paths);
            
            \App\Models\PdfJob::create([
                'id' => $uuid,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
                'tool_slug' => 'pdf-merge',
                'input_files' => $paths,
                'output_file' => $outputPath,
                'status' => 'done',
                'expires_at' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'uuid' => $uuid,
                'download_url' => url("/api/pdf/download/{$uuid}")
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function split(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'ranges' => 'required|string'
        ]);

        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $dir = "pdf-jobs/{$uuid}";
        
        $path = $request->file('file')->store($dir, 'local');
        $absolutePath = storage_path("app/private/{$path}");

        try {
            // Parse ranges like "1-3, 5, 7-10" into an array of page numbers
            $ranges = $request->input('ranges');
            $pages = [];
            $parts = array_filter(array_map('trim', explode(',', $ranges)));
            foreach ($parts as $part) {
                if (str_contains($part, '-')) {
                    list($start, $end) = array_map('intval', explode('-', $part));
                    if ($start <= $end) {
                        for ($i = $start; $i <= $end; $i++) {
                            $pages[] = $i;
                        }
                    }
                } else {
                    $pages[] = intval($part);
                }
            }
            $pages = array_unique(array_filter($pages));
            sort($pages);

            if (empty($pages)) {
                throw new \Exception("Invalid page ranges specified.");
            }

            // Notice we use extractPages method from PdfService
            $outputPath = $this->pdfService->extractPages($absolutePath, $pages);
            
            \App\Models\PdfJob::create([
                'id' => $uuid,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
                'tool_slug' => 'pdf-split',
                'input_files' => [$absolutePath],
                'output_file' => $outputPath,
                'status' => 'done',
                'expires_at' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'uuid' => $uuid,
                'download_url' => url("/api/pdf/download/{$uuid}")
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function compress(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'quality' => 'required|string|in:screen,ebook,printer'
        ]);

        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $dir = "pdf-jobs/{$uuid}";
        
        $path = $request->file('file')->store($dir, 'local');
        $absolutePath = storage_path("app/private/{$path}");

        try {
            $outputPath = $this->pdfService->compressPdf($absolutePath, $request->input('quality'));
            
            \App\Models\PdfJob::create([
                'id' => $uuid,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
                'tool_slug' => 'pdf-compress',
                'input_files' => [$absolutePath],
                'output_file' => $outputPath,
                'status' => 'done',
                'expires_at' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'uuid' => $uuid,
                'new_size_mb' => number_format(filesize($outputPath) / 1024 / 1024, 2),
                'download_url' => url("/api/pdf/download/{$uuid}")
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function passwordProtect(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'password' => 'required|string|min:4'
        ]);

        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $dir = "pdf-jobs/{$uuid}";
        
        $path = $request->file('file')->store($dir, 'local');
        $absolutePath = storage_path("app/private/{$path}");

        try {
            $outputPath = $this->pdfService->passwordProtect($absolutePath, $request->input('password'));
            
            \App\Models\PdfJob::create([
                'id' => $uuid,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
                'tool_slug' => 'pdf-password-protect',
                'input_files' => [$absolutePath],
                'output_file' => $outputPath,
                'status' => 'done',
                'expires_at' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'uuid' => $uuid,
                'download_url' => url("/api/pdf/download/{$uuid}")
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function removePassword(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'password' => 'required|string'
        ]);

        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $dir = "pdf-jobs/{$uuid}";
        
        $path = $request->file('file')->store($dir, 'local');
        $absolutePath = storage_path("app/private/{$path}");

        try {
            $outputPath = $this->pdfService->removePassword($absolutePath, $request->input('password'));
            
            \App\Models\PdfJob::create([
                'id' => $uuid,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
                'tool_slug' => 'pdf-remove-password',
                'input_files' => [$absolutePath],
                'output_file' => $outputPath,
                'status' => 'done',
                'expires_at' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'uuid' => $uuid,
                'download_url' => url("/api/pdf/download/{$uuid}")
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function watermark(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'config' => 'required|string'
        ]);

        $config = json_decode($request->input('config'), true);

        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $dir = "pdf-jobs/{$uuid}";
        
        $path = $request->file('file')->store($dir, 'local');
        $absolutePath = storage_path("app/private/{$path}");

        try {
            $outputPath = $this->pdfService->addWatermark($absolutePath, $config);
            
            \App\Models\PdfJob::create([
                'id' => $uuid,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
                'tool_slug' => 'pdf-watermark',
                'input_files' => [$absolutePath],
                'output_file' => $outputPath,
                'status' => 'done',
                'expires_at' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'uuid' => $uuid,
                'download_url' => url("/api/pdf/download/{$uuid}")
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function rotate(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'rotation' => 'required|integer'
        ]);

        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $dir = "pdf-jobs/{$uuid}";
        
        $path = $request->file('file')->store($dir, 'local');
        $absolutePath = storage_path("app/private/{$path}");

        try {
            // we pass the single rotation angle to apply to all pages
            $outputPath = $this->pdfService->rotatePdf($absolutePath, ['all' => $request->input('rotation')]);
            
            \App\Models\PdfJob::create([
                'id' => $uuid,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
                'tool_slug' => 'pdf-rotate',
                'input_files' => [$absolutePath],
                'output_file' => $outputPath,
                'status' => 'done',
                'expires_at' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'uuid' => $uuid,
                'download_url' => url("/api/pdf/download/{$uuid}")
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function extractPages(Request $request) { return response()->json(); }
    public function pdfToWord(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:pdf'
        ]);

        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $dir = "pdf-jobs/{$uuid}";
        
        $path = $request->file('file')->store($dir, 'local');
        $absolutePath = storage_path("app/private/{$path}");

        try {
            $outputPath = $this->pdfService->pdfToWord($absolutePath);
            
            \App\Models\PdfJob::create([
                'id' => $uuid,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
                'tool_slug' => 'pdf-to-word',
                'input_files' => [$absolutePath],
                'output_file' => $outputPath,
                'status' => 'done',
                'expires_at' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'uuid' => $uuid,
                'download_url' => url("/api/pdf/download/{$uuid}")
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function pdfToImages(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'format' => 'required|string|in:jpg,png'
        ]);

        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $dir = "pdf-jobs/{$uuid}";
        
        $path = $request->file('file')->store($dir, 'local');
        $absolutePath = storage_path("app/private/{$path}");

        try {
            $images = $this->pdfService->pdfToImages($absolutePath, $request->input('format'));
            
            // Zip the images
            $zipPath = storage_path("app/private/pdf-jobs/{$uuid}/images.zip");
            $zip = new \PhpZip\ZipFile();
            foreach ($images as $index => $imagePath) {
                $zip->addFile($imagePath, 'page-' . ($index + 1) . '.' . $request->input('format'));
            }
            $zip->saveAsFile($zipPath);
            $zip->close();

            \App\Models\PdfJob::create([
                'id' => $uuid,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
                'tool_slug' => 'pdf-to-images',
                'input_files' => [$absolutePath],
                'output_file' => $zipPath,
                'status' => 'done',
                'expires_at' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'uuid' => $uuid,
                'download_url' => url("/api/pdf/download/{$uuid}")
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function imagesToPdf(Request $request) {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|image'
        ]);

        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $dir = "pdf-jobs/{$uuid}";
        
        $paths = [];
        foreach ($request->file('files') as $file) {
            $path = $file->store($dir, 'local');
            $paths[] = storage_path("app/private/{$path}");
        }

        try {
            $outputPath = $this->pdfService->imagesToPdf($paths);
            
            \App\Models\PdfJob::create([
                'id' => $uuid,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
                'tool_slug' => 'images-to-pdf',
                'input_files' => $paths,
                'output_file' => $outputPath,
                'status' => 'done',
                'expires_at' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'uuid' => $uuid,
                'download_url' => url("/api/pdf/download/{$uuid}")
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function addPageNumbers(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'config' => 'required|string'
        ]);

        $config = json_decode($request->input('config'), true);

        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $dir = "pdf-jobs/{$uuid}";
        
        $path = $request->file('file')->store($dir, 'local');
        $absolutePath = storage_path("app/private/{$path}");

        try {
            $outputPath = $this->pdfService->addPageNumbers($absolutePath, $config);
            
            \App\Models\PdfJob::create([
                'id' => $uuid,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
                'tool_slug' => 'pdf-page-numbers',
                'input_files' => [$absolutePath],
                'output_file' => $outputPath,
                'status' => 'done',
                'expires_at' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'uuid' => $uuid,
                'download_url' => url("/api/pdf/download/{$uuid}")
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function edit(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'edits' => 'required|string'
        ]);

        $edits = json_decode($request->input('edits'), true);
        $deletedPages = $request->has('deleted_pages') ? json_decode($request->input('deleted_pages'), true) : [];
        $pageOrder = $request->has('page_order') ? json_decode($request->input('page_order'), true) : [];

        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $dir = "pdf-jobs/{$uuid}";
        
        $path = $request->file('file')->store($dir, 'local');
        $absolutePath = storage_path("app/private/{$path}");

        try {
            $outputPath = $this->pdfService->editPdf($absolutePath, $edits, $deletedPages, $pageOrder);
            
            \App\Models\PdfJob::create([
                'id' => $uuid,
                'user_id' => auth('sanctum')->id(),
                'ip_address' => $request->ip(),
                'tool_slug' => 'pdf-edit',
                'input_files' => [$absolutePath],
                'output_file' => $outputPath,
                'status' => 'done',
                'expires_at' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'uuid' => $uuid,
                'download_url' => url("/api/pdf/download/{$uuid}")
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function reorderPages(Request $request) { return response()->json(); }
    
    public function download($uuid) {
        $job = \App\Models\PdfJob::where('id', $uuid)->firstOrFail();
        
        if (!file_exists($job->output_file)) {
            abort(404, 'File not found or expired');
        }
        
        return response()->download($job->output_file, basename($job->output_file));
    }
    
    public function preview($uuid) { return response()->json(); }
}
