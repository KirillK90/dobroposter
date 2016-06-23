<?php

use yii\db\Migration;

class m160623_082440_event_url_nullable extends Migration
{
    public function up()
    {
        $this->execute('alter table event alter column url DROP NOT NULL');
    }

    public function down()
    {
        $this->execute('alter table event alter column url SET NOT NULL');
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
