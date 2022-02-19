<?php

namespace app\common\lib;

use app\common\lib\FileTool;
use think\helper\Str;

use PhpOffice\PhpSpreadsheet\Reader\{Xlsx, Xls};
use PhpOffice\PhpSpreadsheet\{IOFactory, Spreadsheet};
use PhpOffice\PhpSpreadsheet\Cell\{Cell,DataType, Coordinate};
use PhpOffice\PhpSpreadsheet\Style\{NumberFormat, Alignment, Border, Color, Fill};
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
/**
 * Excel操作类
 */
class ExcelTool
{

    /**
     * 获取excel的reader
     * @param $file_path 本地文件地址
     * @return Xlsx|Xls
     * @author xk
     */
    private static function getReader($file_path)
    {
        $ext = FileTool::getExt($file_path);
        if ($ext == 'xlsx') {
            $reader = new Xlsx();
        }else if ($ext == 'xls') {
            $reader = new Xls();
        }else{
            throw new \Exception('不支持该文件类型');
        }

        return $reader;
    }

    /**
     * 读取excel (excel文件转数组)
     * @param string $file_path 本地文件地址
     * @param int  $sheet 指定sheet
     * @return array 
     * @author xk
     */
    public static function readExcel($file_path,$sheet=null)
    {
        if (empty($file) || !file_exists($file)) {
            throw new \Exception('文件不存在!');
        }

        $reader = self::getReader($file_path);
        $spreadsheet = $reader->setReadDataOnly(true)->load($file_path);

        if(is_null($sheet)){
            // 将Excel文件的当前显示sheet的数据转化为数组
            $data = $spreadsheet->getActiveSheet()->toArray();
        }else{
            // 将Excel文件的指定sheet的数据转化为数组
            $data = $spreadsheet->getSheet($sheet)->toArray();
        }
        
        return $data;
    }

    /**
    * 获取行索引名称
    * @param int $column_index
    * @return string
    */
    public static function getColumnName($column_index = 0)
    {
        static $_indexCache= array();
        if (!isset($_indexCache[$column_index])) {
            if ($column_index < 26) {
                $_indexCache[$column_index]= chr(65 + $column_index);
            }elseif ($column_index < 702) {
                $_indexCache[$column_index]= chr(64 + ($column_index / 26)). chr(65 + $column_index % 26);
            }else {
                $_indexCache[$column_index]= chr(64 + (($column_index - 26)/ 676)). chr(65 + ((($column_index - 26)% 676)/ 26)). chr(65 + $column_index % 26);
            }
        }
        return $_indexCache[$column_index];
    }


    /**
     * 下载excel
     * @param array $table_header 表头数组
     * @param array $data   该数组如果为一维数组，则填写一行，如果为二维数组，则多行数据
     * @param string $name  下载Excel的文件名
     * @param string $type  文件类型，如果不填写，默认下载为xlsx格式，如果任意填写数值为xls格式
     * @param int  $with 设置sheet默认列宽
     * @author xk
     */
    public static function downloadExcel(array $table_header,array $data,$name='',$type='Xlsx',$with=12)
    {
        //文件名
        if(empty($name)){
            $name = date("Y-m-d H:i:s")."_".rand(1000,9999);
        }

        //文件类型
        $type  = Str::studly($type);

        //实例化
        $Spreadsheet = new Spreadsheet();
        $sheet = $Spreadsheet->getActiveSheet();

        //设置表头
        $table_header = array_values($table_header);
        foreach($table_header as $k=>$v){
            $sheet->setCellValue(self::getColumnName($k).'1',$v);
        }

        //设置内容
        $sheet->fromArray($data,null,"A2");

        //样式设置
        $sheet->getDefaultColumnDimension()->setWidth($with);

        //设置下载与后缀
        if($type=="Xlsx"){
            header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            $suffix = "xlsx";
        }else{
            header("Content-Type:application/vnd.ms-excel");
            $type = "Xls";
            $suffix = "xls";
        }
        header("Content-Disposition:attachment;filename=$name.$suffix");
        header("Cache-Control:max-age=0");//缓存控制
        $writer = IOFactory::createWriter($Spreadsheet,$type);
        $writer->save("php://output");//数据流
        $Spreadsheet->disconnectWorksheets();
        unset($Spreadsheet);
    }

}