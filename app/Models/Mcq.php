<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Mcq extends Model {
    protected $fillable = ['teacher_id','subject_id','class','title','time_limit','expires_at'];
    protected $casts    = ['expires_at' => 'datetime'];

    public function teacher()    { return $this->belongsTo(Teacher::class); }
    public function subject()    { return $this->belongsTo(Subject::class); }
    public function questions()  { return $this->hasMany(McqQuestion::class)->orderBy('order'); }
    public function submissions(){ return $this->hasMany(McqSubmission::class); }
}
