<?php

use lispa\amos\core\views\AmosGridView;
use lispa\amos\cwh\AmosCwh;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var lispa\amos\cwh\models\search\CwhPubblicazioniSearch $searchModel
 */

$this->title = AmosCwh::t('amoscwh', 'Cwh Pubblicazioni');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cwh-pubblicazioni-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo         Html::a(AmosCwh::t('amoscwh', 'Nuovo {modelClass}', [
    'modelClass' => 'Cwh Pubblicazioni',
])        , ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>

    <?php Pjax::begin();
    echo AmosGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'cwh_config_id',
            'cwh_regole_pubblicazione_id',

            [
                'class' => 'lispa\amos\core\views\grid\ActionColumn',
            ],
        ],
    ]);
    Pjax::end(); ?>

</div>
