<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pedido".
 *
 * @property int $id
 * @property int $id_cliente
 * @property float $valor
 *
 * @property Cliente $cliente
 * @property PedidoProduto[] $pedidoProdutos
 */
class Pedido extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pedido';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_cliente', 'valor'], 'required'],
            [['id_cliente'], 'default', 'value' => null],
            [['id_cliente'], 'integer'],
            [['valor'], 'number'],
            [['id_cliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['id_cliente' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'id_cliente' => 'Cliente',
            'valor' => 'Valor',
        ];
    }

    /**
     * Gets query for [[Cliente]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Cliente::className(), ['id' => 'id_cliente']);
    }

    /**
     * Gets query for [[PedidoProdutos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPedidoProdutos()
    {
        return $this->hasMany(PedidoProduto::className(), ['id_pedido' => 'id']);
    }
}
