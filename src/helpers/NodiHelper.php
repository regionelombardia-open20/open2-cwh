<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

namespace lispa\amos\cwh\helpers;

class NodiHelper extends \yii\base\Component
{

    public static function text(\lispa\amos\core\record\Record $model)
    {
        $recordPubblicato = \lispa\amos\cwh\models\CwhConfig::getConfig($model->tableName());
        return $recordPubblicato;
    }

}