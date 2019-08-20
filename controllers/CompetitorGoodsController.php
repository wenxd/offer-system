<?php

namespace app\controllers;

use app\models\Competitor;
use app\models\Customer;
use Yii;
use app\actions;
use app\models\CompetitorGoods;
use app\models\CompetitorGoodsSearch;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use app\models\Goods;

/**
 * CompetitorGoodsController implements the CRUD actions for CompetitorGoods model.
 */
class CompetitorGoodsController extends BaseController
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new CompetitorGoodsSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class'      => actions\CreateAction::className(),
                'modelClass' => CompetitorGoods::className(),
                'scenario'   => 'competitor_goods',
            ],
            'update' => [
                'class'      => actions\UpdateAction::className(),
                'modelClass' => CompetitorGoods::className(),
                'scenario'   => 'competitor_goods',
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => CompetitorGoods::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => CompetitorGoods::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => CompetitorGoods::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => CompetitorGoods::className(),
            ],
        ];
    }

    /**
     * 下载零件模板
     */
    public function actionDownload()
    {
        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
        $spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        $excel=$spreadsheet->setActiveSheetIndex(0);

        $letter = ['A', 'B', 'C', 'D', 'E', 'F'];
        $tableHeader = ['零件号', '竞争对手', '针对客户', '税率', '未税价格', '备注'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
        }

        $title = '竞争对手价格上传模板';
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle($title);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
        exit;
    }

    /**
     *批量上传零件
     */
    public function actionUpload()
    {
        //判断导入文件
        if (!isset($_FILES["FileName"])) {
            return json_encode(['code' => 500, 'msg' => '没有检测到上传文件']);
        } else {
            //导入文件是否正确
            if ($_FILES["FileName"]["error"] > 0) {
                return json_encode(['code' => 500, 'msg' => $_FILES["FileName"]["error"]]);
            } //导入文件类型
            else if ($_FILES['FileName']['type'] == 'application/vnd.ms-excel' ||
                $_FILES['FileName']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                $_FILES['FileName']['type'] == 'application/octet-stream'
            ) {
                //获取文件名称
                $ext = explode('.', $_FILES["FileName"]["name"]);
                $saveName = date('YmdHis') . rand(1000, 9999) . '.' . end($ext);
                //保存文件
                move_uploaded_file($_FILES["FileName"]["tmp_name"], $saveName);
                if (file_exists($saveName)) {
                    //获取excel对象
                    $spreadsheet = IOFactory::load($saveName);
                    //数据转换为数组
                    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                    //总数
                    $total = count($sheetData);
                    $num = 0;
                    $date = date('Y-m-d H:i:s');
                    foreach ($sheetData as $key => $value) {
                        if ($key > 1) {
                            //零件号
                            $a = trim($value['A']);
                            if (empty($a)) {
                                continue;
                            }
                            $goods = Goods::find()->where(['is_deleted' => Goods::IS_DELETED_NO])
                                ->andWhere(['goods_number' => $a])->one();
                            if (!$goods) {
                                return json_encode(['code' => 500, 'msg' => '在第' . $key . '行没有' . $a . '这个厂家号，请添加零件信息']);
                            }
                            //竞争对手
                            $b = trim($value['B']);
                            $competitor = Competitor::find()->where(['is_deleted' => Goods::IS_DELETED_NO])
                                ->andWhere(['name' => $b])->one();
                            if (!$competitor) {
                                return json_encode(['code' => 500, 'msg' => '在第' . $key . '行没有' . $b . '这个竞争对手，请添加竞争对手信息']);
                            }
                            //针对客户
                            $c = trim($value['C']);
                            $customer = Customer::find()->where(['is_deleted' => Goods::IS_DELETED_NO])
                                ->andWhere(['name' => $c])->one();
                            if (!$customer) {
                                return json_encode(['code' => 500, 'msg' => '在第' . $key . '行没有' . $c . '这个客户，请添加客户信息']);
                            }
                            //税率
                            $d = 0;
                            if ($value['D']) {
                                $d = trim($value['D']);
                            }
                            //未税价格
                            $e = 0;
                            if ($value['E']) {
                                $e = trim($value['E']);
                            }
                            $tax_price = number_format(($e * (1 + floatval($d/100))), 2, '.', '');
                            $competitorGoods = CompetitorGoods::find()->where([
                                'goods_id'      => $goods->id,
                                'competitor_id' => $competitor->id,
                                'customer'      => $customer->id
                            ])->one();
                            if (!$competitorGoods) {
                                $competitorGoods = new CompetitorGoods();
                            }
                            $competitorGoods->goods_id      = $goods->id;
                            $competitorGoods->goods_number  = $a;
                            $competitorGoods->competitor_id = $competitor->id;
                            $competitorGoods->customer      = $customer->id;
                            $competitorGoods->tax_rate      = $d;
                            $competitorGoods->price         = $e;
                            $competitorGoods->tax_price     = $tax_price;
                            $competitorGoods->offer_date    = $date;
                            if ($value['F']) {
                                $competitorGoods->remark = trim($value['F']);
                            }
                            if ($competitorGoods->save()) {
                                $num++;
                            } else {
                                return json_encode(['code' => 500, 'msg' => $competitorGoods->getErrors()]);
                            }
                        }
                    }
                }
                unlink('./' . $saveName);
                return json_encode(['code' => 200, 'msg' => '总共' . ($total - 1) . '条,' . '成功' . $num . '条'], JSON_UNESCAPED_UNICODE);
            }
        }
    }

}
