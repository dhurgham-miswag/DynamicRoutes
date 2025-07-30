<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for downloading and converting YouTube videos
 * Provides functionality to search, convert to MP3/MP4, and get video information
 */
class YouTubeDownloaderService
{
    private $headers = [
        'accept' => '*/*',
        'accept-language' => 'en-US,en;q=0.9',
        'dnt' => '1',
        'sec-ch-ua' => '"Chromium";v="135", "Not-A.Brand";v="8"',
        'sec-ch-ua-mobile' => '?0',
        'sec-ch-ua-platform' => '"macOS"',
        'sec-fetch-dest' => 'empty',
        'sec-fetch-mode' => 'cors',
        'sec-fetch-site' => 'cross-site',
        'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
    ];

    /**
     * Extract video ID from YouTube URL
     */
    public function getVideoId(string $url): ?string
    {
        $parsed = parse_url($url);

        if (!$parsed) {
            return null;
        }

        if (isset($parsed['host']) && str_contains($parsed['host'], 'youtu.be')) {
            return ltrim($parsed['path'] ?? '', '/');
        }

        if (isset($parsed['path']) && str_contains($parsed['path'], '/shorts/')) {
            $pathParts = explode('/', $parsed['path']);
            return end($pathParts);
        }

        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $query);
            if (isset($query['v'])) {
                return $query['v'];
            }
        }

        return null;
    }

    /**
     * Search for YouTube videos
     */
    public function search(string $query): array
    {
        try {
            $headers = array_merge($this->headers, [
                'origin' => 'https://yt1s.click',
                'referer' => 'https://yt1s.click/',
            ]);

            $response = Http::withHeaders($headers)
                ->get("https://test.flvto.online/search/?q={$query}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->body(),
                    'status' => $response->status()
                ];
            }

            return [
                'success' => false,
                'error' => 'Search request failed',
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('YouTube search error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Convert YouTube video to MP3
     */
    public function convertToMp3(string $url): array
    {
        $videoId = $this->getVideoId($url);

        if (!$videoId) {
            return [
                'success' => false,
                'error' => 'Invalid YouTube URL or could not extract video ID'
            ];
        }

        try {
            $headers = array_merge($this->headers, [
                'content-type' => 'application/json',
                'origin' => 'https://ht.flvto.online',
                'referer' => "https://ht.flvto.online/widget?url=https://www.youtube.com/watch?v={$videoId}&el=289",
                'sec-fetch-site' => 'same-origin',
                'sec-fetch-storage-access' => 'active',
            ]);

            $response = Http::withHeaders($headers)
                ->post('https://ht.flvto.online/converter', [
                    'id' => $videoId,
                    'fileType' => 'MP3',
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'video_id' => $videoId,
                    'format' => 'MP3'
                ];
            }

            return [
                'success' => false,
                'error' => 'MP3 conversion failed',
                'status' => $response->status(),
                'video_id' => $videoId
            ];
        } catch (\Exception $e) {
            Log::error('YouTube MP3 conversion error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'video_id' => $videoId
            ];
        }
    }

    public function convertToMp4(string $url): array
    {
        $videoId = $this->getVideoId($url);

        if (!$videoId) {
            return [
                'success' => false,
                'error' => 'Invalid YouTube URL or could not extract video ID'
            ];
        }

        try {
            $headers = array_merge($this->headers, [
                'content-type' => 'application/json',
                'origin' => 'https://ht.flvto.online',
                'referer' => "https://ht.flvto.online/widget?url={$videoId}",
                'sec-fetch-site' => 'same-origin',
                'sec-fetch-storage-access' => 'active',
            ]);

            $response = Http::withHeaders($headers)
                ->post('https://ht.flvto.online/converter', [
                    'id' => $videoId,
                    'fileType' => 'mp4',
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'video_id' => $videoId,
                    'format' => 'MP4'
                ];
            }

            return [
                'success' => false,
                'error' => 'MP4 conversion failed',
                'status' => $response->status(),
                'video_id' => $videoId
            ];
        } catch (\Exception $e) {
            Log::error('YouTube MP4 conversion error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'video_id' => $videoId
            ];
        }
    }

    public function getInfo(string $url): array
    {
        $videoId = $this->getVideoId($url);

        if (!$videoId) {
            return [
                'success' => false,
                'error' => 'Invalid YouTube URL or could not extract video ID'
            ];
        }

        return [
            'success' => true,
            'video_id' => $videoId,
            'url' => $url,
            'formats_available' => ['MP3', 'MP4']
        ];
    }
} 