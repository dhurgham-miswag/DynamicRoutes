<?php

namespace App\Http\Controllers\Api\Youtube;

use App\Services\YouTubeDownloaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class YouTubeDownloaderController extends Controller
{
    protected YouTubeDownloaderService $downloader;

    public function __construct(YouTubeDownloaderService $downloader)
    {
        $this->downloader = $downloader;
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['query' => 'required|string']);
        $result = $this->downloader->search($request->input('query'));
        return response()->json($result);
    }

    public function convertToMp3(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url']);
        $result = $this->downloader->convertToMp3($request->input('url'));
        return response()->json($result);
    }

    public function convertToMp4(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url']);
        $result = $this->downloader->convertToMp4($request->input('url'));
        return response()->json($result);
    }

    public function getInfo(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url']);
        $result = $this->downloader->getInfo($request->input('url'));
        return response()->json($result);
    }
}
