<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%machinery_type}}".
 *
 * @property int $id
 * @property int|null $mining_group_id
 * @property string $name
 * @property string|null $description
 * @property string|null $photo_base_url
 * @property string|null $photo_path
 * @property string $prefix
 * @property int|null $last_number
 *
 * @property Machinery[] $machineries
 * @property MiningGroup $miningGroup
 */
class MachineryType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%machinery_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mining_group_id', 'last_number'], 'integer'],
            [['name', 'prefix'], 'required'],
            [['description'], 'string'],
            [['name', 'photo_base_url', 'photo_path'], 'string', 'max' => 255],
            [['prefix'], 'string', 'max' => 5],
            [['mining_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MiningGroup::class, 'targetAttribute' => ['mining_group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'mining_group_id' => Yii::t('app', 'Mining Group ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'photo_base_url' => Yii::t('app', 'Photo Base Url'),
            'photo_path' => Yii::t('app', 'Photo Path'),
            'prefix' => Yii::t('app', 'Prefix'),
            'last_number' => Yii::t('app', 'Last Number'),
        ];
    }

    /**
     * Gets query for [[Machineries]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MachineryQuery
     */
    public function getMachineries()
    {
        return $this->hasMany(Machinery::class, ['machinery_type_id' => 'id']);
    }


    public function getMiningGroup()
    {
        return $this->hasOne(MiningGroup::class, ['id' => 'mining_group_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\MachineryTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\MachineryTypeQuery(get_called_class());
    }



    public function generatePrefix($name)
    {
        $name = mb_strtoupper(trim($name));
        $words = explode(' ', $name);

        if (count($words) == 1) {
            $prefix = mb_substr($words[0], 0, 2);
        } else {
            $prefix = '';
            foreach ($words as $word) {
                if (!empty($word)) {
                    $prefix .= mb_substr($word, 0, 1);
                }
            }
        }

        if (strlen($prefix) < 2) {
            $prefix = str_pad($prefix, 2, $prefix);
        }

        $this->prefix = $prefix;

        if ($this->last_number === null) {
            $this->last_number = 0;
        }

        return $prefix;
    }
    public function generateNextTag()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->last_number += 1;
            $this->save(false); // false para evitar validación

           
            $formattedNumber = str_pad($this->last_number, 3, '0', STR_PAD_LEFT);

           
            $tag = "{$this->prefix}-{$formattedNumber}";

            $transaction->commit();
            return $tag;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
