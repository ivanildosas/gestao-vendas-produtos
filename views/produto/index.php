<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProdutoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

Yii::$container->set('yii\grid\ActionColumn', ['header' => 'Ações']);

$this->title = 'Produtos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Cadastrar Produto', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'nome',
            'preco',
            'quantidade',
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
