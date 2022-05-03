<?php

namespace App\Http\Controllers;


use App\Http\Services\TelegramService;
use App\Models\TelegramChat;
use App\Models\TelegramContact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramWebHookController extends Controller
{
    protected $telegramService;
    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    // web hook
    public function index(Request $request)
    {
        $update = Telegram::commandsHandler(true);
        $data = $request->all();
        $this->telegramService->webHookMessage($data);
    }
}
