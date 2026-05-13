<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\AutoEncoder;


class CaptchaController extends Controller
{
    // Generate CAPTCHA
    public function generateCaptcha()
    {
        // $manager = new \Intervention\Image\ImageManager(['driver' => 'gd']); // Ensure 'gd' or 'imagick' driver is specified
        $manager = new ImageManager(
            Driver::class
        );
        

        // Generate random string
        $captchaText = strtoupper(substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 6));
        Session::put('captcha', $captchaText);

        // Create CAPTCHA image
        $img = $manager->create(120, 20);
        $img->text($captchaText, 70, 10, function ($font) {
            $font->file(public_path('fonts/arial.ttf')); // Add a font file in the 'public/fonts' directory
            $font->size(20);
            $font->color('#333333');
            $font->align('center');
            $font->valign('middle');
        });

        return $img->encodeByExtension('png');
    }

    // Verify CAPTCHA
    public function verifyCaptcha(Request $request)
    {
        $request->validate([
            'captcha' => 'required|string'
        ]);

        if (Session::get('captcha') === $request->input('captcha')) {
            return response()->json(['success' => true, 'message' => 'CAPTCHA verified!']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid CAPTCHA.']);
    }
}
