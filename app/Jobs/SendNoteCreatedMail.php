<?php

namespace App\Jobs;

use App\Mail\NoteCreated;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNoteCreatedMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Get all users and send them mail
     *
     * @return void
     */
    public function handle()
    {
        $chunks = User::query()
            ->get()
            ->chunk(25);

        foreach ($chunks as $users) {
            Mail::to($users)
                ->send(new NoteCreated());
        }
    }
}
