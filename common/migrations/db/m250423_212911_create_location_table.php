<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%location}}`.
 */
class m250423_212911_create_location_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%location}}', [
            'id' => $this->primaryKey(),
            'latitude' => $this->decimal(10, 7)->notNull()->comment('Latitude coordinate'),
            'longitude' => $this->decimal(11, 7)->notNull()->comment('Longitude coordinate')
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%location}}');
    }
}
