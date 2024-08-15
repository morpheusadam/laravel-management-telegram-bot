<?php

namespace App\Queue;

use Illuminate\Queue\DatabaseQueue;
use Throwable;
use Illuminate\Support\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Queue\Jobs\DatabaseJob;
use Illuminate\Queue\Jobs\DatabaseJobRecord;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Auth;

class CustomDatabaseQueue extends DatabaseQueue
{
    /**
     * Create an array to insert for the given job.
     * @Note:- Overriding to add custom field : logged in user id
     *
     * @param  string|null  $queue
     * @param  string  $payload
     * @param  int  $availableAt
     * @param  int  $attempts
     * @return array
     */
    protected function buildDatabaseRecord($queue, $payload, $availableAt, $attempts = 0)
    {
        $user_id = Auth::user() ? Auth::user()->id : null;
        $process_id = getmypid();
        $payload_decoded = json_decode($payload,true);
        $payload_decoded['user_id'] =  $user_id;
        $payload_decoded['process_id'] =  $process_id;
        $payload = json_encode($payload_decoded);
        return [
            'queue' => $queue,
            'attempts' => $attempts,
            'reserved_at' => null,
            'available_at' => $availableAt,
            'created_at' => $this->currentTime(),
            'payload' => $payload,
            'user_id' => $user_id,
            'process_id' => $process_id
        ];
    }
}