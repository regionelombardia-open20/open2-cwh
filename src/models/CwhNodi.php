<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

namespace lispa\amos\cwh\models;
use yii\base\Exception;
use lispa\amos\cwh\models\base\CwhNodiView;

/**
 * This is the model class for table "cwh_nodi".
 */
class CwhNodi extends \lispa\amos\cwh\models\base\CwhNodi
{
    private $text;
/*
    public static function find()
    {
        return new \lispa\amos\cwh\models\query\CwhNodiQuery(get_called_class());
    }
*/
    public static function primaryKey()
    {
        return [
            'id'
        ];
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        if(!$this->text) {
            if (isset($this->record_id)) {
                $NodeRecordClass = \Yii::createObject($this->classname);
                if ($NodeRecordClass:: findOne($this->record_id)) {
                    $this->text = $NodeRecordClass:: findOne($this->record_id)->__toString();
                }
            }
        }
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     *
     */
    public static function mustReset(){

        try{
            $maxdate = CwhNodi::find()->max('updated_at');
            $maxdate_view = CwhNodiView::find()->max('updated_at');
            if($maxdate != $maxdate_view) {
                \Yii::$app->db->createCommand()->truncateTable(CwhNodi::tableName())->execute();
                \Yii::$app->db->createCommand('INSERT ' . CwhNodi::tableName() . ' SELECT * FROM ' . CwhNodiView::tablename())->execute();
            }
        }catch(Exception $ex){
            \Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
    }

}
