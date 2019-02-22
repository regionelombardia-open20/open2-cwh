<?php

use lispa\amos\cwh\AmosCwh;
use yii\widgets\DetailView;
use lispa\amos\core\forms\CloseButtonWidget;
use lispa\amos\core\forms\ContextMenuWidget;

/**
 * @var yii\web\View $this
 * @var lispa\amos\cwh\models\CwhConfig $model
 */

$this->title = $model->tablename;
$this->params['breadcrumbs'][] = ['label' => AmosCwh::t('amoscwh', 'Cwh Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cwh-config-view">

    <?= ContextMenuWidget::widget([
        'model' => $model,
        'actionModify' => "/cwh/cwh-config/update?id=" . $model->id,
        'actionDelete' => "/cwh/cwh-config/delete?id=" . $model->id,
    ]) ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'classname',
            'tablename',
            'visibility',
        ],
    ]) ?>

    <?= CloseButtonWidget::widget(['urlClose' => '/cwh/cwh-config/index']) ?>

</div>
