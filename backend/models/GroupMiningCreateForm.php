<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\MiningGroup;
/**
 * Account form
 */
class GroupMiningCreateForm extends Model
{
    public $ges_name;
    public $name;
    public $miningGroup;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ges_name'], 'filter', 'filter' => 'trim'],
            [['ges_name'], 'string', 'min' => 1, 'max' => 255],
            [['ges_name'], 'unique',
                'targetClass' => '\common\models\MiningGroup',
                'message' => Yii::t('backend', 'This name has already been taken.'),
                'filter' => function ($query) {
                    $query->andWhere(['not', ['id' => Yii::$app->user->getId()]]);
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ges_name' => Yii::t('backend', 'Nombre del Grupo Minero'),
        ];
    }


    /**
     * @inheritdoc
     */

     public function save() 
     {
         if ($this->validate()) {
             $miningGroup = new MiningGroup();
             $miningGroup->ges_name = $this->ges_name;
             $miningGroup->name = $this->ges_name;
             if ($miningGroup->save()) {
                 $this->miningGroup = $miningGroup;
                 return true;
             }
         }
         return false;
     }
}
