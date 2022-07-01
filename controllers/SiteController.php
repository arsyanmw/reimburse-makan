<?php

namespace app\controllers;
require_once '../vendor/autoload.php';

use app\components\Helper;
use app\models\TblMakanan;
use app\models\TblTransaksi;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $bulanSekarang = date('Y') . "-" . date('m') . "-26";
        $bulanSebelum = date('Y') . "-0" . (date('m') - 1) . "-26";

        $conditions = "DATE(tgl_transaksi) BETWEEN '$bulanSebelum' AND '$bulanSekarang'";

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => TblTransaksi::find()->where($conditions),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $totalReimburse = TblTransaksi::find()->where($conditions)->sum('harga');

        $transaksi = new TblTransaksi();
        $makanan = new TblMakanan();

        if ($post = Yii::$app->request->post()) {
            $transaksi->load($post);
            if ($transaksi->validate()) {
                $makanan->load($post, 'TblTransaksi');
                if ($transaksi->save() && $makanan->save()) {
                    Yii::$app->session->setFlash('success', 'Data berhasil disimpan');
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $transaksi,
            'total' => $totalReimburse,
        ]);
    }

    public function actionExportData() {
        $bulanSekarang = date('Y') . "-" . date('m') . "-26";
        $bulanSebelum = date('Y') . "-0" . (date('m') - 1) . "-26";
        $conditions = "DATE(tgl_transaksi) BETWEEN '$bulanSebelum' AND '$bulanSekarang'";

        $model = TblTransaksi::find()->where($conditions)->all();
        $total = TblTransaksi::find()->where($conditions)->sum('harga');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No')->getColumnDimension('A')->setAutoSize(true);
        $sheet->setCellValue('B1', 'Tanggal')->getColumnDimension('B')->setAutoSize(true);
        $sheet->setCellValue('C1', 'Nama Makanan')->getColumnDimension('C')->setAutoSize(true);
        $sheet->setCellValue('D1', 'Harga')->getColumnDimension('D')->setAutoSize(true);

        $row = 2;
        $i = 1;
        foreach ($model as $data) {
            $sheet->setCellValue('A' . $row, $i);
            $sheet->setCellValue('B' . $row, Helper::formatDateIndonesia($data->tgl_transaksi, false, false));
            $sheet->setCellValue('C' . $row, ucfirst($data->nm_makanan));
            $sheet->setCellValue('D' . $row, "Rp " . number_format($data->harga));
            $row++;
            $i++;
        }

        $sheet->mergeCells('A'.$row.':C'.($row+1));
        $sheet->mergeCells('D'.$row.':D'.($row+1));

        $styleTotal = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::VERTICAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $sheet->setCellValue('A' . $row, 'Total Reimburse')->getStyle('A'.$row)->applyFromArray($styleTotal);
        $sheet->setCellValue('D' . $row, "Rp " . number_format($total))->getStyle('D'.$row)->applyFromArray($styleTotal);

        $styleHeader = [
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $styleData = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:D1')->applyFromArray($styleHeader);
        $sheet->getStyle('A2:D' . ($row + 1))->applyFromArray($styleData);

        $filename = "Reimburse Makan " . date('m') . "_" . date('Y') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$filename);
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die();
    }

    public function actionDelete($id) {
        $transaksi = TblTransaksi::findOne($id);
        $makanan= TblMakanan::findOne($id);
        if ($transaksi->delete() && $makanan->delete()) {
            Yii::$app->session->setFlash('success', 'Data berhasil dihapus');
            return $this->redirect(['index']);
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
