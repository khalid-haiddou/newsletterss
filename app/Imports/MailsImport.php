<?php

namespace App\Imports;

use App\Models\Mail;
use Maatwebsite\Excel\Concerns\ToModel;

class MailsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Mail([
            //
            'email' => $row[0],
        ]);
    }
}
