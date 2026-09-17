<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage as ContactMessageMail;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $name = $request->string('name')->toString();
        $email = $request->string('email')->toString();
        $message = $request->string('message')->toString();

        $contactMessage = ContactMessage::create([
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'mail_sent' => false,
        ]);

        try {
            Mail::to(config('site.email'))->send(new ContactMessageMail($name, $email, $message));
            $contactMessage->update(['mail_sent' => true]);
        } catch (\Throwable $e) {
            Log::warning('Contact mail kon niet verstuurd worden, bericht wel opgeslagen in DB.', [
                'contact_message_id' => $contactMessage->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
