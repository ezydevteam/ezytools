<?php

namespace Database\Seeders;

use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Database\Seeder;

class VideoToolsSeeder extends Seeder
{
    public function run(): void
    {
        $category = ToolCategory::updateOrCreate(
            ['slug' => 'video-tools'],
            [
                'name' => 'Video Tools',
                'description' => 'Resize, compress, convert, trim, merge and process videos right in your browser.',
                'icon' => 'VideoCameraIcon',
                'order' => 12,
                'is_active' => true,
            ]
        );

        $tools = [
            [
                'name' => 'Video Resize',
                'slug' => 'video-resize',
                'short_description' => 'Resize video dimensions to any custom resolution',
                'description' => 'Resize your video files to any custom width and height. Supports MP4, WebM and MOV formats. All processing happens in your browser — no upload needed.',
                'component_name' => 'VideoResize',
                'icon' => 'ArrowsPointingOutIcon',
                'order' => 1,
            ],
            [
                'name' => 'Audio Extractor',
                'slug' => 'audio-extractor',
                'short_description' => 'Extract audio track from any video file',
                'description' => 'Extract the audio track from your video and download it as an MP3 or WAV file. Fast, private, and works entirely in your browser.',
                'component_name' => 'AudioExtractor',
                'icon' => 'MusicalNoteIcon',
                'order' => 2,
            ],
            [
                'name' => 'Video to GIF',
                'slug' => 'video-to-gif',
                'short_description' => 'Convert video clips to animated GIF images',
                'description' => 'Convert any video clip to an animated GIF. Set start time, duration, frame rate and output size. Great for social media and messaging.',
                'component_name' => 'VideoToGif',
                'icon' => 'GifTopRightIcon',
                'order' => 3,
            ],
            [
                'name' => 'Video Compress',
                'slug' => 'video-compress',
                'short_description' => 'Compress video files to reduce file size',
                'description' => 'Reduce video file size while maintaining quality. Choose compression level and output format. Perfect for sharing on social media or email.',
                'component_name' => 'VideoCompress',
                'icon' => 'ArchiveBoxArrowDownIcon',
                'order' => 4,
            ],
            [
                'name' => 'Video Trimmer',
                'slug' => 'video-trimmer',
                'short_description' => 'Trim and cut video clips with precision',
                'description' => 'Trim your videos by selecting exact start and end times. Preview your selection before exporting. All processing happens in your browser.',
                'component_name' => 'VideoTrimmer',
                'icon' => 'ScissorsIcon',
                'order' => 5,
            ],
            [
                'name' => 'Video Merger',
                'slug' => 'video-merger',
                'short_description' => 'Merge multiple video clips into one file',
                'description' => 'Combine multiple video files into a single video. Drag and drop to reorder clips. Supports MP4, WebM formats.',
                'component_name' => 'VideoMerger',
                'icon' => 'RectangleGroupIcon',
                'order' => 6,
            ],
            [
                'name' => 'Video Watermark',
                'slug' => 'video-watermark',
                'short_description' => 'Add text or image watermark to your videos',
                'description' => 'Add a custom text or image watermark to your videos. Choose position, opacity, and size. Protect your video content from unauthorized use.',
                'component_name' => 'VideoWatermark',
                'icon' => 'ShieldCheckIcon',
                'order' => 7,
            ],
            [
                'name' => 'Video Thumbnail Generator',
                'slug' => 'video-thumbnail-generator',
                'short_description' => 'Generate thumbnails from any video at any timestamp',
                'description' => 'Extract high-quality thumbnail images from your videos at any point in time. Perfect for YouTube, social media, and content creation.',
                'component_name' => 'VideoThumbnailGenerator',
                'icon' => 'PhotoIcon',
                'order' => 8,
            ],
            [
                'name' => 'YouTube Video Info',
                'slug' => 'youtube-video-info',
                'short_description' => 'Extract metadata and info from YouTube videos',
                'description' => 'Get detailed information about any YouTube video — title, description, duration, view count, channel info, and thumbnails. No download, metadata only.',
                'component_name' => 'YoutubeVideoInfo',
                'icon' => 'InformationCircleIcon',
                'order' => 9,
            ],
            [
                'name' => 'Video to Text',
                'slug' => 'video-to-text',
                'short_description' => 'Transcribe video audio to text using AI',
                'description' => 'Automatically transcribe the audio from your videos into text using AI-powered speech recognition. Supports multiple languages including Bangla and English.',
                'component_name' => 'VideoToText',
                'icon' => 'DocumentTextIcon',
                'order' => 10,
            ],
        ];

        foreach ($tools as $toolData) {
            Tool::updateOrCreate(
                ['slug' => $toolData['slug']],
                array_merge($toolData, [
                    'category_id' => $category->id,
                    'is_active' => true,
                    'is_premium' => false,
                    'daily_limit_free' => 10,
                    'daily_limit_pro' => -1,
                    'usage_count' => 0,
                ])
            );
        }
    }
}
