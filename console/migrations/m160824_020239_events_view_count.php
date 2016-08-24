<?php

use yii\db\Migration;

class m160824_020239_events_view_count extends Migration
{
    public function up()
    {
        $this->addColumn('event', 'views_count', $this->integer()->notNull()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('event', 'views_count');
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
