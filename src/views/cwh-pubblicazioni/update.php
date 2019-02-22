<?php
use lispa\amos\cwh\AmosCwh;

/**
 * @var yii\web\View $this
 * @var lispa\amos\cwh\models\CwhPubblicazioni $model
 */


$this->title = AmosCwh::t('amoscwh', 'Update {modelClass}: ', [
        'modelClass' => 'Cwh Pubblicazioni',
    ]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => AmosCwh::t('amoscwh', 'Cwh Pubblicazioni'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = AmosCwh::t('amoscwh', 'Update');
?>
<div class="cwh-pubblicazioni-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
