<?php

namespace App\Http\Controllers;

use App\Exports\MailExport;
use App\Models\Mail as Mails;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MailsImport;

class MailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $mail;
    public function __construct(){
        $this->mail = new Mails();
    }
    public function index()
    {
        //
        $getEmails = $this->mail->orderby('id' ,  "DESC")->paginate(10);
        $count = count($getEmails);
        return view('emails' , compact('getEmails' , 'count'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function AddEmail(Request $request)

    {
        $request->validate([
            'email' =>'required|unique:mails|max:255',
        ]);
        $this->mail->email = $request->email;
        $this->mail->save();
        return redirect('emails')->with('msg' , 'Add With Successfly');
        
    }


    public function deleteEmail($id)
    {
        $email = $this->mail->find($id);
        if ($email) {
            $email->delete();
        }

        return redirect('emails')->with('delmsg' , 'Deleted With Successfly');
    }



    public function EditEmail(Request $request){
        $request->validate([
            'email' =>'required|unique:mails|max:255',
        ]);
        $email = $this->mail->find($request->id);
        if ($email) {
            $email->email = $request->email;
            $email->save();
        }

        return redirect('emails')->with('msg' , 'Updated With Successfly');
    }
   

    public function deleteAll(){
        $this->mail->query()->delete();
        return redirect('emails')->with('delmsg', 'All Emails Have Been Deleted Successfully');
    }
    
    

    public function import(Request $request)
    {


        $file = $request->file('file');

         Excel::import(new MailsImport, $file);

        return redirect('emails')->with('msg', 'Data imported successfully!');
    }

    public function export()
    {
        return Excel::download(new MailExport, 'Mails.xlsx');
    }
}
