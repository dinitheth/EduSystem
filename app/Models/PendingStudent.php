<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingStudent extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'dob',
        'gender',
        'requested_subject_ids',
        'status',
        'notes',
    ];

    protected $casts = [
        'dob' => 'date',
        'requested_subject_ids' => 'array',
    ];

    public function requestedSubjects()
    {
        return Subject::whereIn('id', $this->requested_subject_ids ?? [])->orderBy('subject_name')->get();
    }
}
