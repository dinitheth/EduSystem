<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model {
    protected $fillable = ['teacher_id','subject_id','class','title','description','file_path','due_date'];
    public function teacher()     { return $this->belongsTo(Teacher::class); }
    public function subject()     { return $this->belongsTo(Subject::class); }
    public function submissions() { return $this->hasMany(AssignmentSubmission::class); }
}
