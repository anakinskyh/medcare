<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use Illuminate\Support\Facades\Mail;

class demo extends Controller
{

    //
    public function demo(){

        $title = 'Hi';
        $content = 'Tell Me what your name is?';

        Mail::send('emails.send', ['title' => $title, 'content' => $content], function ($message)
        {

            $message->to('pointguard.1rose@gmail.com');

        });

        return response()->json(['message' => 'Request completed']);
    }
}
