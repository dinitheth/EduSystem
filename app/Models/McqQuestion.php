<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class McqQuestion extends Model {
    protected $fillable = ['mcq_id','question','order'];
    public function mcq()     { return $this->belongsTo(Mcq::class); }
    public function options() { return $this->hasMany(McqOption::class, 'question_id'); }
}
