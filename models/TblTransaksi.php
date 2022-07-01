<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "{{%tbl_transaksi}}".
 *
 * @property string|null $nm_makanan
 * @property int|null $harga
 * @property int|null $tgl_transaksi
 * @property int $id
 */
class TblTransaksi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%transaksi}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nm_makanan', 'harga', 'tgl_transaksi'], 'required'],
            [['harga'], 'integer'],
            [['nm_makanan'], 'string', 'max' => 50],
            [['nm_makanan, harga, tgl_transaksi'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'nm_makanan' => Yii::t('app', 'Nm Makanan'),
            'harga' => Yii::t('app', 'Harga'),
            'tgl_transaksi' => Yii::t('app', 'Tgl Transaksi'),
            'id' => Yii::t('app', 'ID'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_date'],
                ],
            ],
        ];
    }
}
