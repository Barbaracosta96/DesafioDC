<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Queued version of the built-in ResetPassword notification.
 * Dispatched to the queue so the HTTP request returns immediately,
 * and the e-mail is sent asynchronously by the queue worker.
 */
class QueuedResetPassword extends ResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * The queue connection to use.
     */
    public ?string $connection = null;

    /**
     * The queue name to use.
     */
    public ?string $queue = 'emails';

    /**
     * Number of seconds to delay the job.
     */
    public int $delay = 0;
}
