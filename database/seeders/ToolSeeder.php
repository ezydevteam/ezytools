<?php

namespace Database\Seeders;

use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Database\Seeder;

class ToolSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ToolCategory::all()->keyBy('slug');
        if ($categories->isEmpty()) return;

        $tools = [
            // Text Tools
            ['category' => 'text-tools', 'name' => 'Bijoy to Unicode', 'short_description' => 'Convert Bijoy font text to Unicode format', 'slug' => 'bijoy-to-unicode', 'component_name' => 'BijoyToUnicode', 'icon' => 'LanguageIcon', 'order' => 1],
            ['category' => 'text-tools', 'name' => 'Unicode to Bijoy', 'short_description' => 'Convert Unicode text to Bijoy font format', 'slug' => 'unicode-to-bijoy', 'component_name' => 'UnicodeToBijoy', 'icon' => 'LanguageIcon', 'order' => 2],
            ['category' => 'text-tools', 'name' => 'Bangla Word Counter', 'short_description' => 'Count words, characters, and sentences in Bangla', 'slug' => 'bangla-word-counter', 'component_name' => 'BanglaWordCounter', 'icon' => 'DocumentTextIcon', 'order' => 3],
            ['category' => 'text-tools', 'name' => 'English Word Counter', 'short_description' => 'Count words, characters, and sentences in English', 'slug' => 'english-word-counter', 'component_name' => 'EnglishWordCounter', 'icon' => 'DocumentTextIcon', 'order' => 4],
            ['category' => 'text-tools', 'name' => 'Text Case Converter', 'short_description' => 'Convert text to UPPERCASE, lowercase, Title Case, etc.', 'slug' => 'text-case-converter', 'component_name' => 'TextCaseConverter', 'icon' => 'ArrowsUpDownIcon', 'order' => 5],
            ['category' => 'text-tools', 'name' => 'Duplicate Line Remover', 'short_description' => 'Remove duplicate lines from your text', 'slug' => 'duplicate-line-remover', 'component_name' => 'DuplicateLineRemover', 'icon' => 'BarsArrowDownIcon', 'order' => 6],
            ['category' => 'text-tools', 'name' => 'Text Reverser', 'short_description' => 'Reverse the characters or words in your text', 'slug' => 'text-reverser', 'component_name' => 'TextReverser', 'icon' => 'ArrowPathIcon', 'order' => 7],
            ['category' => 'text-tools', 'name' => 'Whitespace Remover', 'short_description' => 'Remove extra spaces and line breaks from text', 'slug' => 'whitespace-remover', 'component_name' => 'WhitespaceRemover', 'icon' => 'DocumentMinusIcon', 'order' => 8],
            ['category' => 'text-tools', 'name' => 'Text to Slug', 'short_description' => 'Generate URL-friendly slugs from any text', 'slug' => 'text-to-slug', 'component_name' => 'TextToSlug', 'icon' => 'LinkIcon', 'order' => 9],
            ['category' => 'text-tools', 'name' => 'Bangla OCR', 'short_description' => 'Extract Bangla and English text from images', 'slug' => 'bangla-ocr', 'component_name' => 'BanglaOcr', 'icon' => 'DocumentMagnifyingGlassIcon', 'order' => 10],

            // Calculators
            ['category' => 'calculators', 'name' => 'Land Converter', 'short_description' => 'Convert land measurements easily', 'slug' => 'land-converter', 'component_name' => 'LandConverter', 'icon' => 'MapIcon', 'order' => 10],
            ['category' => 'calculators', 'name' => 'EMI Calculator', 'short_description' => 'Calculate Equated Monthly Installment for loans', 'slug' => 'emi-calculator', 'component_name' => 'EMICalculator', 'icon' => 'BanknotesIcon', 'order' => 11],
            ['category' => 'calculators', 'name' => 'BD VAT Calculator', 'short_description' => 'Calculate VAT for Bangladesh standards', 'slug' => 'bd-vat-calculator', 'component_name' => 'BDVatCalculator', 'icon' => 'ReceiptPercentIcon', 'order' => 12],
            ['category' => 'calculators', 'name' => 'Salary Calculator', 'short_description' => 'Calculate net and gross salary breakdowns', 'slug' => 'salary-calculator', 'component_name' => 'SalaryCalculator', 'icon' => 'BanknotesIcon', 'order' => 13],
            ['category' => 'calculators', 'name' => 'CGPA Calculator', 'short_description' => 'Calculate your university CGPA quickly', 'slug' => 'cgpa-calculator', 'component_name' => 'CGPACalculator', 'icon' => 'AcademicCapIcon', 'order' => 14],
            ['category' => 'calculators', 'name' => 'SSC/HSC GPA Calculator', 'short_description' => 'Calculate SSC and HSC board exam GPA', 'slug' => 'ssc-hsc-gpa-calculator', 'component_name' => 'SscHscGpaCalculator', 'icon' => 'AcademicCapIcon', 'order' => 15],
            ['category' => 'calculators', 'name' => 'Age Calculator', 'short_description' => 'Calculate exact age in years, months, and days', 'slug' => 'age-calculator', 'component_name' => 'AgeCalculator', 'icon' => 'CalendarDaysIcon', 'order' => 16],
            ['category' => 'calculators', 'name' => 'Date Difference Calculator', 'short_description' => 'Find the duration between two specific dates', 'slug' => 'date-difference-calculator', 'component_name' => 'DateDifferenceCalculator', 'icon' => 'CalendarDaysIcon', 'order' => 17],
            ['category' => 'calculators', 'name' => 'Percentage Calculator', 'short_description' => 'Calculate percentages, discounts, and markup', 'slug' => 'percentage-calculator', 'component_name' => 'PercentageCalculator', 'icon' => 'ReceiptPercentIcon', 'order' => 18],
            ['category' => 'calculators', 'name' => 'Discount Calculator', 'short_description' => 'Calculate final price after applying discount', 'slug' => 'discount-calculator', 'component_name' => 'DiscountCalculator', 'icon' => 'TagIcon', 'order' => 19],
            ['category' => 'calculators', 'name' => 'Profit/Loss Calculator', 'short_description' => 'Calculate business profit, loss, and margin', 'slug' => 'profit-loss-calculator', 'component_name' => 'ProfitLossCalculator', 'icon' => 'ChartBarIcon', 'order' => 20],
            ['category' => 'calculators', 'name' => 'Simple Interest Calculator', 'short_description' => 'Calculate simple interest on your investments', 'slug' => 'simple-interest-calculator', 'component_name' => 'SimpleInterestCalculator', 'icon' => 'ChartPieIcon', 'order' => 21],
            ['category' => 'calculators', 'name' => 'Compound Interest Calculator', 'short_description' => 'Calculate compound interest and total returns', 'slug' => 'compound-interest-calculator', 'component_name' => 'CompoundInterestCalculator', 'icon' => 'ChartPieIcon', 'order' => 22],
            ['category' => 'calculators', 'name' => 'BMI Calculator', 'short_description' => 'Calculate your Body Mass Index', 'slug' => 'bmi-calculator', 'component_name' => 'BMICalculator', 'icon' => 'ScaleIcon', 'order' => 23],
            ['category' => 'calculators', 'name' => 'ROI Calculator', 'short_description' => 'Calculate Return on Investment percentage', 'slug' => 'roi-calculator', 'component_name' => 'ROICalculator', 'icon' => 'ArrowTrendingUpIcon', 'order' => 24],

            // Date & Time
            ['category' => 'date-time', 'name' => 'Bangla Date Converter', 'short_description' => 'Convert between Gregorian and Bangla calendars', 'slug' => 'bangla-date-converter', 'component_name' => 'BanglaDateConverter', 'icon' => 'CalendarIcon', 'order' => 25],
            ['category' => 'date-time', 'name' => 'Prayer Time Calculator', 'short_description' => 'Find accurate Islamic prayer times', 'slug' => 'prayer-time-calculator', 'component_name' => 'PrayerTimeCalculator', 'icon' => 'ClockIcon', 'order' => 26],
            ['category' => 'date-time', 'name' => 'Day of Week Finder', 'short_description' => 'Find out what day of the week a date falls on', 'slug' => 'day-of-week-finder', 'component_name' => 'DayOfWeekFinder', 'icon' => 'CalendarDaysIcon', 'order' => 27],
            ['category' => 'date-time', 'name' => 'Working Days Calculator', 'short_description' => 'Calculate working days between two dates', 'slug' => 'working-days-calculator', 'component_name' => 'WorkingDaysCalculator', 'icon' => 'BriefcaseIcon', 'order' => 28],

            // File Tools
            ['category' => 'file-tools', 'name' => 'Image Compressor', 'short_description' => 'Compress images without losing quality', 'slug' => 'image-compressor', 'component_name' => 'ImageCompressor', 'icon' => 'PhotoIcon', 'order' => 29, 'is_premium' => true],
            ['category' => 'file-tools', 'name' => 'Image Format Converter', 'short_description' => 'Convert images between PNG, JPG, WEBP, etc.', 'slug' => 'image-format-converter', 'component_name' => 'ImageFormatConverter', 'icon' => 'ArrowsRightLeftIcon', 'order' => 30],
            ['category' => 'file-tools', 'name' => 'Image Resizer', 'short_description' => 'Resize images to specific dimensions', 'slug' => 'image-resizer', 'component_name' => 'ImageResizer', 'icon' => 'ArrowsPointingOutIcon', 'order' => 31],
            ['category' => 'file-tools', 'name' => 'QR Code Generator', 'short_description' => 'Create custom QR codes instantly', 'slug' => 'qr-code-generator', 'component_name' => 'QRCodeGenerator', 'icon' => 'QrCodeIcon', 'order' => 32],
            ['category' => 'file-tools', 'name' => 'Barcode Generator', 'short_description' => 'Generate various types of barcodes', 'slug' => 'barcode-generator', 'component_name' => 'BarcodeGenerator', 'icon' => 'Bars4Icon', 'order' => 33],
            ['category' => 'file-tools', 'name' => 'PDF to Image', 'short_description' => 'Convert PDF pages into high-quality images', 'slug' => 'pdf-to-image', 'component_name' => 'PdfToImage', 'icon' => 'DocumentIcon', 'order' => 34, 'is_premium' => true],
            ['category' => 'file-tools', 'name' => 'Image to PDF', 'short_description' => 'Combine multiple images into a single PDF', 'slug' => 'image-to-pdf', 'component_name' => 'ImageToPdf', 'icon' => 'DocumentIcon', 'order' => 35, 'is_premium' => true],

            // Business Tools
            ['category' => 'business-tools', 'name' => 'Invoice Generator', 'short_description' => 'Create professional invoices for your business', 'slug' => 'invoice-generator', 'component_name' => 'InvoiceGenerator', 'icon' => 'DocumentTextIcon', 'order' => 36, 'is_premium' => true],
            ['category' => 'business-tools', 'name' => 'Salary Slip Generator', 'short_description' => 'Generate employee salary slips easily', 'slug' => 'salary-slip-generator', 'component_name' => 'SalarySlipGenerator', 'icon' => 'DocumentCheckIcon', 'order' => 37, 'is_premium' => true],
            ['category' => 'business-tools', 'name' => 'Receipt Generator', 'short_description' => 'Create custom payment receipts', 'slug' => 'receipt-generator', 'component_name' => 'ReceiptGenerator', 'icon' => 'DocumentTextIcon', 'order' => 38],
            ['category' => 'business-tools', 'name' => 'Business Card Info Formatter', 'short_description' => 'Format contact info for business cards', 'slug' => 'business-card-info-formatter', 'component_name' => 'BusinessCardInfoFormatter', 'icon' => 'IdentificationIcon', 'order' => 39],

            // Developer Tools
            ['category' => 'developer-tools', 'name' => 'JSON Formatter & Validator', 'short_description' => 'Format and validate JSON code', 'slug' => 'json-formatter', 'component_name' => 'JSONFormatter', 'icon' => 'CodeBracketIcon', 'order' => 40],
            ['category' => 'developer-tools', 'name' => 'Base64 Encoder/Decoder', 'short_description' => 'Encode and decode Base64 strings', 'slug' => 'base64-encoder-decoder', 'component_name' => 'Base64EncoderDecoder', 'icon' => 'CodeBracketSquareIcon', 'order' => 41],
            ['category' => 'developer-tools', 'name' => 'URL Encoder/Decoder', 'short_description' => 'Encode and decode URLs safely', 'slug' => 'url-encoder-decoder', 'component_name' => 'UrlEncoderDecoder', 'icon' => 'LinkIcon', 'order' => 42],
            ['category' => 'developer-tools', 'name' => 'HTML Encoder/Decoder', 'short_description' => 'Encode and decode HTML entities', 'slug' => 'html-encoder-decoder', 'component_name' => 'HtmlEncoderDecoder', 'icon' => 'CodeBracketIcon', 'order' => 43],
            ['category' => 'developer-tools', 'name' => 'MD5 Hash Generator', 'short_description' => 'Generate MD5 hashes from any string', 'slug' => 'md5-hash-generator', 'component_name' => 'Md5HashGenerator', 'icon' => 'HashtagIcon', 'order' => 44],
            ['category' => 'developer-tools', 'name' => 'SHA256 Hash Generator', 'short_description' => 'Generate secure SHA-256 hashes', 'slug' => 'sha256-hash-generator', 'component_name' => 'Sha256HashGenerator', 'icon' => 'HashtagIcon', 'order' => 45],
            ['category' => 'developer-tools', 'name' => 'Password Generator', 'short_description' => 'Generate strong and secure passwords', 'slug' => 'password-generator', 'component_name' => 'PasswordGenerator', 'icon' => 'KeyIcon', 'order' => 46],
            ['category' => 'developer-tools', 'name' => 'Lorem Ipsum Generator', 'short_description' => 'Generate placeholder text for mockups', 'slug' => 'lorem-ipsum-generator', 'component_name' => 'LoremIpsumGenerator', 'icon' => 'DocumentTextIcon', 'order' => 47],
            ['category' => 'developer-tools', 'name' => 'Color Picker & Converter', 'short_description' => 'Pick colors and convert between HEX, RGB, HSL', 'slug' => 'color-picker-converter', 'component_name' => 'ColorPickerConverter', 'icon' => 'SwatchIcon', 'order' => 48],
            ['category' => 'developer-tools', 'name' => 'CSS Minifier', 'short_description' => 'Minify CSS code to reduce file size', 'slug' => 'css-minifier', 'component_name' => 'CssMinifier', 'icon' => 'Bars3BottomRightIcon', 'order' => 49],
            ['category' => 'developer-tools', 'name' => 'JS Minifier', 'short_description' => 'Minify JavaScript code to improve performance', 'slug' => 'js-minifier', 'component_name' => 'JsMinifier', 'icon' => 'Bars3BottomRightIcon', 'order' => 50],

            // Web Tools
            ['category' => 'web-tools', 'name' => 'Meta Tag Generator', 'short_description' => 'Generate SEO-friendly HTML meta tags', 'slug' => 'meta-tag-generator', 'component_name' => 'MetaTagGenerator', 'icon' => 'GlobeAltIcon', 'order' => 51],
            ['category' => 'web-tools', 'name' => 'Open Graph Generator', 'short_description' => 'Generate Open Graph tags for social sharing', 'slug' => 'open-graph-generator', 'component_name' => 'OpenGraphGenerator', 'icon' => 'ShareIcon', 'order' => 52],
            ['category' => 'web-tools', 'name' => 'Robots.txt Generator', 'short_description' => 'Create robots.txt files for search engines', 'slug' => 'robots-txt-generator', 'component_name' => 'RobotsTxtGenerator', 'icon' => 'DocumentIcon', 'order' => 53],
            ['category' => 'web-tools', 'name' => 'Sitemap Generator', 'short_description' => 'Generate XML sitemaps for your website', 'slug' => 'sitemap-generator', 'component_name' => 'SitemapGenerator', 'icon' => 'MapIcon', 'order' => 54],
            ['category' => 'web-tools', 'name' => 'HTTP Status Code Checker', 'short_description' => 'Check HTTP status codes of any URL', 'slug' => 'http-status-code-checker', 'component_name' => 'HttpStatusCodeChecker', 'icon' => 'ServerIcon', 'order' => 55],

            // Unit Converters
            ['category' => 'unit-converters', 'name' => 'Length Converter', 'short_description' => 'Convert between meters, feet, inches, etc.', 'slug' => 'length-converter', 'component_name' => 'LengthConverter', 'icon' => 'ArrowsRightLeftIcon', 'order' => 56],
            ['category' => 'unit-converters', 'name' => 'Weight Converter', 'short_description' => 'Convert between kilograms, pounds, ounces, etc.', 'slug' => 'weight-converter', 'component_name' => 'WeightConverter', 'icon' => 'ScaleIcon', 'order' => 57],
            ['category' => 'unit-converters', 'name' => 'Temperature Converter', 'short_description' => 'Convert between Celsius, Fahrenheit, and Kelvin', 'slug' => 'temperature-converter', 'component_name' => 'TemperatureConverter', 'icon' => 'FireIcon', 'order' => 58],
            ['category' => 'unit-converters', 'name' => 'Speed Converter', 'short_description' => 'Convert between km/h, mph, m/s, etc.', 'slug' => 'speed-converter', 'component_name' => 'SpeedConverter', 'icon' => 'RocketLaunchIcon', 'order' => 59],
            ['category' => 'unit-converters', 'name' => 'Data Size Converter', 'short_description' => 'Convert between MB, GB, TB, etc.', 'slug' => 'data-size-converter', 'component_name' => 'DataSizeConverter', 'icon' => 'CircleStackIcon', 'order' => 60],
            ['category' => 'unit-converters', 'name' => 'Currency Converter', 'short_description' => 'Convert between various world currencies', 'slug' => 'currency-converter', 'component_name' => 'CurrencyConverter', 'icon' => 'CurrencyDollarIcon', 'order' => 61],
        ];

        foreach ($tools as $toolData) {
            $categoryId = $categories[$toolData['category']]->id ?? null;
            if ($categoryId) {
                Tool::updateOrCreate(
                    ['slug' => $toolData['slug']],
                    [
                        'category_id' => $categoryId,
                        'name' => $toolData['name'],
                        'short_description' => $toolData['short_description'],
                        'component_name' => $toolData['component_name'],
                        'icon' => $toolData['icon'],
                        'order' => $toolData['order'],
                        'is_premium' => $toolData['is_premium'] ?? false,
                        'is_active' => true,
                        'daily_limit_free' => 10,
                        'daily_limit_pro' => -1,
                    ]
                );
            }
        }
    }
}
