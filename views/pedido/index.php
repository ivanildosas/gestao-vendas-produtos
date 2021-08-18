<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PedidoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pedidos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Cadastrar Pedido', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'id_cliente',
            'valor',
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
