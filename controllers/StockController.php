<?php

namespace app\controllers;

use app\models\Admin;
use app\models\AuthAssignment;
use app\models\Order;
use app\models\StockLog;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Yii;
use app\actions;
use app\models\Stock;
use app\models\StockSearch;
use yii\helpers\ArrayHelper;

/**
 * StockController implements the CRUD actions for Stock model.
 */
class StockController extends BaseController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => actions\IndexAction::className(),
                'data'  => function(){
                    $searchModel  = new StockSearch();
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class'      => actions\CreateAction::className(),
                'modelClass' => Stock::className(),
                'scenario'   => 'stock',
            ],
            'update' => [
                'class'      => actions\UpdateAction::className(),
                'modelClass' => Stock::className(),
                'scenario'   => 'stock',
            ],
            'delete' => [
                'class'      => actions\DeleteAction::className(),
                'modelClass' => Stock::className(),
            ],
            'sort' => [
                'class'      => actions\SortAction::className(),
                'modelClass' => Stock::className(),
            ],
            'status' => [
                'class'      => actions\StatusAction::className(),
                'modelClass' => Stock::className(),
            ],
            'view' => [
                'class'      => actions\ViewAction::className(),
                'modelClass' => Stock::className(),
            ],
        ];
    }

    public function actionAddress()
    {
        $params = Yii::$app->request->post();
        $num = Stock::updateAll(['position' => $params['address']], ['id' => $params['list']]);
        if ($num) {
            return json_encode(['code' => 200, 'msg' => '保存成功']);
        } else {
            return json_encode(['code' => 500, 'msg' => '失败']);
        }
    }

    /**导出数据
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionDownload()
    {
        $use_admin = AuthAssignment::find()->where(['item_name' => ['库管员', '系统管理员']])->all();
        $adminIds  = ArrayHelper::getColumn($use_admin, 'user_id');

        $adminList = Admin::find()->where(['id' => $adminIds])->all();
        $admins = [];
        foreach ($adminList as $key => $admin) {
            $admins[$admin->id] = $admin->username;
        }
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

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R'];
        $tableHeader = ['ID', '零件号', '中文描述', '英文描述', '税率', '未税单价', '含税单价', '库存位置', '库存数量',
            '建议库存', '高储', '低储', '是否有库存', '库存不足', '库存超量', '设备类别', '所属部件', '设备信息'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getStyle($letter[$i])->getNumberFormat()->applyFromArray(['formatCode' => NumberFormat::FORMAT_TEXT]);
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
        }

        //获取数据
        $stockList = Stock::find()->select('*')->all();

        foreach ($stockList as $key => $stock) {
            for($i = 0; $i < count($letter); $i++) {
                //id
                $excel->setCellValue($letter[$i] . ($key + 2), $stock->id);
                if ($stock->goods) {
                    //零件号
                    $excel->setCellValue($letter[$i+1] . ($key + 2), $stock->goods->goods_number);
                    //中文描述
                    $excel->setCellValue($letter[$i+2] . ($key + 2), $stock->goods->description);
                    //英文描述
                    $excel->setCellValue($letter[$i+3] . ($key + 2), $stock->goods->description_en);
                    //设备类别
                    $excel->setCellValue($letter[$i+15] . ($key + 2), $stock->goods->material_code);
                    //所属部件
                    $excel->setCellValue($letter[$i+16] . ($key + 2), $stock->goods->part);
                    //设备信息
                    $excel->setCellValue($letter[$i+17] . ($key + 2), $stock->goods->device_info);
                } else {
                    //零件号
                    $excel->setCellValue($letter[$i+1] . ($key + 2), '');
                    //中文描述
                    $excel->setCellValue($letter[$i+2] . ($key + 2), '');
                    //英文描述
                    $excel->setCellValue($letter[$i+3] . ($key + 2), '');
                }
                //税率
                $excel->setCellValue($letter[$i+4] . ($key + 2), $stock->tax_rate);
                //未税单价
                $excel->setCellValue($letter[$i+5] . ($key + 2), $stock->price);
                //含税单价
                $excel->setCellValue($letter[$i+6] . ($key + 2), $stock->tax_price);
                //库存位置
                $excel->setCellValue($letter[$i+7] . ($key + 2), $stock->position);
                //库存数量
                $excel->setCellValue($letter[$i+8] . ($key + 2), $stock->number);
                //建议库存
                $excel->setCellValue($letter[$i+9] . ($key + 2), $stock->suggest_number);
                //高储
                $excel->setCellValue($letter[$i+10] . ($key + 2), $stock->high_number);
                //低储
                $excel->setCellValue($letter[$i+11] . ($key + 2), $stock->low_number);
                //是否为0
                $excel->setCellValue($letter[$i+12] . ($key + 2), ($stock->number ? '是' : '否'));
                //库存不足
                $excel->setCellValue($letter[$i+13] . ($key + 2), ($stock->number < $stock->low_number ? '是' : '否'));
                //库存超量
                $excel->setCellValue($letter[$i+14] . ($key + 2), ($stock->number > $stock->high_number ? '是' : '否'));
                break;
            }
        }

        $title = '库存' . date('ymd-His');
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
}
