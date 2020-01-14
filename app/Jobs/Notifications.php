<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\Notifications as EventNotifications;
use App\Events\Message;
use App\Events\Validation;
use Pusher\Pusher;
class Notifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $type;
    public $data;
    public $pusher;
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
        if(env('PUSHER_TYPE') != 'self'){
            $options = array(
                'cluster' => env('OTHER_PUSHER_CLUSTER'),
                'useTLS' => true
            );
            $this->pusher = new Pusher(
                env('OTHER_PUSHER_APP_KEY'),
                env('OTHER_PUSHER_APP_SECRET'),
                env('OTHER_PUSHER_APP_ID'),
                $options
            );
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->type) {
            case 'notifications':
                if(env('PUSHER_TYPE') == 'self'){
                    broadcast(new EventNotifications($this->data));
                }else{
                    $this->pusher->trigger('payhiram', 'Notifications', $this->data);
                }
                break;
            case 'message':
                if(env('PUSHER_TYPE') == 'self'){
                    broadcast(new Message($this->data));
                }else{
                    $this->pusher->trigger('payhiram', 'Message', $this->data);
                }
                break;
            case 'validation':
                if(env('PUSHER_TYPE') == 'self'){
                    broadcast(new Validation($this->data));
                }else{
                    $this->pusher->trigger('payhiram', 'Validation', $this->data);
                }
                break;
            default:
                # code...
                break;
        }
    }
}
