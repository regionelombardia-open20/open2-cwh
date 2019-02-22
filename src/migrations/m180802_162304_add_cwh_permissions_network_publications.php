<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh\migrations
 * @category   CategoryName
 */

use yii\db\Migration;
use lispa\amos\cwh\models\CwhConfig;
use lispa\amos\cwh\utility\CwhUtil;

class m180802_162304_add_cwh_permissions_network_publications extends Migration
{
    public function safeUp()
    {

//        $this->execute('CREATE TABLE ' . \lispa\amos\cwh\models\CwhAuthAssignment::tableName().'_bk' . ' LIKE ' . \lispa\amos\cwh\models\CwhAuthAssignment::tableName());
//        $this->execute('INSERT INTO ' . \lispa\amos\cwh\models\CwhAuthAssignment::tableName().'_bk'. ' (SELECT * FROM ' . \lispa\amos\cwh\models\CwhAuthAssignment::tableName(). ')');
        /** @var lispa\amos\cwh\AmosCwh $moduleCwh */
        $moduleCwh = Yii::$app->getModule('cwh');
        if (!empty($moduleCwh)) {
            $cwhConfigs = CwhConfig::find()->andWhere(['not', ['tablename' => 'user']])->all();
            foreach ($cwhConfigs as $cwhConfig) {
                $networkObject = Yii::createObject($cwhConfig->classname);
                $mmTable = Yii::createObject($networkObject->getMmClassName());
                echo "Adding cwh permission for network type: ".$networkObject->formName()."\n";
                $networks = $networkObject->find()->all();
                foreach ($networks as $network) {
                    echo $cwhConfig->tablename . " ". $network->id.": ";
                    $userNetworkMms = $mmTable->find()->andWhere([$networkObject->getMmNetworkIdFieldName() => $network->id])->all();
                    $membersCount = count($userNetworkMms);
                    echo $membersCount . " members\n";
                    foreach ($userNetworkMms as $userNetworkMm) {
                        CwhUtil::setCwhAuthAssignments($network, $userNetworkMm);
                    }
                }
            }
            return true;
        } else {
            echo "cwh module not found";
            return false;
        }

    }

    public function safeDown()
    {

        echo "m180802_162304_add_cwh_permissions_network_publications data will not be reverted\n";
        return true;
    }


}
