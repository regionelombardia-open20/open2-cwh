<?php
use lispa\amos\core\views\AmosGridView;
use lispa\amos\cwh\AmosCwh;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var lispa\amos\cwh\models\search\CwhNodiSearch $searchModel
 */

$this->title = AmosCwh::t('amoscwh', 'Cwh Nodis');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cwh-nodi-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php  echo         \lispa\amos\core\helpers\Html::a(AmosCwh::t('amoscwh', 'Nuovo {modelClass}', [
    'modelClass' => 'Cwh Nodi',
])        , ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin();
    echo AmosGridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'cwh_config_id',
            'record_id',
            'classname',

            [
                'class' => 'lispa\amos\core\views\grid\ActionColumn',
            ],
        ],
    ]);
    Pjax::end(); ?>

</div>
