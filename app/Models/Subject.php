<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['subject_code','subject_name','description','category','is_active'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_subject');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject');
    }
}
