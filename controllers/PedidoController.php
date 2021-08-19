<?php

namespace app\controllers;

use app\models\Pedido;
use app\models\PedidoProduto;
use app\models\PedidoSearch;
use app\models\Produto;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\db\Transaction;
use yii\base\UserException;
use yii\helpers\Json;


/**
 * PedidoController implements the CRUD actions for Pedido model.
 */
class PedidoController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'salvarPedido' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Pedido models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PedidoSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Pedido model.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Pedido model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Pedido();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Pedido model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Pedido model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Pedido model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Pedido the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pedido::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSalvarPedido() {

        Yii::$app->controller->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $result = [
            'success' => 'true',
            'message' => 'Pedido cadastrado.'
        ];

        $request = Yii::$app->request;
        $data = $request->post('pedido');

        if (!$data){
            throw new UserException( "Falha ao salvar pedido.");    
        }

        $model = new Pedido();
        $model->id_cliente = $data['id_cliente'];
        $model->valor = $data['valor'];

        $transaction = $model->getDb()->beginTransaction();
        try {
            if ($model->save()) {
                foreach ($$data['itens'] as $item){
                    $pedidoProduto = new PedidoProduto();
                    $pedidoProduto->$id_pedido = $model->id;
                    $pedidoProduto->$id_produto = $item['id_produto'];
                    $pedidoProduto->$quantidade = $item['quantidade'];

                    if (($modelProduto = Produto ::findOne($item['id_produto'])) !== null) {
                        if ($modelProduto->quantidade < $item['quantidade']){
                            throw new UserException( "Não há estoque suficiente para fechar o pedido.");    
                        }

                        $modelProduto->quantidade = $modelProduto->quantidade - $item['quantidade'];
                        if (!$modelProduto->save()) {
                            throw new UserException( "Falha ao atualizar produto.");    
                        }

                    } else {
                        throw new NotFoundHttpException('Produto não encontrado.');
                    }

                    if (!$pedidoProduto->save()) {
                        throw new UserException( "Falha ao salvar o Pedido" );
                        print_r($model->getErrors());
                    }
                    return $result;
                }
            } else {
                throw new UserException( "Falha ao salvar o Pedido" );
                print_r($model->getErrors());
            }
            
        } catch (Exception $e) {
            $transaction->rollBack();
            return [
                'success' => 'false',
                'message' => $e->getMessage()
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return [
                'success' => 'false',
                'message' => $e->getMessage()
            ];
        }
    }
}
