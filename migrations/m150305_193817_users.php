<?php

use yii\db\Schema;
use yii\db\Migration;

class m150305_193817_users extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'first_name' => Schema::TYPE_STRING . '(100) NOT NULL',
            'last_name' => Schema::TYPE_STRING . '(100) NOT NULL',
            'email' => Schema::TYPE_STRING . ' NOT NULL',
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'password' => Schema::TYPE_STRING . ' NOT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NULL',
            'access_token' => Schema::TYPE_STRING . '(32) NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->delete('{{%user}}');
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
