<?php

/**
 * @var yii\web\View $this
 * @var lispa\amos\cwh\models\CwhRegolePubblicazione $model
 */

use lispa\amos\cwh\AmosCwh;

$this->title = AmosCwh::t('amoscwh', 'Create {modelClass}', [
    'modelClass' => 'Cwh Regole Pubblicazione',
]);
$this->params['breadcrumbs'][] = ['label' => AmosCwh::t('amoscwh', 'Cwh Regole Pubblicazione'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cwh-regole-pubblicazione-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
