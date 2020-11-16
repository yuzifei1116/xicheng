<?php


namespace service;


class PHPCSVServer
{
    /**
     * 通用的导出类
     * @param $title 导出的标题
     * @param $headArr 导出内容表头
     * @param $exportData 导出数据内容
     */
    public static function exportCommon($title, $headArr, $exportData)
    {
        $total = count($exportData);
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv("UTF-8", "GB18030", $title) . ".csv");
        $fp = fopen('php://output', 'a');
        $headName = array_keys($headArr);
        $headValue = array_values($headArr);
        foreach ($headValue as $k=>&$v){
            $v = iconv("UTF-8", "GB18030", $v);
        }
        fputcsv($fp, $headValue);
        $size = 2000;
        $groupNum = ceil($total / $size);
        for ($i = 0; $i <= $groupNum; $i++) {
            $exportDataTemp = array_slice($exportData, $size * $i, $size);
            foreach ($exportDataTemp as $item) {
                $rows = array();
                foreach ($headName as $name) {
                    $rows[] =  iconv("UTF-8", "GB18030", $item[$name]);
                }
                fputcsv($fp, $rows);
            }
            ob_flush();
            flush();
        }
    }
}