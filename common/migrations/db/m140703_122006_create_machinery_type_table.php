<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%machinery_type}}`.
 */
class m140703_122006_create_machinery_type_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%machinery_type}}', [
            'id' => $this->primaryKey(),
            'mining_group_id' => $this->integer(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'prefix' => $this->string(5)->notNull(), 
            'last_number' => $this->integer()->defaultValue(0),
            'photo_base_url' => $this->string(),
            'photo_path' => $this->string(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Crea índices para mejorar el rendimiento de las consultas
        $this->createIndex(
            'idx-machinery_type-mining_group_id',
            '{{%machinery_type}}',
            'mining_group_id'
        );


        $this->addForeignKey(
            'fk-machinery_type-mining_group_id',
            '{{%machinery_type}}',
            'mining_group_id',
            '{{%mining_group}}',
            'id',
            'SET NULL'
        );

       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Elimina claves foráneas
        $this->dropForeignKey(
            'fk-machinery_type-mining_group_id',
            '{{%machinery_type}}'
        );

        $this->dropTable('{{%machinery_type}}');
    }
}