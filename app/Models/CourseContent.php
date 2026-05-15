<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CourseContent extends Model {
    protected $fillable = [
        'subject_id','teacher_id','type','title','description',
        'content_text','file_path','url','sort_order'
    ];

    public function subject() { return $this->belongsTo(Subject::class); }
    public function teacher() { return $this->belongsTo(Teacher::class); }

    /** Extract YouTube video ID from any YouTube URL */
    public function getYoutubeIdAttribute(): ?string {
        if ($this->type !== 'youtube' || !$this->url) return null;
        preg_match(
            '/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([^&\n\?#]{11})/',
            $this->url, $m
        );
        return $m[1] ?? null;
    }

    /** Embed URL for YouTube */
    public function getYoutubeEmbedAttribute(): ?string {
        $id = $this->youtube_id;
        return $id ? "https://www.youtube.com/embed/{$id}?rel=0" : null;
    }
}
