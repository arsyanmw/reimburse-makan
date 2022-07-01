<?php

/** @var yii\web\View $this */
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use app\components\Helper;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">MAKAN GRATIS!</h1>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-12" style="display: flex; justify-content: space-between">
                <div>
                    <p style="font-weight: bold; font-size: 30px">Total Reimburse Rp <?= number_format($total); ?></p>
                </div>
                <div>
                    <?= Html::button('Tambah', ['class' => 'btn btn-primary', 'id' => 'add-transaksi']) ?>
                    <?= Html::a('<i class="fa fa-file-excel-o"></i> Export to Excel', ['/site/export-data'], ['class' => 'btn btn-success', 'pjax-0' => false]); ?>
                </div>
            </div>
            <div class="col-lg-12">
                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'header' => 'Nama Makanan',
                            'attribute' => 'nm_makanan',
                            'value' => function ($model) {
                                return ucfirst($model->nm_makanan);
                            }
                        ],
                        [
                            'header' => 'Harga',
                            'attribute' => 'harga',
                            'value' => function ($model) {
                                return "Rp " . number_format($model->harga);
                            }
                        ],
                        [
                            'header' => 'Tanggal',
                            'attribute' => 'tgl_transaksi',
                            'value' => function ($model) {
                                return Helper::formatDateIndonesia($model->tgl_transaksi, false, false);
                            }
                        ],
                        [
                                'header' => "Aksi",
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{delete}',
                        ]
                    ],
                ]); ?>
            </div>
        </div>

    </div>

    <div class="modal fade in" tabindex="-1" id="modal-transaksi">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <?php $form = ActiveForm::begin(); ?>
                    <?= $form->field($model, 'nm_makanan')->label('Nama Makanan') ?>
                    <?= $form->field($model, 'harga')->input('number') ?>
                    <?= $form->field($model, 'tgl_transaksi')->widget(DatePicker::className(), [
                        'dateFormat' => 'yyyy-MM-dd',
                        'options' => [
                            'class' => 'form-control',
                        ],
                    ])->label('Tanggal') ?>
                    <?= Html::submitButton('Tambah', ['class' => 'btn btn-primary', 'name' => 'submit-transaksi']) ?>
                    <?php $form = ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#add-transaksi").on("click", function () {
            $("#modal-transaksi").modal("show");
        });

        $("#export-transaksi").on("click", function () {
            $.ajax({
               url: 'site/export-data',
                type: 'GET',
                success: function(data) {
                    console.log(data);
                }
            })
        });
    })
</script>