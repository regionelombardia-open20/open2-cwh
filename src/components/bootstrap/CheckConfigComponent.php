<?php

namespace lispa\amos\cwh\components\bootstrap;

use lispa\amos\cwh\models\CwhConfig;
use lispa\amos\cwh\models\CwhConfigContents;
use yii\base\ActionEvent;
use \Yii;

/**
 * Class CheckConfigComponent
 *
 * @package lispa\amos\cwh\components\bootstrap
 */
class CheckConfigComponent extends \yii\base\Component
{
    public function checkConf(ActionEvent $event)
    {
        if (!(Yii::$app instanceof Yii\console\Application))
        {
            $actionId = $event->action->uniqueId;
            $controllerId = $event->action->controller->uniqueId;
            $configsNetwork = CwhConfig::getConfigs();
            $configsContents = CwhConfigContents::getConfigs();

            if (!count($configsNetwork) || !count($configsContents)) {
                if ($controllerId != 'cwh/configuration' && $actionId != \Yii::$app->getUser()->loginUrl[0]) {
                    return \Yii::$app->getResponse()->redirect('/cwh/configuration/wizard');
                }
            }
        }
    }

}