<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    Open20Package
 * @category   CategoryName
 */
use open20\amos\cwh\AmosCwh;

/**
 * @var yii\web\View $this
 * @var open20\amos\cwh\models\CwhConfig $model
 */

$this->title = AmosCwh::t('amoscwh', 'Crea {modelClass}', [
    'modelClass' => 'Cwh Config',
]);
$this->params['breadcrumbs'][] = ['label' => AmosCwh::t('amoscwh', 'Cwh Config'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cwh-config-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
