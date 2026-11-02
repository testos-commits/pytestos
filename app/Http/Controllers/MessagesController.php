<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MessageService;
use App\Http\Requests\AnswerMessageRequest;
use App\Models\ProfileMessage;
use App\Services\AdminMessageService;
use App\Models\Message;
use Illuminate\Support\Facades\Log;


class MessagesController extends Controller
{
    private $messageService;

    public function __construct()
    {
        $this->messageService = new MessageService();
    }

    public function index()
    {   
        Log::channel('debug')->info('get messages');
        return response()->json([
            'messages' =>  $this->messageService->getUserApplicationsMessages(),
            'admin_messages' => AdminMessageService::get()
        ]);
    }

    public function show($auditionId)
    {
        return response()->json([
           'chat' => $this->messageService->getAuditionChat($auditionId),
           'is_admin_message' => false
        ]);
    }

    public function answer(Message $auditionMessage, AnswerMessageRequest $answerRequest)
    {
        $this->messageService->answer($auditionMessage, $answerRequest->get('answer_id'));
        return response()->json(['message' => 'Your answer was received']);
    }

    public function markRead($auditionId)
    {
        $this->messageService->markRead($auditionId);
        return response()->json(['message' => 'Message marked read']);
    }


    public function updateLastReadAdminMessage()
    {
        AdminMessageService::updateReadStatus();
        return response()->json(['message' => 'Last read admin message updated']);
    }
}
