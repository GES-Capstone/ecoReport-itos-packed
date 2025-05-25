<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%component}}`.
 */
class m200101_000003_create_component_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%component}}', [
            'id' => $this->primaryKey(),
            
            // Relación con machinery
            'machinery_id' => $this->integer()->notNull(),
            
            'name' => $this->string()->notNull(),
            'tag' => $this->string(),
            'model' => $this->string(),
            
            // Vida útil
            'useful_life_years' => $this->double(),
            'useful_life_hours' => $this->integer(),
            
            'supplier' => $this->string(),
            'cost' => $this->decimal(10, 2),
            
            // Fecha de inicio de operaciones
            'started_operations' => $this->date(),
            
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Crear índices
        $this->createIndex(
            'idx-component-machinery_id',
            '{{%component}}',
            'machinery_id'
        );

        $this->createIndex(
            'idx-component-location_id',
            '{{%component}}',
            'location_id'
        );

        $this->createIndex(
            'idx-component-tag',
            '{{%component}}',
            'tag'
        );

        // Crear claves foráneas
        $this->addForeignKey(
            'fk-component-machinery_id',
            '{{%component}}',
            'machinery_id',
            '{{%machinery}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-component-location_id',
            '{{%component}}',
            'location_id',
            '{{%location}}',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        // Eliminar claves foráneas
        $this->dropForeignKey(
            'fk-component-location_id',
            '{{%component}}'
        );

        $this->dropForeignKey(
            'fk-component-machinery_id',
            '{{%component}}'
        );

        // Eliminar índices
        $this->dropIndex(
            'idx-component-tag',
            '{{%component}}'
        );

        $this->dropIndex(
            'idx-component-location_id',
            '{{%component}}'
        );

        $this->dropIndex(
            'idx-component-machinery_id',
            '{{%component}}'
        );

        $this->dropTable('{{%component}}');
    }
}