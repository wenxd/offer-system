<?php


namespace app\assets;

use app\models\SystemNotice;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use yii\console\Exception;

class Common
{
    /**
     * 下载模板
     */
    public static function DownloadTemp($letter, $tableHeader, $fileName = false)
    {
        if (!is_array($letter) || !is_array($tableHeader)) {
            throw new Exception('letter, tableHeader参数格式错误');
        }
        $letter_len = count($letter);
        $tableHeader_len = count($tableHeader);
        if ($letter_len != $tableHeader_len) {
            throw new Exception('letter, tableHeader 数据长度不对等');
        }
        if (!$fileName) {
            $fileName = '模板' . date('ymd-His');
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
        $excel = $spreadsheet->setActiveSheetIndex(0);

        for ($i = 0; $i < count($tableHeader); $i++) {
            $excel->getStyle($letter[$i])->getAlignment()->setVertical('center');
            $excel->getColumnDimension($letter[$i])->setWidth(18);
            $excel->setCellValue($letter[$i] . '1', $tableHeader[$i]);
        }
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle($fileName);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
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
     * 发送系统消息
     */
    public static function SendSystemMsg($admin_id, $content, $notice_at = false)
    {
        if (empty($admin_id) || empty($content)) {
            throw new Exception('admin_id, content不能为空');
        }
        if (!$notice_at) $notice_at = date('Y-m-d H:i:s');
        $systemNotice = new SystemNotice();
        $systemNotice->admin_id = $admin_id;
        $systemNotice->content = $content;
        $systemNotice->notice_at = $notice_at;
        $systemNotice->save();
    }
}