<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%tbl_makanan}}".
 *
 * @property int $id
 * @property string|null $nama
 * @property int|null $harga
 * @property int|null $created_date
 *
 * @property Transaksi[] $transaksis
 */
class TblMakanan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tbl_makanan}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['harga', 'created_date'], 'integer'],
            [['nm_makanan'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nama' => Yii::t('app', 'Nama Makanan'),
            'harga' => Yii::t('app', 'Harga'),
            'created_date' => Yii::t('app', 'Created Date'),
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
