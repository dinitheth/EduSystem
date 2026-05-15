<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model {
    protected $fillable = [
        'assignment_id','student_id','notes','file_path',
        'status','marks','max_marks','feedback',
        'submitted_at','graded_by','graded_at'
    ];
    protected $casts = ['submitted_at'=>'datetime','graded_at'=>'datetime'];

    public function assignment() { return $this->belongsTo(Assignment::class); }
    public function student()    { return $this->belongsTo(Student::class); }
    public function gradedBy()   { return $this->belongsTo(Teacher::class, 'graded_by'); }

    public function getPercentAttribute(): int {
        if (!$this->marks || !$this->max_marks) return 0;
        return (int) round(($this->marks / $this->max_marks) * 100);
    }
    public function getGradeAttribute(): string {
        $p = $this->percent;
        return $p >= 80 ? 'A' : ($p >= 60 ? 'B' : ($p >= 40 ? 'C' : ($this->marks !== null ? 'F' : '—')));
    }
}
