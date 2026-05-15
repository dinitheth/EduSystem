<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['employee_no','full_name','email','phone','specialization','department','employment_status','class'];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject');
    }
}
