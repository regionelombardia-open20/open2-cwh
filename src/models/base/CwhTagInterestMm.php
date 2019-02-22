<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

namespace lispa\amos\cwh\models\base;

use lispa\amos\cwh\AmosCwh;
use mdm\admin\models\AuthItem;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "cwh_tag_interest_mm".
 *
 * @property integer $tag_id
 * @property string $classname
 * @property string $auth_item
 *
 * @property AuthItem $authItem
 * @property \lispa\amos\tag\models\Tag $tag
 */
class CwhTagInterestMm extends \lispa\amos\core\record\AmosRecordAudit
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cwh_tag_interest_mm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_id', 'classname', 'auth_item'], 'required'],
            [['tag_id'], 'integer'],
            [['classname', 'auth_item'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'tag_id' => AmosCwh::t('amoscwh', 'Root'),
            'classname' => AmosCwh::t('amoscwh', 'Model'),
            'auth_item' => AmosCwh::t('amoscwh', 'Item'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        $moduleTag = \Yii::$app->getModule('tag');
        if (isset($moduleTag)) {
            return $this->hasOne(\lispa\amos\tag\models\Tag::className(),
                ['id' => 'tag_id'])->inverseOf('tagModelsAuthItemsMms');
        }
        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItem()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'auth_item'])->inverseOf('tagModelsAuthItemsMms');
    }
}
