<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tempelan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'unique'     => true
            ],
            'teks' => [
                'type' => 'MEDIUMTEXT',
                'null' => true,
            ],
            'diakses' => [
                'type' => 'INT',
                'constraint' => 9,
                'default' => 0
            ],
            'kadaluarsa' => [
                'type' => 'DATETIME'
            ],
			'created_at'	=> [
				'type'		=> 'DATETIME'
			],
			'updated_at'	=> [
				'type'		=> 'DATETIME'
			],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tempelan');
    }

    public function down()
    {
        $this->forge->dropTable('tempelan');
	}
}
