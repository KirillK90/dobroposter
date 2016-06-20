<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'auth_key' => $this->string()->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string(),
            'email' => $this->string()->notNull(),
            'gender' => $this->string(),
            'birthday' => $this->dateTime(),
            'role' => $this->string()->notNull(),
            'last_visit' => $this->dateTime(),
            'photo' => $this->string(),
            'oauth' => $this->boolean(),
            'oauth_service' => $this->string(),
            'oauth_id' => $this->string(),
            'photo_url' => $this->string(),
            'status' => $this->string()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),
        ], $tableOptions);

        $this->createTable('event', [
            'id' => $this->primaryKey(),
            'created_at' => $this->dateTime()->notNull(),
            'name' => $this->string()->notNull(),
            'slug' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'place_id' => $this->integer(),
            'announcement' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'start_time' => $this->dateTime()->notNull(),
            'end_time' => $this->dateTime()->notNull(),
            'status' => $this->string()->notNull(),
            'image_src' => $this->string(),
            'url' => $this->string()->notNull(),
            'free' => $this->boolean()->notNull()->defaultValue(false),
            'price_min' => $this->integer(),
            'in_top' => $this->boolean()->notNull()->defaultValue(false),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'published_at' => $this->dateTime(),
        ], $tableOptions);

        $this->createTable('category', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'top_menu' => $this->integer(),
            'created_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createTable('event_category', [
            'event_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('event_category_pkey', 'event_category', ['event_id', 'category_id']);
        $this->addForeignKey('event_category_event_id_fkey', 'event_category', 'event_id', 'event', 'id', 'cascade');
        $this->addForeignKey('event_category_category_id_fkey', 'event_category', 'category_id', 'category', 'id');
        $this->createTable('place', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('user');
        $this->dropTable('event_category');
        $this->dropTable('event');
        $this->dropTable('category');
        $this->dropTable('place');
    }
}
