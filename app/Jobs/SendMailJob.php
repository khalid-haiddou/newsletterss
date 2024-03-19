<?php

namespace App\Jobs;

use App\Events\SendMailsEvent;
use App\Models\Mail as MailModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail as Mailfacad;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $details;

    /**
     * Create a new job instance.
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Retrieve all email addresses
            $emails = MailModel::pluck('email');

            // Prepare email details
            $input['title'] = $this->details['title'];
            $input['content'] = $this->details['content'];

            // Loop through each email address and send email
            foreach ($emails as $email) {
                // Add email to the input array
                $input['email'] = $email;

                // Send email
                Mailfacad::mailer('smtp')->send('mail.sendEmail', ['input' => $input], function ($message) use ($email, $input) {
                    $message->to($email)->subject($input['title']);
                });
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occurred during email sending
            dd($e);
        }
    }
}
