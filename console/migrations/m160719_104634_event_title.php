<?php

use yii\db\Migration;

class m160719_104634_event_title extends Migration
{
    public function up()
    {
        $this->renameColumn('event', 'name', 'title');
    }

    public function down()
    {
        $this->renameColumn('event', 'title', 'name');
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
