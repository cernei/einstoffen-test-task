<?php

namespace App\Models;

use CodeIgniter\Model;

class EventLog extends Model
{
    protected $table      = 'event_log';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'entity_id',
        'payload',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // or 'date', or 'int' (timestamp)
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Setting this to an empty string disables updated_at
}
