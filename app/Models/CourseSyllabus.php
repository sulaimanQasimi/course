<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CourseSyllabus extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'version',
        'is_active',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'version' => 'integer',
        'uploaded_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('version', 'desc');
    }

    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIsFileAttribute()
    {
        return !empty($this->file_path);
    }

    public function getIsContentAttribute()
    {
        return !empty($this->content);
    }
}
