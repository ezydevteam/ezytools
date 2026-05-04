<?php

namespace Database\Seeders;

use App\Models\Tool;
use Illuminate\Database\Seeder;

class ToolMetaKeywordsSeeder extends Seeder
{
    public function run(): void
    {
        $keywords = [
            // Text Tools
            'bijoy-to-unicode'       => 'bijoy to unicode, bangla converter, bijoy keyboard, unicode bangla, text converter, bangla typing',
            'unicode-to-bijoy'       => 'unicode to bijoy, bangla converter, unicode keyboard, bijoy bangla, text converter, bangla typing',
            'bangla-word-counter'    => 'bangla word counter, word count, character count, bangla text, শব্দ গণনা, বাংলা',
            'text-case-converter'    => 'text case converter, uppercase, lowercase, title case, sentence case, text transform',
            'english-word-counter'   => 'word counter, character counter, text counter, word count online, reading time',
            'duplicate-line-remover' => 'duplicate line remover, remove duplicates, text cleaner, unique lines, dedup',
            'text-reverser'          => 'text reverser, reverse text, mirror text, backwards text, string reverse',
            'whitespace-remover'     => 'whitespace remover, remove spaces, trim whitespace, text cleaner, clean text',
            'text-to-slug'           => 'text to slug, slug generator, url slug, permalink generator, seo slug',

            // Calculators
            'land-converter'         => 'land converter, land measurement, bigha, katha, decimal, acre, land calculator bd',
            'emi-calculator'         => 'emi calculator, loan calculator, monthly installment, emi formula, bank loan',
            'bd-vat-calculator'      => 'vat calculator, bangladesh vat, bd vat, tax calculator, vat 15%',
            'age-calculator'         => 'age calculator, date of birth, age finder, birthday calculator, age in days',
            'cgpa-calculator'        => 'cgpa calculator, gpa calculator, grade point, university gpa, academic calculator',
            'salary-calculator'      => 'salary calculator, take home pay, tax deduction, net salary, gross salary',
            'ssc-hsc-gpa-calculator' => 'ssc gpa calculator, hsc gpa calculator, board exam, gpa calculation, bangladesh education',
            'percentage-calculator'  => 'percentage calculator, percent, calculate percentage, percentage formula',
            'discount-calculator'    => 'discount calculator, sale price, percentage off, discount percentage, savings',
            'profit-loss-calculator' => 'profit loss calculator, business calculator, margin calculator, markup, revenue',
            'simple-interest-calculator'   => 'simple interest calculator, interest rate, principal amount, financial calculator',
            'compound-interest-calculator' => 'compound interest calculator, compound rate, investment calculator, savings growth',
            'bmi-calculator'         => 'bmi calculator, body mass index, weight calculator, health calculator, bmi chart',
            'roi-calculator'         => 'roi calculator, return on investment, investment return, profit calculator',

            // Date & Time
            'bangla-date-converter'  => 'bangla date converter, bangla calendar, bengali date, বাংলা তারিখ, date conversion',
            'prayer-time-calculator' => 'prayer time, namaz time, salah time, islamic prayer, muslim prayer time bangladesh',
            'date-difference-calculator' => 'date difference, days between dates, date calculator, duration calculator',
            'day-of-week-finder'     => 'day of week, what day, day finder, day calculator, weekday finder',
            'working-days-calculator'=> 'working days calculator, business days, weekday counter, work days between dates',

            // Image Tools
            'image-compressor'       => 'image compressor, compress image, reduce image size, image optimizer, jpeg png compress',
            'qr-code-generator'     => 'qr code generator, qr code maker, create qr code, free qr code, qr generator',
            'image-format-converter' => 'image converter, convert image format, jpg to png, png to webp, image format',
            'image-resizer'          => 'image resizer, resize image online, change image size, photo resizer, scale image',
            'barcode-generator'      => 'barcode generator, create barcode, barcode maker, ean barcode, upc barcode',
            'image-background-remover' => 'background remover, remove bg, transparent background, image cutout, photo background',
            'collage-maker'          => 'collage maker, photo collage, image collage, picture collage, photo grid',
            'youtube-cover-photo-maker' => 'youtube cover photo, youtube banner, channel art, youtube thumbnail, banner maker',
            'facebook-cover-photo-maker' => 'facebook cover photo, fb cover, facebook banner, social media cover, cover maker',
            'favicon-maker'          => 'favicon maker, favicon generator, ico maker, website icon, browser icon',
            'jpg-to-png-converter'   => 'jpg to png, convert jpg png, image converter, photo format, lossless conversion',
            'jpg-to-webp-converter'  => 'jpg to webp, convert webp, webp converter, image optimization, web images',
            'png-to-svg-converter'   => 'png to svg, raster to vector, svg converter, vector image, scalable graphics',
            'png-to-jpg-converter'   => 'png to jpg, convert png, image converter, photo format, file size reduce',
            'webp-to-jpg-png-converter' => 'webp to jpg, webp to png, webp converter, image format, photo conversion',

            // Business Tools
            'invoice-generator'      => 'invoice generator, create invoice, free invoice, invoice maker, billing template',
            'salary-slip-generator'  => 'salary slip generator, pay slip, payroll, employee salary, salary statement',
            'receipt-generator'      => 'receipt generator, create receipt, payment receipt, receipt maker, receipt template',
            'business-card-info-formatter' => 'business card formatter, vcard, contact info, business card template, card maker',

            // Developer Tools
            'json-formatter'         => 'json formatter, json validator, json beautifier, json prettify, json parser',
            'base64-encoder-decoder' => 'base64 encoder, base64 decoder, encode base64, decode base64, data encoding',
            'password-generator'     => 'password generator, secure password, random password, strong password, password creator',
            'url-encoder-decoder'    => 'url encoder, url decoder, encode url, percent encoding, uri encoding',
            'html-encoder-decoder'   => 'html encoder, html decoder, html entities, encode html, special characters',
            'md5-hash-generator'     => 'md5 hash, md5 generator, hash calculator, md5 checksum, file hash',
            'sha256-hash-generator'  => 'sha256 hash, sha256 generator, hash calculator, sha256 checksum, secure hash',
            'lorem-ipsum-generator'  => 'lorem ipsum generator, dummy text, placeholder text, filler text, sample text',
            'color-picker-converter' => 'color picker, hex to rgb, color converter, color palette, css colors',
            'css-minifier'           => 'css minifier, minify css, compress css, css optimizer, css compressor',
            'js-minifier'            => 'js minifier, minify javascript, compress js, javascript optimizer, code minifier',
            'markdown-to-html'       => 'markdown to html, md converter, markdown parser, markdown renderer, convert markdown',

            // Web Tools
            'meta-tag-generator'     => 'meta tag generator, seo meta tags, html meta, og tags, meta description generator',
            'open-graph-generator'   => 'open graph generator, og tags, social media meta, facebook meta, twitter cards',
            'robots-txt-generator'   => 'robots.txt generator, robots file, search engine crawler, seo robots, web crawler',
            'sitemap-generator'      => 'sitemap generator, xml sitemap, seo sitemap, website sitemap, search engine',
            'http-status-code-checker' => 'http status codes, status code checker, 404, 200, 301, http response codes',
            'url-shortener'          => 'url shortener, shorten url, link shortener, short link, tiny url, link compressor',
            'dns-lookup'             => 'dns lookup, dns checker, domain dns, a record, mx record, nameserver lookup',
            'ip-lookup'              => 'ip lookup, ip geolocation, ip address finder, my ip, ip location, whats my ip',
            'whois-lookup'           => 'whois lookup, domain whois, domain registration, domain owner, whois checker',
            'google-cache-checker'   => 'google cache checker, cached page, google cache, web cache, cache status',
            'meta-tags-checker'      => 'meta tags checker, seo checker, meta analyzer, website meta, seo audit',
            'ping-tool'              => 'ping tool, ping test, server ping, website ping, latency test, uptime check',
            'hosting-checker'        => 'hosting checker, who is hosting, web hosting detector, hosting provider, server info',

            // Unit Converters
            'length-converter'       => 'length converter, meter to feet, cm to inch, unit converter, distance converter',
            'weight-converter'       => 'weight converter, kg to lbs, gram to ounce, mass converter, weight units',
            'temperature-converter'  => 'temperature converter, celsius to fahrenheit, kelvin, temp converter, degree converter',
            'speed-converter'        => 'speed converter, kmh to mph, velocity converter, speed units, unit conversion',
            'data-size-converter'    => 'data size converter, mb to gb, byte converter, storage converter, file size',
            'currency-converter'     => 'currency converter, exchange rate, bdt to usd, forex, money converter',

            // AI Studio
            'ai-paraphraser'         => 'ai paraphraser, paraphrase tool, rewrite text, bangla paraphraser, text rewriter ai',
            'ai-summarizer'          => 'ai summarizer, text summarizer, article summary, summarize online, auto summary',
            'ai-grammar-checker'     => 'ai grammar checker, grammar fix, spelling check, proofreader, english grammar ai',
            'ai-translator'          => 'ai translator, translate online, bangla translation, language translator, ai translate',
            'ai-email-writer'        => 'ai email writer, email generator, compose email, professional email, email template ai',
            'ai-article-generator'   => 'ai article generator, article writer, content generator, blog post ai, auto article',
            'ai-title-generator'     => 'ai title generator, headline generator, blog title, catchy title, title ideas',
            'ai-meta-description'    => 'ai meta description, seo description, meta tag writer, search snippet, seo ai',
            'ai-youtube-description' => 'ai youtube description, video description, youtube seo, channel description, yt description',
            'ai-social-media-post'   => 'ai social media post, social post generator, facebook post, instagram caption ai',
            'ai-code-explainer'      => 'ai code explainer, explain code, code review, programming helper, code analysis',
            'ai-cover-letter-writer' => 'ai cover letter, cover letter generator, job application, resume cover letter ai',
            'ai-resume-summary'      => 'ai resume summary, resume builder, cv summary, professional summary, career summary',
            'ai-interview-questions' => 'ai interview questions, interview prep, job interview, mock interview, hr questions',
            'ai-tagline-generator'   => 'ai tagline generator, slogan maker, brand tagline, catchy slogan, business tagline',
            'ai-essay-outline'       => 'ai essay outline, essay planner, essay structure, academic writing, essay helper',
            'ai-reply-generator'     => 'ai reply generator, auto reply, email reply, message reply, smart reply',
            'ai-product-description' => 'ai product description, product copywriter, ecommerce description, listing writer',
            'ai-startup-idea-generator' => 'ai startup idea, business idea generator, startup generator, entrepreneur ideas',
            'ai-sentence-expander'   => 'ai sentence expander, expand text, elaborate text, content expander, text enricher',
            'ai-content-studio'      => 'ai content studio, content creator, ai writer, text generator, creative writing ai',
            'ai-hashtag-generator'   => 'ai hashtag generator, trending hashtags, instagram hashtags, social media hashtags',
            'ai-youtube-script-writer' => 'ai youtube script, video script, youtube content, script writer, video outline',
            'ai-ad-copy-generator'   => 'ai ad copy, advertisement writer, google ads copy, facebook ad, marketing copy',
            'ai-story-generator'     => 'ai story generator, story writer, creative story, fiction generator, short story ai',
            'ai-poem-writer'         => 'ai poem writer, kobita writer, poetry generator, bangla poem, verse generator',
            'ai-headline-generator'  => 'ai headline generator, news headline, blog headline, catchy headline, title maker',
            'ai-wish-greeting-generator' => 'ai greeting generator, wish generator, birthday wish, eid greetings, celebration message',
            'ai-swot-analysis'       => 'ai swot analysis, business analysis, strategic planning, swot generator, business strategy',
            'ai-job-description-writer' => 'ai job description, jd writer, job posting, hiring description, recruitment',
            'ai-text-expander'       => 'ai text expander, expand content, elaborate text, text growth, content stretcher',
            'ai-tone-changer'        => 'ai tone changer, change tone, formal informal, text tone, writing style',
            'ai-fake-review-detector'=> 'ai fake review detector, review checker, spam review, authentic reviews, review analysis',
            'ai-study-notes-generator' => 'ai study notes, notes generator, revision notes, academic notes, study helper',
            'ai-citation-generator'  => 'ai citation generator, reference generator, apa citation, mla citation, bibliography',
            'ai-meeting-agenda-generator' => 'ai meeting agenda, agenda generator, meeting planner, meeting template, agenda maker',
            'ai-performance-review-writer' => 'ai performance review, employee review, appraisal writer, feedback generator',
            'ai-joke-generator'      => 'ai joke generator, funny jokes, humor generator, comedy writer, joke maker',
            'ai-proposal-writer'     => 'ai proposal writer, business proposal, project proposal, proposal template, rfp writer',
            'ai-assignment-helper'   => 'ai assignment helper, homework help, student helper, academic assistant, assignment writer',
            'ai-bd-business-proposal' => 'ai bangladesh proposal, bangla business proposal, bd business plan, ব্যবসা প্রস্তাব',
            'ai-content-detector'    => 'ai content detector, ai checker, gpt detector, ai text detection, ai plagiarism',
            'ai-detector-humanizer'  => 'ai humanizer, humanize ai text, ai to human, bypass ai detection, rewrite ai',
            'ai-voice-generator'     => 'ai voice generator, text to speech, tts, bangla voice, ai narration, voice over',

            // Video Tools
            'video-resize'           => 'video resizer, resize video, change video size, video dimensions, scale video',
            'audio-extractor'        => 'audio extractor, extract audio, video to mp3, audio from video, sound extractor',
            'video-to-gif'           => 'video to gif, gif maker, convert gif, animated gif, gif converter',
            'video-compress'         => 'video compressor, compress video, reduce video size, video optimizer, shrink video',
            'video-trimmer'          => 'video trimmer, trim video, cut video, video cutter, clip video',
            'video-merger'           => 'video merger, merge videos, combine videos, join videos, video joiner',
            'video-watermark'        => 'video watermark, add watermark, watermark video, brand video, overlay text',
            'video-thumbnail-generator' => 'video thumbnail, thumbnail maker, youtube thumbnail, video preview, thumbnail creator',
            'youtube-video-info'     => 'youtube video info, video details, youtube analyzer, video stats, yt info',
            'video-to-text'          => 'video to text, transcribe video, video transcription, speech to text, caption video',

            // File Tools
            'csv-to-excel'           => 'csv to excel, convert csv xlsx, spreadsheet converter, data converter',
            'excel-to-csv'           => 'excel to csv, xlsx to csv, spreadsheet to csv, data export',
            'json-to-csv'            => 'json to csv, convert json csv, data transformation, json export',
            'csv-to-json'            => 'csv to json, convert csv json, data conversion, json import',
            'xml-to-excel'           => 'xml to excel, convert xml xlsx, data converter, xml spreadsheet',
            'xml-to-json'            => 'xml to json, convert xml json, data transformation, xml parser',
            'json-to-xml'            => 'json to xml, convert json xml, data conversion, xml generator',
            'json-to-excel'          => 'json to excel, convert json xlsx, data export, json spreadsheet',
            'tsv-to-csv'             => 'tsv to csv, tab separated, convert tsv, data converter',

            // PDF Tools
            'pdf-editor'             => 'pdf editor, edit pdf, pdf annotator, pdf markup, online pdf editor',
            'merge-pdf'              => 'merge pdf, combine pdf, join pdf, pdf merger, pdf combiner',
            'split-pdf'              => 'split pdf, separate pdf, extract pages, pdf splitter, divide pdf',
            'compress-pdf'           => 'compress pdf, reduce pdf size, pdf compressor, shrink pdf, pdf optimizer',
            'protect-pdf'            => 'protect pdf, password pdf, encrypt pdf, secure pdf, pdf security',
            'unlock-pdf'             => 'unlock pdf, remove password, decrypt pdf, pdf unlocker, open pdf',
            'watermark-pdf'          => 'watermark pdf, add watermark, pdf stamp, brand pdf, pdf overlay',
            'rotate-pdf'             => 'rotate pdf, turn pdf, flip pdf pages, pdf rotation, orient pdf',
            'pdf-to-word'            => 'pdf to word, convert pdf docx, pdf converter, document converter',
            'pdf-to-jpg'             => 'pdf to jpg, pdf to image, convert pdf, pdf to picture, pdf export',
            'jpg-to-pdf'             => 'jpg to pdf, image to pdf, photo to pdf, picture to pdf, convert pdf',
            'add-page-numbers'       => 'add page numbers pdf, number pdf pages, pdf pagination, page numbering',
            'organize-pdf'           => 'organize pdf, reorder pdf, rearrange pages, sort pdf, pdf organizer',
            'edit-metadata'          => 'edit pdf metadata, pdf properties, document info, pdf title author',
            'repair-pdf'             => 'repair pdf, fix pdf, broken pdf, corrupt pdf, pdf recovery',
        ];

        $updated = 0;
        foreach ($keywords as $slug => $kw) {
            $count = Tool::where('slug', $slug)->whereNull('meta_keywords')->update(['meta_keywords' => $kw]);
            if ($count) $updated++;
        }

        $this->command->info("✓ Updated meta_keywords for {$updated} tools (skipped tools that already have keywords).");

        // Report tools without keywords
        $missing = Tool::whereNull('meta_keywords')->pluck('slug');
        if ($missing->count()) {
            $this->command->warn("⚠ {$missing->count()} tools still missing keywords: " . $missing->take(10)->implode(', '));
        }
    }
}
