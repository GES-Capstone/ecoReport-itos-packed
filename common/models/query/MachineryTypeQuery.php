<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\MachineryType]].
 *
 * @see \common\models\MachineryType
 */
class MachineryTypeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\MachineryType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\MachineryType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
