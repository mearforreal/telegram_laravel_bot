<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            $data = [
                'description' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            $text = "Description:".$data['description']."\n".
                "file:".$data['file']."\n".
                "line:".$data['line']."\n";
                Telegram::setAsyncRequest(true)
                    ->sendMessage(['chat_id' => env('REPORT_TELEGRAM_ID'), 'text' => $text]);
            //$this->telegram->sendMessage(env('REPORT_TELEGRAM_ID'), (string)view('report', $data));
        });
    }
}
