<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "initial_configuration".
 *
 * @property int $id
 * @property int $step
 * @property string $status
 * @property int $mining_group_id
 */
class InitialConfiguration extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%initial_configuration}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['step', 'mining_group_id'], 'integer'],
            [['status'], 'string'],
            [['mining_group_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'step' => 'Step',
            'status' => 'Status',
            'mining_group_id' => 'Mining Group ID',
        ];
    }
}
