<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%inspection_point}}`.
 */
class m200101_000005_create_inspection_point_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%inspection_point}}', [
            'id' => $this->primaryKey(),
            'machinery_id' => $this->integer(),
            'component_id' => $this->integer(),
            'name' => $this->string(255)->notNull(),
            'is_extra' => $this->boolean()->notNull()->defaultValue(false),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        // Crear índices para las claves foráneas
        $this->createIndex(
            'idx-inspection_point-machinery_id',
            '{{%inspection_point}}',
            'machinery_id'
        );

        $this->createIndex(
            'idx-inspection_point-component_id',
            '{{%inspection_point}}',
            'component_id'
        );

        // Añadir claves foráneas con SET NULL para eliminación
        $this->addForeignKey(
            'fk-inspection_point-machinery_id',
            '{{%inspection_point}}',
            'machinery_id',
            '{{%machinery}}',
            'id',
            'SET NULL',
        );

        $this->addForeignKey(
            'fk-inspection_point-component_id',
            '{{%inspection_point}}',
            'component_id',
            '{{%component}}',
            'id',
            'SET NULL',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Eliminar primero las claves foráneas
        $this->dropForeignKey('fk-inspection_point-machinery_id', '{{%inspection_point}}');
        $this->dropForeignKey('fk-inspection_point-component_id', '{{%inspection_point}}');
        
        // Eliminar los índices
        $this->dropIndex('idx-inspection_point-machinery_id', '{{%inspection_point}}');
        $this->dropIndex('idx-inspection_point-component_id', '{{%inspection_point}}');
        
        // Finalmente eliminar la tabla
        $this->dropTable('{{%inspection_point}}');
    }
}