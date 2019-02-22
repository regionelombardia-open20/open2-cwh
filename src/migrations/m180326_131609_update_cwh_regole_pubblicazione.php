<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh\migrations
 * @category   CategoryName
 */

use lispa\amos\cwh\models\CwhConfig;
use lispa\amos\cwh\models\CwhConfigContents;
use lispa\amos\cwh\models\CwhPubblicazioni;
use lispa\amos\cwh\models\CwhPubblicazioniCwhNodiEditoriMm;
use lispa\amos\cwh\models\CwhPubblicazioniCwhNodiValidatoriMm;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m180326_131609_alter_cwh_regole_pubblicazione
 */
class m180326_131609_update_cwh_regole_pubblicazione extends Migration
{

    public function up()
    {
        try {

            $this->update('cwh_regole_pubblicazione', ['nome' => 'Solo gli utenti per aree di interesse'], ['id' => 2]);

            return true;

        } catch (\yii\base\Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    public function down()
    {
        try {
            $this->update('cwh_regole_pubblicazione', ['nome' => 'Tutti gli utenti con specifici "tag aree di interesse"'], ['id' => 2]);

            return true;

        } catch (\yii\base\Exception $ex) {
            echo $ex->getMessage();
            return false;
        }

    }

}

