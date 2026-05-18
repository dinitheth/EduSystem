<?php

namespace App\Http\Controllers;

use App\Models\PortalNotification;

class PortalNotificationController extends Controller
{
    public function open(PortalNotification $notification)
    {
        $type = session('student_id') ? 'student' : (session('teacher_id') ? 'teacher' : null);
        $id = session('student_id') ?: session('teacher_id');

        if (!$type || !$id || $notification->recipient_type !== $type || (int) $notification->recipient_id !== (int) $id) {
            abort(403);
        }

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return redirect()->to($notification->url ?: url()->previous());
    }
}
