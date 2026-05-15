<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentLogin extends Model {
    protected $fillable = ['student_id','email','password'];
    protected $hidden   = ['password'];
    public function student() { return $this->belongsTo(Student::class); }
}
