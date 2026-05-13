<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Benificiary extends Model
{
    //
    
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        $filename  = $this->image;

        if (
            $filename &&
            $this->hasAllowedImageExtension() &&
            Storage::disk('public')->exists("benificiary/{$filename}")
        ) {
            return url("public/storage/benificiary/{$filename}");
        }

        // Fallback: Generate avatar using name
        $name = urlencode($this->name ?? 'User');
        return "https://ui-avatars.com/api/?name={$name}&background=random";

    }

    public function hasAllowedImageExtension()
    {
        $url = $this->image;

        return $url && Str::endsWith(Str::lower($url), ['.jpg', '.jpeg', '.png', '.gif']);
    }
}
