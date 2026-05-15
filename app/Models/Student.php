<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['reg_no','full_name','email','phone','dob','gender','status','class'];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subject');
    }
}
