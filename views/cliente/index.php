<?php

use yii\helpers\Html;
use yii\grid\GridView;

Yii::$container->set('yii\grid\ActionColumn', ['header' => 'Ações']);

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClienteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clientes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cliente-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Cadastrar Cliente', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            'nome',
            'email:email',
            'cpf',

            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => [
                    'style' => 'color:#007bff'
                ] 
            ],
        ],
        'layout'=>"{items}\n{summary}\n{pager}",
        'emptyText' => 'Nenhum registro encontrado!',
    	'summary' => 'Exibindo {begin}-{end} de {count} registro(s).',
    ]); ?>


</div>
