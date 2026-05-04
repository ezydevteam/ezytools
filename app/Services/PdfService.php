<?php

namespace App\Services;

class PdfService
{
    public function mergePdfs(array $filePaths): string
    {
        $mergedPdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        foreach ($filePaths as $file) {
            $file = $this->ensureCompatiblePdf($file);
            $pageCount = $mergedPdf->setSourceFile($file);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $mergedPdf->importPage($pageNo);
                $size = $mergedPdf->getTemplateSize($templateId);
                $mergedPdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $mergedPdf->useTemplate($templateId);
            }
        }
        $outputPath = storage_path('app/private/pdf-jobs/' . \Illuminate\Support\Str::uuid() . '/merged.pdf');
        @mkdir(dirname($outputPath), 0755, true);
        $pdf->Output('F', $outputPath);
        return $outputPath;
    }

    public function splitPdf(string $filePath, array $pageRanges): array
    {
        return [];
    }

    public function compressPdf(string $filePath, string $quality): string
    {
        $outputPath = storage_path('app/private/pdf-jobs/' . \Illuminate\Support\Str::uuid() . '/compressed.pdf');
        @mkdir(dirname($outputPath), 0755, true);

        // Quality maps to PDFSETTINGS
        $settings = match($quality) {
            'screen' => '/screen', // 72 dpi
            'ebook' => '/ebook', // 150 dpi
            'printer' => '/printer', // 300 dpi
            default => '/ebook'
        };

        $gsCmd = sprintf('gs -q -dNOPAUSE -dBATCH -dSAFER -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=%s -dEmbedAllFonts=true -dSubsetFonts=true -sOutputFile=%s %s 2>&1', escapeshellarg($settings), escapeshellarg($outputPath), escapeshellarg($filePath));
        exec($gsCmd, $output, $returnVar);

        if ($returnVar !== 0) {
            $gsCmdWin = sprintf('gswin64c -q -dNOPAUSE -dBATCH -dSAFER -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=%s -dEmbedAllFonts=true -dSubsetFonts=true -sOutputFile=%s %s 2>&1', escapeshellarg($settings), escapeshellarg($outputPath), escapeshellarg($filePath));
            exec($gsCmdWin, $output, $returnVar);
        }

        if ($returnVar === 0 && file_exists($outputPath)) {
            return $outputPath;
        }

        throw new \Exception("Ghostscript is required for PDF compression. Please install Ghostscript on your server to use this feature.");
    }

    public function passwordProtect(string $filePath, string $password, array $permissions = []): string
    {
        if (!class_exists('\setasign\Fpdi\Tcpdf\Fpdi')) {
            throw new \Exception("TCPDF is required for encryption. Please run: composer require tecnickcom/tcpdf setasign/fpdi-tcpdf --ignore-platform-reqs");
        }

        $filePath = $this->ensureCompatiblePdf($filePath);

        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        $pageCount = $pdf->setSourceFile($filePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
        }

        $pdf->SetProtection($permissions, $password, null, 3, null);

        $outputPath = storage_path('app/private/pdf-jobs/' . \Illuminate\Support\Str::uuid() . '/protected.pdf');
        @mkdir(dirname($outputPath), 0755, true);
        $pdf->Output($outputPath, 'F');
        
        return $outputPath;
    }

    public function removePassword(string $filePath, string $password): string
    {
        $outputPath = storage_path('app/private/pdf-jobs/' . \Illuminate\Support\Str::uuid() . '/unlocked.pdf');
        @mkdir(dirname($outputPath), 0755, true);

        // Try qpdf
        $qpdfCmd = sprintf('qpdf --password=%s --decrypt %s %s 2>&1', escapeshellarg($password), escapeshellarg($filePath), escapeshellarg($outputPath));
        exec($qpdfCmd, $output, $returnVar);

        if ($returnVar === 0 && file_exists($outputPath)) {
            return $outputPath;
        }

        // Try ghostscript (gs / gswin64c)
        $gsCmd = sprintf('gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sPDFPassword=%s -sOutputFile=%s %s 2>&1', escapeshellarg($password), escapeshellarg($outputPath), escapeshellarg($filePath));
        exec($gsCmd, $outputGs, $returnVarGs);

        if ($returnVarGs === 0 && file_exists($outputPath)) {
            return $outputPath;
        }

        throw new \Exception("Failed to unlock PDF. Make sure the password is correct, and that 'qpdf' or 'ghostscript' is installed on your server.");
    }

    private function ensureCompatiblePdf(string $filePath): string
    {
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        
        try {
            $pdf->setSourceFile($filePath);
            return $filePath;
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'compression technique')) {
                // Try to downgrade using ghostscript if available
                $tempPath = $filePath . '.1.4.pdf';
                $gsCmd = sprintf('gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -sOutputFile=%s %s 2>&1', escapeshellarg($tempPath), escapeshellarg($filePath));
                exec($gsCmd, $output, $returnVar);
                
                if ($returnVar !== 0) {
                    $gsCmdWin = sprintf('gswin64c -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -sOutputFile=%s %s 2>&1', escapeshellarg($tempPath), escapeshellarg($filePath));
                    exec($gsCmdWin, $output, $returnVar);
                }

                if ($returnVar === 0 && file_exists($tempPath)) {
                    // verify it works now
                    try {
                        $pdf->setSourceFile($tempPath);
                        return $tempPath;
                    } catch (\Exception $e2) {
                        throw new \Exception("Ghostscript attempted to downgrade the PDF but FPDI still couldn't read it.");
                    }
                }

                throw new \Exception("This PDF uses advanced compression. To process it, you must install Ghostscript on your server.");
            }
            throw $e;
        }
    }

    public function addWatermark(string $filePath, array $config): string
    {
        if (!class_exists('\setasign\Fpdi\Tcpdf\Fpdi')) {
            throw new \Exception("TCPDF is required for watermarking. Please run: composer require tecnickcom/tcpdf setasign/fpdi-tcpdf --ignore-platform-reqs");
        }

        $filePath = $this->ensureCompatiblePdf($filePath);
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        $pageCount = $pdf->setSourceFile($filePath);

        $text = $config['text'] ?? 'CONFIDENTIAL';
        $size = $config['size'] ?? 60;
        $opacity = $config['opacity'] ?? 0.3;
        $rotation = $config['rotation'] ?? 45;
        $color = $config['color'] ?? 'gray';
        
        $rgb = [107, 114, 128]; // gray
        if ($color === 'red') $rgb = [239, 68, 68];
        if ($color === 'blue') $rgb = [59, 130, 246];
        if ($color === 'black') $rgb = [0, 0, 0];

        // Disable TCPDF's auto page break and header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pageSize = $pdf->getTemplateSize($templateId);
            $pdf->AddPage($pageSize['orientation'], [$pageSize['width'], $pageSize['height']]);
            $pdf->useTemplate($templateId);

            $pdf->SetFont('helvetica', 'B', $size);
            $pdf->SetTextColor($rgb[0], $rgb[1], $rgb[2]);
            $pdf->SetAlpha($opacity);
            
            // Calculate center
            $centerX = $pageSize['width'] / 2;
            $centerY = $pageSize['height'] / 2;

            $pdf->StartTransform();
            $pdf->Rotate($rotation, $centerX, $centerY);
            
            // Draw text centered
            $textWidth = $pdf->GetStringWidth($text);
            // $pdf->Text($x, $y, $text)
            $pdf->Text($centerX - ($textWidth / 2), $centerY, $text);
            
            $pdf->StopTransform();
            $pdf->SetAlpha(1);
        }

        $outputPath = storage_path('app/private/pdf-jobs/' . \Illuminate\Support\Str::uuid() . '/watermarked.pdf');
        @mkdir(dirname($outputPath), 0755, true);
        $pdf->Output($outputPath, 'F');
        
        return $outputPath;
    }

    public function rotatePdf(string $filePath, array $rotations): string
    {
        $filePath = $this->ensureCompatiblePdf($filePath);
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false);
        
        $pageCount = $pdf->setSourceFile($filePath);
        $rot = $rotations['all'] ?? 90;
        
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            $w = $size['width'];
            $h = $size['height'];
            
            if ($rot % 180 !== 0) {
                // Swap width and height
                $pdf->AddPage($w > $h ? 'P' : 'L', [$h, $w]);
                $pdf->StartTransform();
                // Rotate around center of the new page
                $pdf->Rotate(-$rot, $h/2, $w/2);
                
                // When rotated 90 degrees around center, the template might be offset if it's not a square.
                // We must shift it so its center aligns with the new page center.
                $dx = ($h - $w) / 2;
                $dy = ($w - $h) / 2;
                $pdf->useTemplate($templateId, $dx, $dy, $w, $h);
                $pdf->StopTransform();
            } else {
                $pdf->AddPage($size['orientation'], [$w, $h]);
                if ($rot % 360 !== 0) {
                    $pdf->StartTransform();
                    $pdf->Rotate(-$rot, $w/2, $h/2);
                    $pdf->useTemplate($templateId, 0, 0, $w, $h);
                    $pdf->StopTransform();
                } else {
                    $pdf->useTemplate($templateId, 0, 0, $w, $h);
                }
            }
        }
        
        $outputPath = storage_path('app/private/pdf-jobs/' . \Illuminate\Support\Str::uuid() . '/rotated.pdf');
        @mkdir(dirname($outputPath), 0755, true);
        $pdf->Output($outputPath, 'F');
        
        return $outputPath;
    }

    public function extractPages(string $filePath, array $pages): string
    {
        $filePath = $this->ensureCompatiblePdf($filePath);
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false);
        
        $pageCount = $pdf->setSourceFile($filePath);
        
        $pagesAdded = 0;
        foreach ($pages as $pageNo) {
            if ($pageNo < 1 || $pageNo > $pageCount) continue;
            
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
            $pagesAdded++;
        }
        
        if ($pagesAdded === 0) {
            throw new \Exception("None of the specified pages were found in the document.");
        }
        
        $outputPath = storage_path('app/private/pdf-jobs/' . \Illuminate\Support\Str::uuid() . '/extracted.pdf');
        @mkdir(dirname($outputPath), 0755, true);
        $pdf->Output($outputPath, 'F');
        
        return $outputPath;
    }

    public function pdfToWord(string $filePath): string
    {
        if (!class_exists('\Smalot\PdfParser\Parser') || !class_exists('\PhpOffice\PhpWord\PhpWord')) {
            throw new \Exception("Required libraries are missing for this conversion.");
        }

        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        
        // Basic split by newlines. Smalot extraction isn't perfect for layout, 
        // but it pulls the text successfully.
        $lines = explode("\n", $text);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $line = htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $section->addText($line);
            } else {
                $section->addTextBreak(1);
            }
        }

        $outputPath = storage_path('app/private/pdf-jobs/' . \Illuminate\Support\Str::uuid() . '/document.docx');
        @mkdir(dirname($outputPath), 0755, true);
        
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($outputPath);
        
        return $outputPath;
    }

    public function pdfToImages(string $filePath, string $format): array
    {
        $dir = dirname($filePath);
        $prefix = $dir . '/page-';
        
        $device = $format === 'png' ? 'png16m' : 'jpeg';
        
        $gsCmd = sprintf('gs -q -dNOPAUSE -dBATCH -sDEVICE=%s -r300 -sOutputFile=%s%%03d.%s %s 2>&1', escapeshellarg($device), escapeshellarg($prefix), escapeshellarg($format), escapeshellarg($filePath));
        exec($gsCmd, $output, $returnVar);

        if ($returnVar !== 0) {
            $gsCmdWin = sprintf('gswin64c -q -dNOPAUSE -dBATCH -sDEVICE=%s -r300 -sOutputFile=%s%%03d.%s %s 2>&1', escapeshellarg($device), escapeshellarg($prefix), escapeshellarg($format), escapeshellarg($filePath));
            exec($gsCmdWin, $output, $returnVar);
        }

        if ($returnVar !== 0) {
            throw new \Exception("Ghostscript is required for converting PDFs to images. Please install Ghostscript on your server.");
        }

        $files = glob($dir . '/page-*.' . $format);
        if (empty($files)) {
            throw new \Exception("Failed to extract images from PDF.");
        }
        
        sort($files);
        return $files;
    }

    public function imagesToPdf(array $imagePaths): string
    {
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false);

        foreach ($imagePaths as $imgPath) {
            $size = getimagesize($imgPath);
            if (!$size) continue;
            
            // Convert px to mm (assuming 72dpi)
            $w = $size[0] * 25.4 / 72;
            $h = $size[1] * 25.4 / 72;

            $pdf->AddPage($w > $h ? 'L' : 'P', [$w, $h]);
            $pdf->Image($imgPath, 0, 0, $w, $h);
        }

        $outputPath = storage_path('app/private/pdf-jobs/' . \Illuminate\Support\Str::uuid() . '/images.pdf');
        @mkdir(dirname($outputPath), 0755, true);
        $pdf->Output($outputPath, 'F');
        
        return $outputPath;
    }

    public function addPageNumbers(string $filePath, array $config): string
    {
        $filePath = $this->ensureCompatiblePdf($filePath);
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

        $pageCount = $pdf->setSourceFile($filePath);
        
        $position = $config['position'] ?? 'bottom-center';
        $format = $config['format'] ?? 'Page 1 of n';
        $size = $config['size'] ?? 12;

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pageSize = $pdf->getTemplateSize($templateId);
            $w = $pageSize['width'];
            $h = $pageSize['height'];
            
            $pdf->AddPage($pageSize['orientation'], [$w, $h]);
            $pdf->useTemplate($templateId);

            $text = str_replace(['1', 'n'], [$pageNo, $pageCount], $format);
            $pdf->SetFont('helvetica', '', $size);
            $pdf->SetTextColor(0, 0, 0);

            $textWidth = $pdf->GetStringWidth($text);
            $margin = 15;
            
            // X position
            if (str_contains($position, 'left')) {
                $x = $margin;
            } elseif (str_contains($position, 'right')) {
                $x = $w - $textWidth - $margin;
            } else {
                $x = ($w - $textWidth) / 2;
            }
            
            // Y position
            if (str_contains($position, 'top')) {
                $y = $margin + $size * 0.35;
            } else {
                $y = $h - $margin;
            }

            $pdf->Text($x, $y, $text);
        }

        $outputPath = storage_path('app/private/pdf-jobs/' . \Illuminate\Support\Str::uuid() . '/numbered.pdf');
        @mkdir(dirname($outputPath), 0755, true);
        $pdf->Output($outputPath, 'F');
        
        return $outputPath;
    }

    public function editPdf(string $filePath, array $edits, array $deletedPages = [], array $pageOrder = []): string
    {
        $filePath = $this->ensureCompatiblePdf($filePath);
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

        $pageCount = $pdf->setSourceFile($filePath);
        
        $pagesToRender = [];
        if (!empty($pageOrder)) {
            $pagesToRender = $pageOrder;
        } else {
            $maxPage = $pageCount;
            if (!empty($edits)) {
                $maxPage = max($pageCount, max(array_keys($edits)));
            }
            for ($i = 1; $i <= $maxPage; $i++) {
                if (!in_array($i, $deletedPages)) {
                    $pagesToRender[] = $i;
                }
            }
        }
        
        foreach ($pagesToRender as $pageNo) {

            if ($pageNo <= $pageCount) {
                // Existing PDF page
                $templateId = $pdf->importPage($pageNo);
                $pageSize = $pdf->getTemplateSize($templateId);
                $w = $pageSize['width'];
                $h = $pageSize['height'];
                
                $pdf->AddPage($pageSize['orientation'], [$w, $h]);
                $pdf->useTemplate($templateId);
            } else {
                // New blank page (A4 default)
                $pdf->AddPage('P', ['210', '297']);
                $w = 210;
                $h = 297;
            }

            if (!empty($edits[$pageNo])) {
                $dataUrl = $edits[$pageNo];
                if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
                    $base64Data = substr($dataUrl, strpos($dataUrl, ',') + 1);
                    $imageData = base64_decode($base64Data);
                    
                    // TCPDF can use '@' prefix to load image from string.
                    $pdf->Image('@' . $imageData, 0, 0, $w, $h, 'PNG');
                }
            }
        }

        $outputPath = storage_path('app/private/pdf-jobs/' . \Illuminate\Support\Str::uuid() . '/edited.pdf');
        @mkdir(dirname($outputPath), 0755, true);
        $pdf->Output($outputPath, 'F');
        
        return $outputPath;
    }
}
