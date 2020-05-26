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
 * @var open20\amos\cwh\models\CwhPubblicazioni $model
 */

$this->title = AmosCwh::t('amoscwh', 'Create {modelClass}', [
    'modelClass' => 'Cwh Pubblicazioni',
]);
$this->params['breadcrumbs'][] = ['label' => AmosCwh::t('amoscwh', 'Cwh Pubblicazioni'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cwh-pubblicazioni-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
