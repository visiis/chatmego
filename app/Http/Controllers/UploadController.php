<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PicBedService;

class UploadController extends Controller
{
    protected $picBedService;

    public function __construct(PicBedService $picBedService)
    {
        $this->picBedService = $picBedService;
        $this->middleware('auth');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'directory' => 'nullable|string|max:255',
        ]);

        $directory = $request->input('directory', 'uploads');
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $result = $this->picBedService->upload($file->getPathname(), $directory);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'url' => $result['url'],
                    'message' => '上传成功',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'success' => false,
            'message' => '请选择要上传的文件',
        ], 400);
    }

    public function uploadFromUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $result = $this->picBedService->uploadFromUrl($request->url);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'url' => $result['url'],
                'message' => '上传成功',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 400);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $result = $this->picBedService->delete($request->url);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => '删除成功',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 400);
    }

    public function listFiles(Request $request)
    {
        $directory = $request->input('directory', '');
        
        $result = $this->picBedService->listFiles($directory);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'files' => $result['files'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 400);
    }
}