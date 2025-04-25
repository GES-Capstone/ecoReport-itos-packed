<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%functional_status}}`.
 */
class m250425_211452_create_functional_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%functional_status}}', [
            'id' => $this->primaryKey(),
            'status' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%functional_status}}');
    }
}
