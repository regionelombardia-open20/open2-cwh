<?php

namespace lispa\amos\cwh\widgets;

use lispa\amos\core\helpers\Html;
use lispa\amos\core\interfaces\ModelLabelsInterface;
use lispa\amos\cwh\AmosCwh;
use lispa\amos\cwh\models\CwhRegolePubblicazione;
use lispa\amos\cwh\utility\CwhUtil;
use kartik\select2\Select2;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * Class RegolaPubblicazione
 * @package lispa\amos\cwh\widgets
 */
class RegolaPubblicazioneNEW extends Widget
{
    protected $data = [];

    protected $default = null;
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

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $cwhModule = AmosCwh::getInstance();
        $scope = $cwhModule->getCwhScope();

        //if we are working under a specific network scope (eg. community dashboard)
        $scopeFilter = (empty($scope) ? false : true);

        $regolePubblicazioneQuery = CwhUtil::getPublicationRulesQuery();
        $regolePubblicazioneQuery->andWhere(['not', ['id' => 5]]);
        $regolePubblicazione = $regolePubblicazioneQuery->all();
        //if working in a network scope only rules based on the network membership are available
        if ($scopeFilter) {
            $this->setDefault($regolePubblicazione[0]);
        }
        $this->setData($regolePubblicazione);

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
        $model = $this->getModel();
        if ($model instanceof ModelLabelsInterface) {
            $labelSuffix = ' ' . $model->getGrammar()->getArticleSingular() . ' ' . $model->getGrammar()->getModelSingularLabel();
        } else {
            $labelSuffix = ' ' . AmosCwh::t('amoscwh', 'il contenuto');
        }

        $pluginName = "";

        $cwhModule = AmosCwh::getInstance();
        $scope = $cwhModule->getCwhScope();

        //if we are working under a specific network scope (eg. community dashboard)
        $scopeFilter = (empty($scope) ? false : true);

        $data = ArrayHelper::map($this->getData(), 'id', 'nome');

        $ruleOneEnabled =  array_key_exists(CwhRegolePubblicazione::ALL_USERS, $data);
        $ruleTwoEnabled = array_key_exists(CwhRegolePubblicazione::ALL_USERS_WITH_TAGS, $data);
        $ruleThreeEnabled = array_key_exists(CwhRegolePubblicazione::ALL_USERS_IN_DOMAINS, $data);
        $ruleFourEnabled = array_key_exists(CwhRegolePubblicazione::ALL_USERS_IN_DOMAINS_WITH_TAGS, $data);
        if($ruleThreeEnabled){
            $data[CwhRegolePubblicazione::ALL_USERS_IN_DOMAINS] = AmosCwh::t('amoscwh', 'Tutti gli utenti');
        }
        if($ruleFourEnabled){
            $data[CwhRegolePubblicazione::ALL_USERS_IN_DOMAINS_WITH_TAGS] = AmosCwh::t('amoscwh', 'Solo gli utenti per aree di interesse');
        }
        $publicationRules = [];

        if(!$scopeFilter && empty($model->destinatari)){
            $enabledRules = array_keys($data);
            foreach ($enabledRules as $enabledRule){
                if($enabledRule <= CwhRegolePubblicazione::ALL_USERS_WITH_TAGS){
                    $publicationRules[$enabledRule] = $data[$enabledRule];
                }
            }
            $regolaPubblicazioneDefault = $ruleOneEnabled ? 1 : 2;
        }else {
            $publicationRules = array_unique($data);
            $regolaPubblicazioneDefault = $ruleThreeEnabled ? 1 : 2;
        }

