<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationsService;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-notification {--user=?} {--stack=?} {--screen=?} {--params=?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the push notification system.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $pn = new NotificationsService([
        //     'ar' => [
        //         'title' => 'تنبيه',
        //         'body' => 'تم إرسال تنبيه جديد'
        //     ],
        //     'en' => [
        //         'title' => 'Notification',
        //         'body' => 'New notification sent'
        //     ]
        //     ], []);

        // if ($this->option('user')) {
        //     $data = [];
        //     if ($this->option('stack') !== '?') {
        //         $data['stack'] = $this->option('stack');
        //     }
        //     if ($this->option('screen')  !== '?') {
        //         $data['screen'] = $this->option('screen');
        //     }
        //     if ($this->option('params')  !== '?') {
        //         $data['params'] = $this->option('params');
        //     }

        //     $pn->testWithOne($this->option('user'), $data);
        // } else {
        //     $pn->testWithOne();
        // }
    }
}
