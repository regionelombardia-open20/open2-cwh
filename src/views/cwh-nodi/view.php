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
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var open20\amos\cwh\models\CwhNodi $model
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => AmosCwh::t('amoscwh', 'Cwh Nodis'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cwh-nodi-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'cwh_config_id',
            'record_id',
            'classname',
        ],
    ]) ?>

</div>
