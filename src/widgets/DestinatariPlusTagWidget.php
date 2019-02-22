<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

namespace lispa\amos\cwh\widgets;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use lispa\amos\cwh\AmosCwh;


class DestinatariPlusTagWidget extends Widget
{

    public $model;

    /**
     * @return \yii\db\ActiveRecord
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param \yii\db\ActiveRecord $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    public function run()
    {
        return $this->render('destinatari-plus-tag', [
            'model' => $this->model
        ]);
    }

}