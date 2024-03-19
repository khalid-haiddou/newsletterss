<?php

namespace App\Exports;

use App\Models\Mail;
use Maatwebsite\Excel\Concerns\FromCollection;

class MailExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Mail::all()->select('email');
    }


}
