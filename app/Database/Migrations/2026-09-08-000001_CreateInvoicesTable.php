<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'invoice_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'customer_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'customer_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'due_date' => [
                'type' => 'DATE',
            ],
            'comment' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'shipping_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'shipping_info' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('invoice_id');
        $this->forge->createTable('invoices');
    }

    public function down()
    {
        $this->forge->dropTable('invoices');
    }
}