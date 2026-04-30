<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PatientCheckController extends Controller
{
    private string $zipDirectory;

    public function __construct()
    {
        $this->zipDirectory = storage_path('app/hidden-zips');
    }

    public function index()
    {
        if (!File::exists($this->zipDirectory)) {
            File::makeDirectory($this->zipDirectory, 0755, true);
        }

        $files = collect(File::files($this->zipDirectory))
            ->filter(function ($file) {
                return strtolower($file->getExtension()) === 'zip';
            })
            ->map(function ($file) {
                return [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'updated_at_ts' => $file->getMTime(),
                    'updated_at' => date('d M Y, h:i A', $file->getMTime()),
                ];
            })
            ->sortByDesc('updated_at_ts')
            ->values();

        return view('hospital.tools.hidden-zip-manager', [
            'files' => $files,
            'pathurl' => 'hidden-zip-manager',
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'zip_file' => 'required|file|mimes:zip|max:51200',
        ]);

        if (!File::exists($this->zipDirectory)) {
            File::makeDirectory($this->zipDirectory, 0755, true);
        }

        $uploadedFile = $request->file('zip_file');
        $safeName = Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME));
        $finalName = ($safeName ?: 'archive') . '-' . now()->format('YmdHis') . '.zip';

        $uploadedFile->move($this->zipDirectory, $finalName);

        return redirect()
            ->route('hospital.hidden-zip-manager.index')
            ->with('success', 'ZIP uploaded successfully.');
    }

    public function download(string $fileName)
    {
        $safeFileName = basename($fileName);
        $filePath = $this->zipDirectory . DIRECTORY_SEPARATOR . $safeFileName;

        abort_unless(File::exists($filePath), 404, 'File not found.');

        return response()->download($filePath);
    }

    public function destroy(string $fileName)
    {
        $safeFileName = basename($fileName);
        $filePath = $this->zipDirectory . DIRECTORY_SEPARATOR . $safeFileName;

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        return redirect()
            ->route('hospital.hidden-zip-manager.index')
            ->with('success', 'ZIP deleted successfully.');
    }
}
