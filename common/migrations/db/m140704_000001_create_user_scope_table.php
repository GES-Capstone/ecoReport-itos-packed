<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_scope}}`.
 */
class m140704_000001_create_user_scope_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_scope}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'company_id' => $this->integer(),

        ]);

        // Añadir índices para mejorar el rendimiento
        $this->createIndex(
            'idx-user_scope-user_id',
            '{{%user_scope}}',
            'user_id'
        );

        $this->createIndex(
            'idx-user_scope-company_id',
            '{{%user_scope}}',
            'company_id'
        );

        // Crear índice único para evitar duplicados
        $this->createIndex(
            'idx-user_scope-user_company-unique',
            '{{%user_scope}}',
            ['user_id', 'company_id'],
            true
        );

        // Añadir claves foráneas
        $this->addForeignKey(
            'fk-user_scope-user_id',
            '{{%user_scope}}',
            'user_id',
            '{{%user}}',
            'id',
            'SET NULL',
            
        );

        $this->addForeignKey(
            'fk-user_scope-company_id',
            '{{%user_scope}}',
            'company_id',
            '{{%company}}',
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
        $this->dropForeignKey('fk-user_scope-user_id', '{{%user_scope}}');
        $this->dropForeignKey('fk-user_scope-company_id', '{{%user_scope}}');
        
        // Eliminar los índices
        $this->dropIndex('idx-user_scope-user_id', '{{%user_scope}}');
        $this->dropIndex('idx-user_scope-company_id', '{{%user_scope}}');
        $this->dropIndex('idx-user_scope-user_company-unique', '{{%user_scope}}');
        
        // Finalmente eliminar la tabla
        $this->dropTable('{{%user_scope}}');
    }
}