<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%machinery}}`.
 */
class m250425_211707_create_machinery_table extends Migration
{
    public function safeUp()
    {
        // Crear la enumeración para inspection_type
        $this->execute("CREATE TYPE inspection_type AS ENUM ('PER COMPONENT', 'COMPLETE')");
        
        $this->createTable('{{%machinery}}', [
            'id' => $this->primaryKey(),
            'mining_group_id' => $this->integer(),
            'company_id' => $this->integer(),
            'fleet_id' => $this->integer(),
            'area_id' => $this->integer(),
            'functional_status_id' => $this->integer(),
            'location_id' => $this->integer(),
            'tag' => $this->string()->notNull(),
            'brand' => $this->string(),
            'model' => $this->string(),
            'start_operation' => $this->date(),
            'lifespan_years' => $this->integer(),
            'supplier' => $this->string(),
            'cost' => $this->decimal(19, 4),
            'sap_code' => $this->string(),
            'description' => $this->text(),
            'photo_base_url' => $this->string(),
            'photo_path' => $this->string(),
            'inspection_type' => "inspection_type", // Usando el ENUM creado
        ]);

        // Añadir claves foráneas
        $this->addForeignKey(
            'fk-machinery-mining_group_id',
            '{{%machinery}}',
            'mining_group_id',
            '{{%mining_group}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-machinery-company_id',
            '{{%machinery}}',
            'company_id',
            '{{%company}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-machinery-fleet_id',
            '{{%machinery}}',
            'fleet_id',
            '{{%fleet}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-machinery-area_id',
            '{{%machinery}}',
            'area_id',
            '{{%area}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-machinery-functional_status_id',
            '{{%machinery}}',
            'functional_status_id',
            '{{%functional_status}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-machinery-location_id',
            '{{%machinery}}',
            'location_id',
            '{{%location}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // Eliminar claves foráneas
        $this->dropForeignKey('fk-machinery-mining_group_id', '{{%machinery}}');
        $this->dropForeignKey('fk-machinery-company_id', '{{%machinery}}');
        $this->dropForeignKey('fk-machinery-fleet_id', '{{%machinery}}');
        $this->dropForeignKey('fk-machinery-area_id', '{{%machinery}}');
        $this->dropForeignKey('fk-machinery-functional_status_id', '{{%machinery}}');
        $this->dropForeignKey('fk-machinery-location_id', '{{%machinery}}');

        // Eliminar tabla
        $this->dropTable('{{%machinery}}');
        
        // Eliminar la enumeración
        $this->execute("DROP TYPE inspection_type");
    }
}
