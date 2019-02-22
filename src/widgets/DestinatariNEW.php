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

use lispa\amos\admin\AmosAdmin;
use lispa\amos\community\models\Community;
use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\interfaces\ModelGrammarInterface;
use lispa\amos\core\interfaces\ModelLabelsInterface;
use lispa\amos\core\interfaces\OrganizationsModelInterface;
use lispa\amos\cwh\AmosCwh;
use lispa\amos\cwh\models\CwhConfig;
use lispa\amos\cwh\models\CwhNodi;
use lispa\amos\cwh\models\CwhRegolePubblicazione;
use lispa\amos\cwh\utility\CwhUtil;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class DestinatariNEW extends Widget
{
    /**
     * @var \yii\widgets\ActiveForm $form
     */
    protected $form = null;

    /**
     * @var \yii\db\ActiveRecord $model
     */
    protected $model = null;

    /**
     * @var string
     */
    protected $nameField = null;

    public function init()
    {
        parent::init();
        if (!isset($this->nameField)) {
            $refClass = new \ReflectionClass(get_class($this->getModel()));
            $this->setNameField($refClass->getShortName());
        }
    }

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
        $enableDestinatariFatherChildren = isset(AmosCwh::getInstance()->enableDestinatariFatherChildren) ? AmosCwh::getInstance()->enableDestinatariFatherChildren :  false;
        $networkModels = CwhConfig::find()->andWhere(['<>','tablename','user'])->all();
        $networks = CwhNodi::find()->andWhere(['NOT LIKE', 'id', 'user'])->all(); //GROUP BY PER CWH CONFIG ID O AND WHERE TIPO RETE

        $scope = AmosCwh::getInstance()->getCwhScope();
        $scopeFilter = (empty($scope))? false : true;

        $destinatariData = [];
        $destinatariForScope = [];
        /** @var CwhConfig $networkModel */
        foreach ($networkModels as $networkModel){
            $networkObject = Yii::createObject($networkModel->classname);
            $children  = [];
            if(!empty($scope[$networkModel->tablename])) {
                $children = $this->getNetworkChildren($scope[$networkModel->tablename], $networkModel);
            }
            $destinatari = [];
            $categoryName = "";
            /** @var CwhNodi $network */
            foreach ($networks as $network){
                if($scopeFilter){
                    if(isset($scope[$networkModel->tablename]) && ($network->record_id == $scope[$networkModel->tablename])){
                        $destinatariForScope[] = $network;
                    }
                    if( $enableDestinatariFatherChildren
                        && isset($scope[$networkModel->tablename])
                        && ($network->record_id == $scope[$networkModel->tablename]
                            || $this->isNetworkChild($network->record_id, $children)
                            || $this->isNetworkFather($network->record_id, $scope[$networkModel->tablename], $networkModel)
                            || $this->isNetworkBrother($network->record_id, $scope[$networkModel->tablename], $networkModel))) {
                        $destinatari[] = $network;
                    }
                }else {
                    $uid = null;
                    if(!$this->model->isNewRecord)
                    {
                        $uid = $this->model->created_by;
                    }
                    if ($network->classname == $networkModel->classname && $networkObject->isValidated($network->record_id) && $networkObject->isNetworkUser($network->record_id,$uid)) {
                        $destinatari[] = $network;
                        $destinatariForScope[] = $network;
                    }
                }
            }
            /** @var Community $model */
            $model = new $networkModel->classname;
            if($model instanceof ModelLabelsInterface){
                /** @var ModelGrammarInterface $networkModelGrammar */
                $networkModelGrammar = $model->getGrammar();
                if($networkModelGrammar) {
                    $categoryName = AmosCwh::t('messages', 'Solo gli utenti delle ') . $networkModelGrammar->getModelLabel();
                }
            } else {
                $categoryName = "";
            }

            if(!$scopeFilter || ($scopeFilter && $enableDestinatariFatherChildren)) {
                if (sizeof($destinatari) > 0) {
                    if (!array_key_exists($categoryName, $destinatariData)) {
                        $arr = [$categoryName => ArrayHelper::map($destinatari, 'id', 'text')];
                        $destinatariData = array_merge($destinatariData, $arr);
                    } else {
                        $destinatariData[$categoryName] = array_merge($destinatariData[$categoryName], ArrayHelper::map($destinatari, 'id', 'text'));
                    }

                }
            }
        }
        if ($this->getModel() instanceof ModelLabelsInterface) {
            $labelSuffix = ' ' . $this->getModel()->getGrammar()->getArticleSingular() . ' ' . $this->getModel()->getGrammar()->getModelSingularLabel();
        } else {
            $labelSuffix = ' ' . AmosCwh::t('amoscwh', 'il contenuto');
        }
        //if we are not under a specific network domain (destinatari and publication rule 3 are not automatically selected)
        if(!$scopeFilter){
            return $this->renderWithoutScope($destinatariData, $labelSuffix);
         //if we are inside a specific scope and the pubblication for network father and children is enabled
        } elseif($scopeFilter && $enableDestinatariFatherChildren) {
            return $this->renderWithScopeFatherChildren($destinatariForScope, $destinatariData, $labelSuffix);
         // if we are inside a scope
        } else  {
            return $this->renderWithScope($destinatariForScope, $labelSuffix);
        }

    }

    /**
     * @return \yii\widgets\ActiveForm
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param \yii\widgets\ActiveForm $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return string
     */
    public function getNameField()
    {
        return $this->nameField;
    }

    /**
     * @param string $nameField
     */
    public function setNameField($nameField)
    {
        $this->nameField = $nameField;
    }

    /**
     * @param $currentId
     * @param $networkId
     * @param $networkModel
     * @return bool
     */
    public function isNetworkFather($currentId, $networkId, $networkModel){
        if(!empty($networkId) && $networkModel){
            $classname = $networkModel->classname;
            $networkFather = $classname::findOne($networkId);
            return $currentId == $networkFather->parent_id;

        }
        return false;
    }

    /**
     * @param $currentId
     * @param $networkId
     * @param $networkModel
     * @return bool
     */
    public function isNetworkBrother($currentId, $networkId, $networkModel){
        if(!empty($networkId) && $networkModel) {
            $brothersIds = [];
            $classname = $networkModel->classname;
            $network = $classname::findOne($networkId);
            if ($network) {
                $networkFather = $classname::findOne($network->parent_id);
                if ($networkFather) {
                    $brothers = $classname::find()->andWhere(['parent_id' => $networkFather->id])->all();
                    foreach ($brothers as $brother) {
                        $brothersIds [] = $brother->id;
                    }
                    return in_array($currentId, $brothersIds);
                }
            }
        }
        return false;
    }

    /**
     * @param $currentId
     * @param $childrenIds
     * @return bool
     */
    public function isNetworkChild($currentId, $childrenIds){
        if(!empty($currentId)){
            return in_array($currentId, $childrenIds);

        }
        return false;
    }

    /**
     * @param $networkId
     * @param $networkModel
     * @param null $model
     * @return array
     */
    public function getNetworkChildren($networkId, $networkModel, $model = null){
        $children = [];
        /** INIZIALIZE THE MODEL */
        /** first step */
        if(!empty($networkId) && $networkModel){
            $classname = $networkModel->classname;
            $networkchildren = $classname::find()->andWhere(['parent_id' => $networkId])->all();
        }
        /** other steps */
        else {
            $children []= $model->id;
            $classname = $model->className();
            $networkchildren = $classname::find()->andWhere(['parent_id' => $model->id])->all();

        }

        /** exit condition for recursive */
        if(count($networkchildren) == 0){
            return $children;
        }

        /** MERGE THE RESULTS FORM THE  REVURSIVE CALLS */
        /** @var  $networkChild */
        foreach ($networkchildren as $networkChild){
            $children = ArrayHelper::merge($children,  $this->getNetworkChildren(null, null, $networkChild));

        }
        return $children;
    }

    /**
     * @param $destinatariData
     * @param $labelSuffix
     * @return string
     */
    public function renderWithoutScope($destinatariData, $labelSuffix){
        $primo_piano_checkbox = Yii::$app->user->can('UROI_LANDING') ?
            (Yii::$app->getModule('news')->params['site_publish_enabled']
                ? Html::checkbox('landing-checkbox', $this->model->regola_pubblicazione == 5, ['label' => AmosCwh::t('message', 'Proponi pubblicazione sul portale pubblico'), 'id' => 'landing-checkbox'])
                : '')
            : '';

        return $this->getForm()->field($this->getModel(), 'destinatari')->widget(
                \kartik\select2\Select2::className(), [
                    'name' => $this->getNameField() . '[destinatari]',
                    'data' => $destinatariData,
                    'options' => [
                        'multiple' =>  true,
//                        'value' => $scopeFilter ? $destinatari[0]['id'] : null,
                        'placeholder' => AmosCwh::t('amoscwh', 'Nella piattaforma'),
                        'name' => $this->getNameField() . '[destinatari]',
                        'id' => 'cwh-destinatari'
                    ],
                    'showToggleAll' => false,
                    'pluginOptions' => [
                        'tags' => true,
                        'maximumInputLength' => 10,
                    ],
                ]
            )->label(AmosCwh::t('amoscwh', 'Dove vuoi mostrare ').$labelSuffix). $primo_piano_checkbox;
    }

    /**
     * @param $destinatariForScope
     * @param $labelSuffix
     * @return
     */
    public function renderWithScope($destinatariForScope, $labelSuffix){
        //if we are under a network domain, publication is blocked for that network
        $destinatariField = $this->getForm()->field($this->getModel(),
            'destinatari')->label(AmosCwh::t('amoscwh', 'Dove vuoi mostrare '.$labelSuffix));

        $destinatariField->template = "<div class=\"row\">
                <div class=\"col-xs-12\">{label}</div>
                <div class=\"col-xs-12\"> <span class=\"tooltip-field pull-right\"> {hint} </span> <span class=\"tooltip-error-field pull-right\"> {error} </span> </div>
            \n<div class=\"col-xs-12\">" .\kartik\select2\Select2::widget([
                'id' => 'placeholder-destinatari',
                'name' => 'placeholder-destinatari',
                'data' => [0=>$destinatariForScope[0]['text']],
                'readonly' => true,
            ]) . "{input}</div>
            </div>";
        $destinatariField->hiddenInput(['value' => $destinatariForScope[0]['id'], 'id' => 'cwh-destinatari', 'name' => $this->getNameField() . '[destinatari][]']);
        return $destinatariField;

    }

    /**
     * @param $destinatariForScope
     * @param $destinatariData
     * @param $labelSuffix
     * @return
     */
    public function renderWithScopeFatherChildren($destinatariForScope, $destinatariData, $labelSuffix){
        $currentNewtworkScope = $destinatariForScope[0]['id'];
        if(empty($this->getModel()->destinatari)) {
            $this->getModel()->destinatari = $destinatariForScope[0]['id'];
        }
            return $this->getForm()->field($this->getModel(), 'destinatari')->widget(
                \kartik\select2\Select2::className(), [
                    'name' => $this->getNameField() . '[destinatari]',
                    'data' => $destinatariData,
                    'options' => [
                        'multiple' =>  true,
//                        'value' => $scopeFilter ? $destinatari[0]['id'] : null,
                        'placeholder' => AmosCwh::t('amoscwh', 'Nella piattaforma'),
                        'name' => $this->getNameField() . '[destinatari]',
                        'id' => 'cwh-destinatari',

                    ],
                    'showToggleAll' => false,
                    'pluginOptions' => [
                        'tags' => true,
                        'maximumInputLength' => 10,
                    ],
                    'pluginEvents' => [
                        "select2:unselecting" => "function(event) { 
                            if('$currentNewtworkScope' == event.params.args.data.id){
                              return false;
                            }
                       }",
                    ]
                ]
            )->label(AmosCwh::t('amoscwh', 'Dove vuoi mostrare ').$labelSuffix);
    }
}