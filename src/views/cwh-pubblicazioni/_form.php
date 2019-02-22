<?php
use lispa\amos\core\forms\ActiveForm;
use lispa\amos\cwh\AmosCwh;
use yii\bootstrap\Tabs;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var lispa\amos\cwh\models\CwhPubblicazioni $model
 * @var yii\widgets\ActiveForm $form
 */


?>

<div class="cwh-pubblicazioni-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="form-actions">

        <?= Html::submitButton($model->isNewRecord ?
            AmosCwh::t('amoscwh', 'Crea') :
            AmosCwh::t('amoscwh', 'Aggiorna'),
            [
                'class' => $model->isNewRecord ?
                    'btn btn-success' :
                    'btn btn-primary'
            ]); ?>

    </div>


    <?php $this->beginBlock('generale'); ?>

    <div class="col-lg-6 col-sm-6">

        <?= // generated by schmunk42\giiant\crud\providers\RelationProvider::activeField
        $form->field($model, 'cwh_config_id')->dropDownList(
            \yii\helpers\ArrayHelper::map(lispa\amos\cwh\models\CwhConfig::find()->all(), 'id', 'id'),
            ['prompt' => AmosCwh::t('amoscwh', 'Select')]
        ); ?>
    </div>

    <div class="col-lg-6 col-sm-6">

        <?= // generated by schmunk42\giiant\crud\providers\RelationProvider::activeField
        $form->field($model, 'cwh_regole_pubblicazione_id')->dropDownList(
            \yii\helpers\ArrayHelper::map(lispa\amos\cwh\models\CwhRegolePubblicazione::find()->all(), 'id', 'id'),
            ['prompt' => AmosCwh::t('amoscwh', 'Select')]
        ); ?>
    </div>
    <div class="clearfix"></div>
    <?php $this->endBlock(); ?>

    <?php $itemsTab[] = [
        'label' => AmosCwh::t('amoscwh', 'generale '),
        'content' => $this->blocks['generale'],
    ];
    ?>

    <?= Tabs::widget(
        [
            'encodeLabels' => false,
            'items' => $itemsTab
        ]
    );
    ?>
    <?php ActiveForm::end(); ?>
</div>
