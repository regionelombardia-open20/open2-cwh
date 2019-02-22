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

use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\interfaces\ModelLabelsInterface;
use lispa\amos\core\interfaces\OrganizationsModelInterface;
use lispa\amos\core\user\User;
use lispa\amos\cwh\AmosCwh;
use lispa\amos\cwh\models\search\CwhNodiSearch;
use kartik\widgets\Select2;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class ValidatoriNEW extends Validatori
{

    public function run()
    {

        if (!count(AmosCwh::getInstance()->validateOnStatus)) {
            throw new InvalidConfigException(AmosCwh::t('amoscwh', 'E\' necessario impostare il campo validateOnStatus nella configuazione della CWH per il model {classname}', [
                'classname' => get_class($this->model)
            ]));
        } else {
            $config = AmosCwh::getInstance()->validateOnStatus[get_class($this->model)];
        }
        $isUpdate = false;
        if (!in_array($this->model->{$config['attribute']}, $config['statuses'])) {
            $isUpdate = true;
        }

        $data = [];
        $nodi = CwhNodiSearch::findByModel($this->getModel());
        $data = ArrayHelper::merge($data, ArrayHelper::map(
            $nodi, 'id', 'text'
        ));
        $validators = [];
        $i = 0;
        $scope = AmosCwh::getInstance()->getCwhScope();
        $scopeFilter = (empty($scope))? false : true;
        $myown_rule = null;

//        if($this->model->createdUserProfile) {
//            $userProfile = $this->model->createdUserProfile;
//            $name = $userProfile->getNomeCognome();
//        } else {
            $name = \Yii::$app->user->identity->profile->getNomeCognome();
        //}

        foreach ($data as $key => $value){
            if($scopeFilter){
                $pos = strpos($key,'-');
                $scopeKey = substr($key, 0 ,$pos);
                if(isset($scope[$scopeKey]) && $scope[$scopeKey] == $nodi[$i]->record_id) {
                    $validators[$key] = $name; //. ' (' . $data[$key] . ')'; // nomecognome utente
                }
            } else {
                if ( strpos($key, 'user-') !== false) {
                    $user = User::findOne($nodi[$i]->record_id);
                    if (!is_null($user)) {
                        $myown_rule = array($key => $name);
                    }
                    $validators[$key] = $name; //.' for '.$data[$key];
                } else {
                    if(!$scopeFilter) {
                        $networkObject = \Yii::createObject($nodi[$i]->classname);
                        if ($isUpdate || $networkObject->isValidated($nodi[$i]->record_id)) {
//                            if(array_key_exists('lispa\amos\community\models\CommunityContextInterface', class_implements($networkObject))){
//                                $validators[AmosCwh::t('amoscwh', 'Community')][$key] = $name . ' (' . $data[$key] . ')';
//                            }
                            if(array_key_exists('lispa\amos\core\interfaces\OrganizationsModelInterface', class_implements($networkObject))){
                                $validators[AmosCwh::t('amoscwh', 'Organizzazioni')][$key] = $name . ' (' . $data[$key] . ')';
                            }
                        }
                    }
                }
            }
            $i++;
        }
        $data = $validators;
        /***
         * for add My own key at the beginning of array.
         */
        if(!empty($myown_rule)){
            $data = array_merge($myown_rule, $data);
        }

        //if(count($data) == 0) {
        $creator = $this->model->created_by;
        $creatorName = '';
        if($creator) {
            $creatorKey = 'user-' . $creator;
            if(!array_key_exists($creator, $data)) {
                $user = UserProfile::findOne(['user_id' => $creator])->getNomeCognome();
                $creatorName = $user;
                $data[$creatorKey] = $user;
            }
        }
//            else {
//                $user = UserProfile::findOne(['id' => \Yii::$app->user->id])->getNomeCognome();
//                $data['user-' . \Yii::$app->getUser()->getId()] = $user;
//            }
        //}
        $value = [];
        //$data = array_unique($data);
        if($this->getModel()->isNewRecord)
        {
            $value = array_keys($data)[0];
        }
        else
        {
            $value = $this->getModel()->validatori;
            if(is_array($value)) {
                $data[array_keys($value)[0]] = $creatorName;
            } else {
                $data[$value] = $creatorName;
            }
        }

        return $this->getForm()->field($this->getModel(), 'validatori')->widget(
            Select2::className(), [
                'name' => $this->getNameField() . '[validatori]',
                'disabled' => $isUpdate && !$this->model->isNewRecord,
                'data' => $data,
                //'readonly' => true,
                'options' => [
                    'id'=> 'validatori-cwh',
                    'placeholder' => AmosCwh::t('amoscwh', '#3col_sender_placeholder'),
                    'name' => $this->getNameField() . '[validatori]',
                    'value' => $value,
                    'disabled' => !$this->getModel()->isNewRecord,
                ],
                'pluginOptions' => [
                    'maximumInputLength' => 10
                ],
            ]
        )->label(AmosCwh::t('amoscwh', 'Scegli la firma')); // TODO traduzione corretta
    }

}