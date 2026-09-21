<?php

namespace App\Models;

use CodeIgniter\Model;

class Invoice extends Model
{
    protected $table      = 'invoices';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'invoice_id',
        'customer_id',
        'customer_name',
        'customer_email',
        'amount',
        'currency',
        'status',
        'due_date',
        'comment',
        'shipping_status',
        'shipping_info',
    ];

    protected $useTimestamps = true;
}
