<?php
use lispa\amos\core\forms\ActiveForm;
use lispa\amos\cwh\AmosCwh;

use yii\bootstrap\Tabs;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var lispa\amos\cwh\models\CwhRegolePubblicazione $model
 * @var yii\widgets\ActiveForm $form
 */


?>

<div class="cwh-regole-pubblicazione-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php $this->beginBlock('generale'); ?>

    <div class="col-lg-6 col-sm-6">

        <?= $form->field($model, 'nome')->textInput(['maxlength' => true])->label('Nome'); ?>
    </div>

    <div class="col-lg-6 col-sm-6">

        <?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?>
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
    <?php
    echo \lispa\amos\core\forms\CloseSaveButtonWidget::widget(['model' => $model]);
    ActiveForm::end(); ?>
</div>
