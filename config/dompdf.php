<?php

return [
    'show_warnings' => false,
    'public_path' => null,
    'convert_entities' => true,

    'options' => [
        /**
         * The location of the dompdf font directory
         *
         * The location of the directory where dompdf will store fonts and font metrics
         * Note: This directory must exist and be writable by the webserver process.
         * *Please note the trailing slash.*
         *
         * Notes regarding fonts:
         * Additional .afm font metrics can be added by executing load_font.php from command line.
         *
         * Only the original "Base 14" fonts are present in the directory by
         * default. Prerequisites for using any additional font:
         * 1. The fonts must be TrueType fonts.
         * 2. You must have read access to the .ttf files.
         *
         * @var string
         */
        'font_dir' => public_path('fonts/bangla'),

        /**
         * The location of the dompdf font cache directory
         *
         * This directory contains the cached font metrics for the type of fonts
         * used by dompdf.
         * This directory can be the same as DOMPDF_FONT_DIR
         *
         * Note: This directory must exist and be writable by the webserver process.
         *
         * @var string
         */
        'font_cache' => storage_path('fonts'),

        /**
         * The location of a temporary directory.
         *
         * The directory specified must be writeable by the webserver process.
         * The temporary directory is required to download remote images and when
         * using the PclZip (starting with 1.2.0.x, before then ZipArchive was used)
         * to generate the PDF file (before DOMPDF 1.2.0.x see
         * \Dompdf\Dompdf::getTempDir).
         *
         * @var string
         */
        'temp_dir' => storage_path('app/temp'),

        'chroot' => realpath(base_path()),
        'allowed_protocols' => [
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],
        'log_output_file' => null,
        'enable_font_subsetting' => false,
        'pdf_backend' => 'CPDF',
        'default_media_type' => 'screen',
        'default_paper_size' => 'a4',
        'default_paper_orientation' => 'portrait',
        'default_font' => 'serif',
        'dpi' => 96,
        'enable_php' => false,
        'enable_javascript' => true,
        'enable_remote' => true,
        'allowed_remote_hosts' => null,
        'font_height_ratio' => 1.1,
        'enable_html5_parser' => true,

        // Unicode support for Bangla
        'unicode_enabled' => true,
        'enable_unicode' => true,
    ],
];
