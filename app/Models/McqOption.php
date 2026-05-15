<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class McqOption extends Model {
    protected $table    = 'mcq_options';
    protected $fillable = ['question_id','option_text','is_correct'];
    public function question() { return $this->belongsTo(McqQuestion::class, 'question_id'); }
}