        $rules = [
            1 => AmosCwh::t('amoscwh', 'Tutti gli utenti'),
        ];
        if($ruleTwoEnabled || $ruleFourEnabled){
            $rules[2] = AmosCwh::t('amoscwh', 'Solo gli utenti per aree di interesse');
        }
        if (count($this->getData()) > 1) {

            $js = <<<JS
                
                (function($) {
                    var origAppend = $.fn.append;
                
                    $.fn.append = function () {
                        return origAppend.apply(this, arguments).trigger("append");
                    };
                })(jQuery);
                    
                    var opt = $("#cwh-regola_pubblicazione").children("option[value=1]");
                    var saveOpt = $(opt).clone();
                    $($(".field-cwh-destinatari").find(".select2-selection__rendered")[0]).on('append', function (e) {
                        if($(".field-cwh-destinatari").find(".select2-selection__choice").length > 0){
                            e.preventDefault();
                            $("#cwh-regola_pubblicazione").append(saveOpt);
                            $("#cwh-regola_pubblicazione").select2("destroy");
                            $("#cwh-regola_pubblicazione").select2(select2_3e79cec5);
                        } else {
                            var opt = $("#cwh-regola_pubblicazione").children("option[value=1]");
                            $(opt).remove();
                            $("#cwh-regola_pubblicazione").select2("destroy");
                            $("#cwh-regola_pubblicazione").select2(select2_3e79cec5);
                            $("#cwh-regola_pubblicazione").val("2").trigger("change");
                        }
                    })

JS;

            if(!$ruleOneEnabled) {
                $this->view->registerJs($js);
            }

            if(!empty($model->destinatari) && !$scopeFilter) {
                $this->view->registerJs(<<<JS
                 var optVal1 = $("#cwh-regola_pubblicazione").children("option[value=1]");
                    
                    if($(".field-cwh-destinatari").find(".select2-selection__choice").length <= 0){
                        if($("input[id$=\"-regola_pubblicazione\"]").val() != undefined && $("input[id$=\"-regola_pubblicazione\"]").val() != 1 && $("input[id$=\"-regola_pubblicazione\"]").val() != 3) {
                            $(optVal1).remove();
                        }
                    }
JS
);
            }

            if(!$scopeFilter && !$ruleOneEnabled && !in_array($model->regola_pubblicazione, [CwhRegolePubblicazione::ALL_USERS_IN_DOMAINS, CwhRegolePubblicazione::ALL_USERS_IN_DOMAINS_WITH_TAGS])){
                $this->view->registerJs(<<<JS
                    var optV1 = $("#cwh-regola_pubblicazione").children("option[value=1]");
                    if($(".field-cwh-destinatari").find(".select2-selection__choice").length <= 0){
                        $(optV1).remove();
                    }
JS
);
            }

            if($model->regola_pubblicazione == 3){
                $model->regola_pubblicazione = 1;
            }
            if($model->regola_pubblicazione == 4){
                $model->regola_pubblicazione = 2;
            }

            $widget = '
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">'.Html::label(AmosCwh::t('amoscwh', 'Chi può visualizzare ') . $labelSuffix,null,['class' => 'control-label']) . '</div>
                        <div class="col-xs-12">' .
                            Select2::widget([
                                'name' => 'regola',
                                'value' => $model->regola_pubblicazione ? $model->regola_pubblicazione : $regolaPubblicazioneDefault,
                                'data' => $rules,
                                'options' => [
                                    'placeholder' => AmosCwh::t('amoscwh','#3col_recipients_placeholder'),
                                    'name' => $this->getNameField() . '[regola_pubblicazione]',
                                    'id' => 'cwh-regola_pubblicazione',
                                    'value' => $this->getDefault(),
                                ]
                            ]

                        ) . '
                        </div>
                    </div>' . $this->getForm()->field($model, 'regola_pubblicazione')->hiddenInput([
                    'value' => $model->regola_pubblicazione ? $model->regola_pubblicazione : $regolaPubblicazioneDefault,])->label(false) .
                '</div>';
        } else {
            $regolaDiPubblicazioneField = $this->getForm()->field($model,
                'regola_pubblicazione')->label(AmosCwh::t('amoscwh', 'Chi può visualizzare '). $labelSuffix); // TODO traduzione corretta
            $RegolaDiPubblicazione = $this->getData()[0];

            $regolaDiPubblicazioneField->template = "
                <div class=\"row\">
                    <div class=\"col-xs-6\">{label}</div>
                    <div class=\"col-xs-6\"> <span class=\"tooltip-field pull-right\"> {hint} </span> <span class=\"tooltip-error-field pull-right\"> {error} </span> </div>
                    <div class=\"col-xs-12\"><strong>" . $RegolaDiPubblicazione['nome'] . "</strong>{input}</div>
                </div>";

            $widget = $regolaDiPubblicazioneField->hiddenInput(['value' => $RegolaDiPubblicazione['id'], 'id' => 'cwh-regola_pubblicazione',]);
        }

        return $widget;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
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
     * @return null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param null $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }
}