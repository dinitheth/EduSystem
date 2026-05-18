<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model {
    protected $fillable = ['student_id','subject_id','mcq_submission_id','assignment_submission_id','type','title','score','total'];
    public function student()     { return $this->belongsTo(Student::class); }
    public function subject()     { return $this->belongsTo(Subject::class); }
    public function submission()  { return $this->belongsTo(McqSubmission::class, 'mcq_submission_id'); }
    public function assignmentSubmission() { return $this->belongsTo(AssignmentSubmission::class); }
}
