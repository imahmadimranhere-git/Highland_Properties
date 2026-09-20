<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'disk', 'path', 'webp_path', 'thumb_path', 'original_name',
        'mime', 'size', 'width', 'height', 'alt_text', 'folder', 'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /** Full-size WebP when it exists, original otherwise. Use on detail pages. */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->webp_path ?: $this->path);
    }

    /** Small thumbnail. Use on every listing/grid page. */
    public function getThumbUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->thumb_path ?: ($this->webp_path ?: $this->path));
    }

    public function getOriginalUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime, 'image/');
    }
}
