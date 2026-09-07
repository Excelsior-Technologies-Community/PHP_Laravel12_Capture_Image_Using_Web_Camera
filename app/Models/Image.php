<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'width',
        'height',
        'tags',
        'caption',
        'is_deleted',
        'deleted_at',
        'metadata',
    ];

    protected $casts = [
        'tags' => 'array',
        'metadata' => 'array',
        'is_deleted' => 'boolean',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute()
    {
        return asset('uploads/' . $this->filename);
    }

    public function getThumbnailUrlAttribute()
    {
        return asset('uploads/' . $this->filename);
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeTrashed($query)
    {
        return $query->where('is_deleted', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('filename', 'like', '%' . $term . '%')
              ->orWhere('original_filename', 'like', '%' . $term . '%')
              ->orWhere('caption', 'like', '%' . $term . '%')
              ->orWhereJsonContains('tags', $term);
        });
    }

    public function scopeFilterByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getExtensionAttribute()
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }
}
