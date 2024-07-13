<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\WateringReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWateringReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;
    protected string $plantName;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $plantName)
    {
        $this->user = $user;
        $this->plantName = $plantName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->notify(new WateringReminder($this->plantName));
    }
}
