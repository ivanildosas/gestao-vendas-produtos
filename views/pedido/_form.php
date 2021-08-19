<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use app\models\Cliente;
use app\models\Produto;
use app\models\ProdutoSearch;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\widgets\Pjax;


$data = [
    // ['id' => 1, 'qtd' => '1'],
];

$provider = new ArrayDataProvider([
    'allModels' => $data,
    'pagination' => [
        'pageSize' => 10,
    ],
    'sort' => [
        'attributes' => ['id', 'qtd'],
    ],
]);

$produtoDataProvider = new ActiveDataProvider([
    'query' => Produto::find(),
    'pagination' => [
        'pageSize' => 20,
    ],
]);

/* @var $this yii\web\View */
/* @var $model app\models\Pedido */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pedido-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php 
        echo $form->field($model, 'id_cliente')->dropDownList(
            ArrayHelper::map(Cliente::find()->asArray()->all(), 'id', 'nome'),
            ['prompt'=>'Selecione'],
            
        );
    ?>

    <?php Pjax::begin(['id' => 'pjax_produtos']) ?>
    Produtos:
    <?= GridView::widget([
        'id' => 'carrinho-grid',
        'dataProvider' => $provider,
        'columns' => [
            [
                'header' => 'Código',
                'value' => 'id',
                'headerOptions' => [
                    'style' => 'color:#007bff'
                ] 
            ],
            [
                'header' => 'Nome',
                'value' => 'nome',
                'headerOptions' => [
                    'style' => 'color:#007bff'
                ] 
            ],
            [
                'header' => 'Preço',
                'value' => 'preco',
                'headerOptions' => [
                    'style' => 'color:#007bff'
                ] 
            ],
            [
                'header' => 'Quantidade',
                'value' => 'qtd',
                'headerOptions' => [
                    'style' => 'color:#007bff'
                ] 
            ],
            
        ],
        'layout'=>"{items}\n{pager}",
        'emptyText' => '',
        'summary' => 'Exibindo {begin}-{end} de {count} registro(s).',
    ]); ?>
    <?php Pjax::end() ?>

    Adicionar Produto
    <?= GridView::widget([
        'id' => 'produtos-grid',
        'dataProvider' => $produtoDataProvider,
        'columns' => [
            [
                'header' => 'Código',
                'value' => 'id',
                'headerOptions' => [
                    'style' => 'color:#007bff'
                ] 
            ],
            'nome',
            'preco',
            [
                'header' => 'Disponível',
                'attribute' => 'quantidade',
                'headerOptions' => [
                    'style' => 'color:#007bff'
                ],
            ],
            
            [
                'header' => 'Quantidade',
                'attribute'=>'qtd',
                'value' => function($searchModel, $model){
                    return Html::textInput('qtd', '1');
                },
                'headerOptions' => [
                    'style' => 'color:#007bff'
                ],
                'format' => 'raw',
            ],
            
            [
                'class' => \yii\grid\ActionColumn::class,
                'header' => 'Ações',
                'headerOptions' => [
                    'style' => 'color:#007bff'
                ],
                'template' => '{add}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'add') {
                        $url =\yii\helpers\Url::to(['/your_controller/your_action', 'id' => $model->id]); 
                        return $url;
                    }                                            
                }, 
                'buttons' => [
                    'add' => function($url, $model, $key) {
                        return Html::button('Adicionar', 
                            [
                                'class' => 'btn btn-primary', 
                                'onclick' => '(function ($event) {
                                    var qtd = $("tr[data-key=' . $model->id . ']").find("td:eq(4) > input[type=text]")[0].value;
                                    var nome = $("tr[data-key=' . $model->id . ']").find("td:eq(1)")[0].innerHTML;
                                    var preco = $("tr[data-key=' . $model->id . ']").find("td:eq(2)")[0].innerHTML;
                                    addProduto(' . $model->id . ', nome, preco, qtd);
                                })();'
                            ]
                        );
                    },
                ],
                'visibleButtons' => [
                    'add' => true
                ],
            ],
        ],
        'layout'=>"{items}\n{summary}\n{pager}",
        'emptyText' => 'Nenhum registro encontrado!',
        'summary' => 'Exibindo {begin}-{end} de {count} registro(s).',
    ]); ?>

    <div class="form-group">
        <?= Html::button(
            'Salvar', 
            ['class' => 'btn btn-success',
             'id' => 'btnSalvar',
            'onclick' => '
                (function ($event) { 
                salvarPedido();
            })();'
            ]);
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    function addProduto(id, nome, preco, qtd) {
        $('#carrinho-grid').find('tbody:last').append(
            '<tr><td>' + id +'</td><td>' + nome +'</td><td>' + preco +'</td><td>' + qtd +'</td></tr>'
        );
    }

    function salvarPedido() {
        var id_cliente = $('#pedido-id_cliente').val();
        var valor = 0;
        var itens = [];

        if (!id_cliente) {
            alert('Selecione um cliente!');
            return;
        }

        $("#carrinho-grid  tbody tr").each(function(){
            id = $(this).children(':nth-child(1)').text(); 
            nome = $(this).children(':nth-child(2)').text();
            preco = $(this).children(':nth-child(3)').text();
            qtd = $(this).children(':nth-child(4)').text();

            if (id) {
                const item = {
                    id_produto: id, 
                    quantidade: qtd
                };
                itens.push(item);
                valor += preco * qtd;
            }
        });

        var pedido = {
            id_cliente: id_cliente,
            itens: itens,
            valor: valor
        };

        $.ajax({
            type: "POST",
            url: "<?=Yii::$app->urlManager->baseUrl . '/pedido/salvarPedido'?>",
            data: {
                pedido: pedido,
                _csrf : "<?=Yii::$app->request->getCsrfToken()?>" 
            },
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                }else {
                    alert(response.message);
                }
            },
            error: function (response) {
                alert("Erro ao salvar pedido: " + response);
                console.log(response);
            }
        });

    }

</script>