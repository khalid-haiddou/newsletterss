<?php

namespace App\Http\Controllers;
use Symfony\Component\Mailer\Exception\UnexpectedResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Jobs\SendMailJob;
use App\Models\Newsletter;

class SendMailController extends Controller
{

    public function send_emails(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'title' => 'required',
        //     'content' => 'required',
        // ]);


        $newsletters = Newsletter::all(); // Adjust this query as per your requirements

        // Loop through each newsletter to send emails
        foreach ($newsletters as $newsletter) {
            // Prepare email details
            $details = [
                'title' => $newsletter->title,
                'content' => $newsletter->content,

                // Add more data as needed for the email template
            ];

            // Dispatch job to send email
            $job = new SendMailJob($details);
            dispatch($job);


            return redirect('newsletter')->with('msg', 'Add With Successfly');

        }

        return back()->with('status', 'Mails sent successefly');
    }
}
