<?php

namespace App\Classes;

use Illuminate\Support\Facades\App;

abstract class MessageHandler
{
    protected $messages = [];

    public function __construct()
    {
        if (auth('sanctum')->check()) {
            App::setLocale(auth('sanctum')->user()->app_language);
        }
    }
    public function getMessages()
    {
        // if (count($this->messages) == 1) {
        //     return $this->messages[0];
        // }

        return $this->messages;
    }

    public function setMessages($messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $key => $message) {
                $this->messages[$message] = __('messages.' . $message);
            }
        } else {
            $this->messages[$messages] = __('messages.' . $messages);
        }
    }
}
