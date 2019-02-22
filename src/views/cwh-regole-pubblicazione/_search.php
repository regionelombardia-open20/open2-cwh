<?php

use yii\helpers\Html;
use lispa\amos\core\forms\ActiveForm;

use lispa\amos\cwh\AmosCwh;

/**
 * @var yii\web\View $this
 * @var lispa\amos\cwh\models\search\CwhRegolePubblicazioneSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="cwh-regole-pubblicazione-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'codice') ?>

    <div class="form-group">
        <?= Html::submitButton(AmosCwh::t('amoscwh', 'Cerca'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(AmosCwh::t('amoscwh', 'Annulla'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
