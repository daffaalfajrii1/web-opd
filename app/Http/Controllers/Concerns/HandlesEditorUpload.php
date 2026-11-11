<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait HandlesEditorUpload
{
    /** @var list<string> */
    private const ALLOWED_FOLDERS = [
        'pages/editor',
        'beritas/editor',
        'agendas/editor',
        'pengumumans/editor',
        'galeris/editor',
    ];

    public function uploadEditorImage(Request $request): JsonResponse
    {
        $file = $request->file('file') ?? $request->file('upload');

        if (! $file) {
            return response()->json([
                'message' => 'File gambar wajib diunggah.',
            ], 422);
        }

        Validator::make(
            ['file' => $file],
            [
                'file' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,webp,gif',
                    'max:5120',
                    'dimensions:max_width=5000,max_height=5000',
                ],
            ],
            [
                'file.image' => 'File harus berupa gambar (JPG, PNG, GIF, atau WebP).',
                'file.mimes' => 'Format gambar tidak diizinkan.',
                'file.dimensions' => 'Resolusi gambar terlalu besar (maks. 5000×5000).',
            ],
        )->validate();

        $folder = (string) $request->input('folder', 'pages/editor');
        if (! in_array($folder, self::ALLOWED_FOLDERS, true)) {
            $folder = 'pages/editor';
        }

        $path = $file->store($folder, 'public');
        $url = asset('storage/'.$path);

        return response()->json([
            'url' => $url,
            'location' => $url,
        ]);
    }
}
