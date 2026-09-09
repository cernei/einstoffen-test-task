<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $invoices = $db->table('invoices')->orderBy('id', 'desc')->get()->getResultArray();
        $eventLog = $db->table('event_log')->orderBy('id', 'desc')->get()->getResultArray();

        return view('dashboard', ['invoices' => $invoices, 'eventLog' => $eventLog]);
    }
}