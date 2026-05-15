<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class McqAnswer extends Model {
    protected $fillable = ['submission_id','question_id','option_id','is_correct'];
    public function submission() { return $this->belongsTo(McqSubmission::class, 'submission_id'); }
    public function question()   { return $this->belongsTo(McqQuestion::class, 'question_id'); }
    public function option()     { return $this->belongsTo(McqOption::class, 'option_id'); }
}
