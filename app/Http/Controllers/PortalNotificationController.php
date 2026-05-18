<?php

namespace App\Http\Controllers;

use App\Models\PortalNotification;
use Illuminate\Http\Request;

class PortalNotificationController extends Controller
{
    public function open(Request $request, PortalNotification $notification)
    {
        $type = null;
        $id = null;

        if ($request->is('student/*') && session('student_id')) {
            $type = 'student';
            $id = session('student_id');
        }

        if ($request->is('teacher/*') && session('teacher_id')) {
            $type = 'teacher';
            $id = session('teacher_id');
        }

        if (!$type || !$id || $notification->recipient_type !== $type || (int) $notification->recipient_id !== (int) $id) {
            abort(403);
        }

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return redirect()->to($notification->url ?: url()->previous());
    }
}
