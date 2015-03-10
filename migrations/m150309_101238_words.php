<?php

use yii\db\Schema;
use yii\db\Migration;

class m150309_101238_words extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%words}}', [
            'id' => Schema::TYPE_PK,
            'word' => Schema::TYPE_STRING . '(60) NOT NULL',
            'userID' => Schema::TYPE_INTEGER . '(10) NOT NULL',
            'teach_priority' => Schema::TYPE_INTEGER . '(10) NOT NULL',
            'in_russian' => Schema::TYPE_STRING . '(50) NOT NULL',
            'in_armenian' => Schema::TYPE_STRING . '(50) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->delete('{{%words}}');
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
