<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class McqSubmission extends Model {
    protected $fillable = ['student_id','mcq_id','score','total','submitted_at'];
    public function student() { return $this->belongsTo(Student::class); }
    public function mcq()     { return $this->belongsTo(Mcq::class); }
    public function answers() { return $this->hasMany(McqAnswer::class, 'submission_id'); }
}
