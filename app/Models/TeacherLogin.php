<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeacherLogin extends Model {
    protected $fillable = ['teacher_id','email','password'];
    protected $hidden   = ['password'];
    public function teacher() { return $this->belongsTo(Teacher::class); }
}
