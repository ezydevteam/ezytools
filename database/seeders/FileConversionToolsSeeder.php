<?php

namespace Database\Seeders;

use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Database\Seeder;

class FileConversionToolsSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Rename old "File Tools" (id=4) → "Image Tools" ───
        $imageCategory = ToolCategory::find(4);
        if ($imageCategory) {
            $imageCategory->update([
                'name' => 'Image Tools',
                'slug' => 'image-tools',
                'description' => 'Compress, resize, convert, and edit images right in your browser.',
                'icon' => 'PhotoIcon',
            ]);
        }

        // ─── 2. Create new "File Tools" category ───
        $fileCategory = ToolCategory::updateOrCreate(
            ['slug' => 'file-tools'],
            [
                'name' => 'File Tools',
                'description' => 'Convert between file formats — CSV, JSON, XML, Excel, and more.',
                'icon' => 'DocumentArrowDownIcon',
                'order' => 5,
                'is_active' => true,
            ]
        );

        // ─── 3. Move non-image tools to new File Tools category ───
        Tool::whereIn('component_name', [
            'QRCodeGenerator',
            'BarcodeGenerator',
            'PdfToImage',
            'ImageToPdf',
        ])->update(['category_id' => $fileCategory->id]);

        // ─── 4. Create file conversion tools ───
        $tools = [
            [
                'name' => 'CSV to Excel',
                'slug' => 'csv-to-excel',
                'short_description' => 'Convert CSV files to Excel spreadsheets',
                'description' => 'Convert your CSV files to Excel (XLSX) format. Preserves all data, columns, and formatting. Process entirely in your browser.',
                'component_name' => 'CsvToExcel',
                'icon' => 'TableCellsIcon',
                'order' => 1,
            ],
            [
                'name' => 'Excel to CSV',
                'slug' => 'excel-to-csv',
                'short_description' => 'Convert Excel spreadsheets to CSV format',
                'description' => 'Convert your Excel (XLSX/XLS) files to CSV format. Select specific sheets and encoding options.',
                'component_name' => 'ExcelToCsv',
                'icon' => 'TableCellsIcon',
                'order' => 2,
            ],
            [
                'name' => 'JSON to CSV',
                'slug' => 'json-to-csv',
                'short_description' => 'Convert JSON data to CSV spreadsheets',
                'description' => 'Convert JSON arrays or objects to CSV format. Automatically detects nested structures and flattens them into columns.',
                'component_name' => 'JsonToCsv',
                'icon' => 'CodeBracketIcon',
                'order' => 3,
            ],
            [
                'name' => 'CSV to JSON',
                'slug' => 'csv-to-json',
                'short_description' => 'Convert CSV files to JSON format',
                'description' => 'Convert CSV data to JSON arrays or objects. Choose output format and configure parsing options.',
                'component_name' => 'CsvToJson',
                'icon' => 'CodeBracketSquareIcon',
                'order' => 4,
            ],
            [
                'name' => 'XML to Excel',
                'slug' => 'xml-to-excel',
                'short_description' => 'Convert XML data to Excel spreadsheets',
                'description' => 'Convert XML files to Excel (XLSX) format. Parses nested XML structures into organized spreadsheet rows and columns.',
                'component_name' => 'XmlToExcel',
                'icon' => 'DocumentTextIcon',
                'order' => 5,
            ],
            [
                'name' => 'XML to JSON',
                'slug' => 'xml-to-json',
                'short_description' => 'Convert XML data to JSON format',
                'description' => 'Convert XML documents to clean JSON. Handles nested elements, attributes, and arrays automatically.',
                'component_name' => 'XmlToJson',
                'icon' => 'ArrowsRightLeftIcon',
                'order' => 6,
            ],
            [
                'name' => 'JSON to XML',
                'slug' => 'json-to-xml',
                'short_description' => 'Convert JSON data to XML format',
                'description' => 'Convert JSON objects and arrays to well-formatted XML. Configure root element name and indentation.',
                'component_name' => 'JsonToXml',
                'icon' => 'ArrowsRightLeftIcon',
                'order' => 7,
            ],
            [
                'name' => 'JSON to Excel',
                'slug' => 'json-to-excel',
                'short_description' => 'Convert JSON data to Excel spreadsheets',
                'description' => 'Convert JSON arrays to XLSX spreadsheets. Automatically maps keys to columns and values to rows.',
                'component_name' => 'JsonToExcel',
                'icon' => 'TableCellsIcon',
                'order' => 8,
            ],
            [
                'name' => 'TSV to CSV',
                'slug' => 'tsv-to-csv',
                'short_description' => 'Convert tab-separated values to CSV',
                'description' => 'Convert TSV (tab-separated) files to CSV (comma-separated) format. Quick and easy conversion.',
                'component_name' => 'TsvToCsv',
                'icon' => 'DocumentDuplicateIcon',
                'order' => 9,
            ],
            [
                'name' => 'Markdown to HTML',
                'slug' => 'markdown-to-html',
                'short_description' => 'Convert Markdown text to HTML',
                'description' => 'Convert Markdown to clean HTML. Supports headings, lists, code blocks, tables, links, and images.',
                'component_name' => 'MarkdownToHtml',
                'icon' => 'DocumentTextIcon',
                'order' => 10,
            ],
        ];

        foreach ($tools as $toolData) {
            Tool::updateOrCreate(
                ['slug' => $toolData['slug']],
                array_merge($toolData, [
                    'category_id' => $fileCategory->id,
                    'is_active' => true,
                    'is_premium' => false,
                    'daily_limit_free' => 20,
                    'daily_limit_pro' => -1,
                    'usage_count' => 0,
                ])
            );
        }
    }
}
