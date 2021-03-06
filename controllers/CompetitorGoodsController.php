<?php

namespace app\controllers;

use app\assets\Common;
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

        $letter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
        $tableHeader = ['品牌', '零件号', '竞争对手', '针对客户', '税率', '未税单价', '数量', '货期', '库存数量', '备注', '是否发行价'];
        for($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i].'1',$tableHeader[$i]);
        }

        $title = '竞争对手价格上传模板' . date('ymd-His');
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
                            if (!$value['F']) {
                                unlink('./' . $saveName);
                                return json_encode(['code' => 500, 'msg' => '第' . $key . '行数量不能为空'], JSON_UNESCAPED_UNICODE);
                            }
                        }
                    }
                    foreach ($sheetData as $key => $value) {
                        if ($key > 1) {
                            //零件号
                            $a = trim($value['B']);
                            if (empty($a)) {
                                continue;
                            }
                            $goods = Goods::find()->where(['is_deleted' => Goods::IS_DELETED_NO])
                                ->andWhere(['goods_number' => $a, 'material_code' => trim($value['A'])])->one();
                            if (!$goods) {
                                return json_encode(['code' => 500, 'msg' => '在第' . $key . '行没有' . $a . '这个品牌零件号，请添加零件信息']);
                            }
                            //竞争对手
                            $b = trim($value['C']);
                            $competitor = Competitor::find()->where(['is_deleted' => Goods::IS_DELETED_NO])
                                ->andWhere(['name' => $b])->one();
                            if (!$competitor) {
                                return json_encode(['code' => 500, 'msg' => '在第' . $key . '行没有' . $b . '这个竞争对手，请添加竞争对手信息']);
                            }
                            //针对客户
                            $c = trim($value['D']);
                            $customer = Customer::find()->where(['is_deleted' => Goods::IS_DELETED_NO])
                                ->andWhere(['name' => $c])->one();
                            if (!$customer) {
                                return json_encode(['code' => 500, 'msg' => '在第' . $key . '行没有' . $c . '这个客户，请添加客户信息']);
                            }
                            //税率
                            $d = 0;
                            if ($value['E']) {
                                $d = trim($value['E']);
                            }
                            //未税单价
                            $e = 0;
                            if ($value['F']) {
                                $e = trim($value['F']);
                            }
                            $tax_price = number_format(($e * (1 + floatval($d/100))), 2, '.', '');

                            $competitorGoods = new CompetitorGoods();
                            $competitorGoods->goods_id      = $goods->id;
                            $competitorGoods->goods_number  = $a;
                            $competitorGoods->competitor_id = $competitor->id;
                            $competitorGoods->customer      = $customer->id;
                            $competitorGoods->tax_rate      = $d;
                            $competitorGoods->price         = $e;
                            $competitorGoods->tax_price     = $tax_price;
                            $competitorGoods->number        = $value['G'] ? trim($value['G']) : 0;
                            $competitorGoods->all_price     = $competitorGoods->price * $competitorGoods->number;
                            $competitorGoods->all_tax_price = $competitorGoods->tax_price * $competitorGoods->number;
                            $competitorGoods->delivery_time = $value['H'] ? trim($value['H']) : 0;
                            $competitorGoods->stock_number  = $value['I'] ? trim($value['I']) : 0;
                            $competitorGoods->offer_date    = $date;
                            if ($value['J']) {
                                $competitorGoods->remark = trim($value['J']);
                            }
                            if ($value['K'] && $value['K'] == '是') {
                                $competitorGoods->is_issue = CompetitorGoods::IS_ISSUE_YES;
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

    /**
     * 竞争对手价格记录模板:根据品牌，零件号，竞争对手名称  导出未税单价
     */
    public function actionDownloadCompTemp()
    {
        // 品牌，零件号，竞争对手名称  导出未税单价
        $letter = ['A', 'B', 'C', 'D'];
        $tableHeader = self::ComTemp;
        $fileName = '竞争对手价格记录模板' . date('ymd-His');
        Common::DownloadTemp($letter, $tableHeader, $fileName);
    }   
    const ComTemp = ['品牌', '零件号', '竞争对手名称', '未税单价'];
    /**
     * 上传竞争对手价格记录模板
     */
    public function actionUploadCompTempCheck()
    {
        $cache = Yii::$app->cache;
        $key_name = 'upload_comp_temp_check';
        //判断导入文件
        if (!isset($_FILES["FileName"])) {
            if ($cache->exists($key_name)) {
                $data = json_decode($cache->get($key_name), true);
                $cache->delete($key_name);
                $fileName = '竞争对手价格记录检测结果.csv';
                header('Content-Description: File Transfer');
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                $fp = fopen('php://output', 'a');//打开output流
                foreach ($data as $rowData) {
                    mb_convert_variables('GBK', 'UTF-8', $rowData);
                    fputcsv($fp, $rowData);
                }
                unset($data);//释放变量的内存
                ob_flush();
                flush();//必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
                fclose($fp);
                exit();
            }
            return json_encode(['code' => 500, 'msg' => '没有检测到上传文件'], JSON_UNESCAPED_UNICODE);
        } else {
            //导入文件是否正确
            if ($_FILES["FileName"]["error"] > 0) {
                return json_encode(['code' => 500, 'msg' => $_FILES["FileName"]["error"]]);
            } else if ($_FILES['FileName']['type'] == 'application/vnd.ms-excel' || $_FILES['FileName']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $_FILES['FileName']['type'] == 'application/octet-stream') {
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
                    //组装数据
                    $data = [self::ComTemp];
                    foreach ($sheetData as $k => $v) {
                        if ($k > 1) {
                            $brand = trim($v['A']) ?? '';
                            $goods_number = trim($v['B']) ?? '';
                            $competitor = trim($v['C']) ?? '';
                            $info = [$brand, $goods_number, $competitor];
                            if (!$brand || !$goods_number || !$competitor) {
                                $info[] = '上传数据为空跳出';
                                $data[] = $info;
                                continue;
                            }
                            // 根据品牌和零件号查询零件信息
                            $goods_number_info = Goods::find()->select('id')
                                ->where(['goods_number' => $goods_number, 'material_code' => $brand])
                                ->asArray([''])->one();
                            if (empty($goods_number_info)) {
                                $info[] = '零件查询为空跳出';
                                $data[] = $info;
                                continue;
                            }
                            //查询竞争对手信息
                            $competitor_info = Competitor::find()->select('id')
                                ->where(['name' => $competitor])
                                ->asArray([''])->one();
                            if (empty($competitor_info)) {
                                $info[] = '竞争对手查询为空跳出';
                                $data[] = $info;
                                continue;
                            }
                            //查询数据
                            $competitor_goods_info = CompetitorGoods::find()->select('price')
                                ->where(['goods_id' => $goods_number_info['id'], 'competitor_id' => $competitor_info['id']])
                                ->asArray()->all();
                            if (empty($competitor_goods_info)) {
                                $info[] = '';
                                $data[] = $info;
                                continue;
                            }
                            foreach ($competitor_goods_info as $item) {
                                $info_copy = $info;
                                $info_copy[] = $item['price'];
                                $data[] = $info_copy;
                            }
                        }
                    }
                    if (count($data) > 1) {
                        $cache->set($key_name, json_encode($data), 60);
                    }
                    unlink('./' . $saveName);
                    return json_encode(['code' => 200, 'msg' => '数据生成成功'], JSON_UNESCAPED_UNICODE);
                }
                return json_encode(['code' => 500, 'msg' => "数据生成失败"], JSON_UNESCAPED_UNICODE);
            }
        }
    }

}
