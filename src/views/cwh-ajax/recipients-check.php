<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\views\AmosGridView;
use lispa\amos\cwh\AmosCwh;
use yii\data\ActiveDataProvider;

?>
<div id='recipients-grid' data-pjax-container='' data-pjax-timeout='1000'>
<?php if(!empty($validators)): ?>
    <strong><?= AmosCwh::t('amoscwh', '#3col_sender_label{labelSuffix}', ['labelSuffix' => $labelSuffix])?>: </strong><?= $validators ?><br/>
<?php endif; ?>
<?php if(!empty($publicationRule)): ?>
    <strong><?= AmosCwh::t('amoscwh', '#3col_recipients_label{labelSuffix}', ['labelSuffix' => $labelSuffix])?>: </strong><?= $publicationRule ?><br/>
<?php endif; ?>
<?php if(!empty($tagValues)): ?>
    <strong><?= AmosCwh::t('amoscwh', 'Tags') ?>: </strong><?= $tagValues ?><br/>
<?php endif; ?>
<?php if(!empty($scopes)): ?>
    <strong><?= AmosCwh::t('amoscwh', 'Scopes') ?>: </strong><?= $scopes ?><br/>
<?php endif; ?>

<div class="search-recipients">
    <div class="container-tools">
        <div class="col-xs-12">
            <div class="col-sm-6 col-sm-push-6 btn-search-recipients-check">
                <?= Html::input('text', null, $searchName, [
                    'id' => 'search-recipients',
                    'class' => 'form-control pull-left',
                    'placeholder' => AmosCwh::t('amoscwh', 'Search ...')
                ]) ?>
                <?= Html::a(AmosIcons::show('search'),
                    null,
                    [
                        'id' => 'search-recipients-btn',
                        'class' => 'btn btn-tools-secondary',
                    ])
                ?>
                <?= Html::a(AmosIcons::show('close'),
                    null,
                    [
                        'id' => 'reset-search-recipients-btn',
                        'class' => 'btn btn-danger-inverse',
                        'alt' => AmosCwh::t('amoscwh', 'Cancel recipient search')
                    ])
                ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
         <?= AmosGridView::widget([
             'dataProvider' => new ActiveDataProvider(['query' => $query]),
             'columns' => [
                 'photo' => [
                     'headerOptions' => [
                         'id' => AmosAdmin::t('amosadmin', 'Photo'),
                     ],
                     'contentOptions' => [
                         'headers' => AmosAdmin::t('amosadmin', 'Photo'),
                     ],
                     'label' => AmosAdmin::t('amosadmin', 'Photo'),
                     'format' => 'raw',
                     'value' => function ($model) {
                         $url = $model->getAvatarUrl();
                         $viewUrl = "/admin/user-profile/view?id=" . $model->id;
                         $img = Html::tag('div', Html::img($url, [
                             'class' => Yii::$app->imageUtility->getRoundImage($model)['class'],
                             'style' => "margin-left: " . Yii::$app->imageUtility->getRoundImage($model)['margin-left'] . "%; margin-top: " . Yii::$app->imageUtility->getRoundImage($model)['margin-top'] . "%;",
                             'alt' => $model->getNomeCognome()
                         ]),
                             ['class' => 'container-round-img-sm']);
                         $options = ['title' =>  AmosAdmin::t('amosadmin', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $model->nomeCognome])];
                         return Html::a($img, $viewUrl, $options);
                     }
                 ],
                 'name' => [
                     'attribute' => 'nomeCognome',
                     'headerOptions' => [
                         'id' => AmosAdmin::t('amosadmin', 'Name'),
                     ],
                     'contentOptions' => [
                         'headers' => AmosAdmin::t('amosadmin', 'Name'),
                     ],
                     'label' => AmosAdmin::t('amosadmin', 'Name'),
                     'value' => function($model){
                         return Html::a($model->nomeCognome, ['/admin/user-profile/view', 'id' => $model->id ], [
                             'title' => AmosAdmin::t('amosadmin', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $model->nomeCognome])
                         ]);
                     },
                     'format' => 'html'
                 ],
                 'status' => [
                     'attribute' => 'status',
                     'headerOptions' => [
                         'id' => AmosAdmin::t('amosadmin', 'Status'),
                     ],
                     'contentOptions' => [
                         'headers' => AmosAdmin::t('amosadmin', 'Status'),
                     ],
                     'label' => AmosAdmin::t('amosadmin', 'Status'),
                     'value' => function ($model) {
                         return $model->hasWorkflowStatus() ? $model->getWorkflowStatus()->getLabel() : '--';
                     }
                 ]
             ],
         ]);
         ?>
        </div>
    </div>
</div>

