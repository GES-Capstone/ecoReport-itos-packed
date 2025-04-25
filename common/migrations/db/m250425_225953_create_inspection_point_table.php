<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%inspection_point}}`.
 */
class m250425_225953_create_inspection_point_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%inspection_point}}', [
            'id' => $this->primaryKey(),
            'machine_id' => $this->integer(),
            'component_id' => $this->integer(),
            'name' => $this->string()->notNull(),
            'is_extra' => $this->boolean()->notNull()->defaultValue(false),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%inspection_point}}');
    }
}
