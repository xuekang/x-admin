<?php

namespace app\common\tools;

use think\helper\Str;


/**
 * 字符串相关工具类
 */
class StringTool
{
	/**
     * 生成全局唯一id
     * @param string $prefix 前缀
     * @param int $length 长度
     * @return string
     * @author xk
     */
    public static function createGuid($prefix='',$length=0){
        $uuid = md5(uniqid(strval(mt_rand()), true));

        if($length){
            $uuid = substr($uuid,0,$length);
        }
        return $prefix . $uuid;
        // return uniqid();
    }
	
	/**
	 * 判断指定字符串是否在ids中
     * @param string $str 指定字符串
     * @param string $ids_str 待查询字符串
     * @param string $delimiter 分隔符
     * @return boolean
     * @author xk
	 */
	public static function inIds($str, $ids_str,$delimiter=',')
	{
	    $ids_arr = explode($delimiter, $ids_str);
	    if (in_array($str, $ids_arr)) {
	        return true;
	    } else {
	        return false;
	    }
	}

    /**
	 * 判断指定字符串ids是否全包含在ids中
     * @param string $ids 指定字符串
     * @param string $ids_str 待查询字符串
     * @param string $delimiter 分隔符
     * @return boolean
     * @author xk
	 */
    public static function idsInIds($ids, $ids_str,$delimiter=',')
    {
        if (!$ids) {
            return false;
        }
        $arr = explode($delimiter, $ids);
        $ids_arr = explode($delimiter, $ids_str);
        foreach ($arr as $val) {
            if (!in_array($val, $ids_arr)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取字符串首字符串缩写
     * @param string $str 原始字符串
     * @param string $delimiter 分隔符
     * @param string $data 
     * @author xk
     */
	public static function getShortStr($str='', $delimiter='_')
	{
		$data = [];
		$str_arr = explode($delimiter,$str);
		foreach($str_arr as $k=>$v){
			$item = str_split($v);
			if(isset($item[0])){
				$data[] = $item[0];
			}
		}
		$data = implode('',$data);
		return $data;
	}


	/**
	 * 计算 UTF-8 字符串长度
	 * @param string $str
	 * @return int
	 * @author 艾伟
	 */
	public static function strlenUTF8($str)
	{
	    $i = 0;
	    $count = 0;
	    $len = strlen($str);
	    while ($i < $len) {
	        $chr = ord($str[$i]);
	        $count++;
	        $i++;
	        if ($i >= $len) {
	            break;
	        }
	        if ($chr & 0x80) {
	            $chr <<= 1;
	            while ($chr & 0x80) {
	                $i++;
	                $chr <<= 1;
	            }
	        }
	    }

	    return $count;
	}

	/**
	 * 数字金额转换成中文大写金额的函数
	 * @param int|string $num  要转换的小写数字或小写字符串
	 * @return string 大写字符串(小数位为两位)
	 */
	public static function num2zh($num)
	{
	    $c1 = "零壹贰叁肆伍陆柒捌玖";
	    $c2 = "分角元拾佰仟万拾佰仟亿";
	    //精确到分后面就不要了，所以只留两个小数位
	    $num = round($num, 2);
	    //将数字转化为整数
	    $num = $num * 100;
	    if (strlen($num) > 10) {
	        return "金额太大，请检查";
	    }
	    $i = 0;
	    $c = "";
	    while (1) {
	        if ($i == 0) {
	            //获取最后一位数字
	            $n = substr($num, strlen($num) - 1, 1);
	        } else {
	            $n = $num % 10;
	        }
	        //每次将最后一位数字转化为中文
	        $p1 = substr($c1, 3 * $n, 3);
	        $p2 = substr($c2, 3 * $i, 3);
	        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
	            $c = $p1 . $p2 . $c;
	        } else {
	            $c = $p1 . $c;
	        }
	        $i = $i + 1;
	        //去掉数字最后一位了
	        $num = $num / 10;
	        $num = (int)$num;
	        //结束循环
	        if ($num == 0) {
	            break;
	        }
	    }
	    $j = 0;
	    $slen = strlen($c);
	    while ($j < $slen) {
	        //utf8一个汉字相当3个字符
	        $m = substr($c, $j, 6);

	        //处理数字中很多0的情况,每次循环去掉一个汉字“零”
	        if ($m == '零元' || $m == '零亿' || $m == '零零') {
	            $left = substr($c, 0, $j);
	            $right = substr($c, $j + 3);
	            $c = $left . $right;
	            $j = $j - 3;
	            $slen = $slen - 3;
	        } else if ($m == '零万') {
	            $left = substr($c, 0, $j);
	            $right = substr($c, $j + 6);
	            $c = $left . '万零' . $right;
	            $j = $j - 3;
	            $slen = $slen - 3;
	        }
	        $j = $j + 3;
	    }
	    //这个是为了去掉类似23.0中最后一个“零”字
	    if (substr($c, strlen($c) - 3, 3) == '零') {
	        $c = substr($c, 0, strlen($c) - 3);
	    }
	    //将处理的汉字加上“整”
	    if (empty($c)) {
	        return "零元整";
	    } else {
	        return $c . "整";
	    }
	}

	/**
	 * 根据身份证号获取性别
	 * @param string $card     身份证号
	 * @return string   性别
	 * @author           ai
	 */
	public static function getSexByCard($card)
	{
	    if (strlen($card) == 18) {
	        $number = substr($card, 16, 1);
	    } else {
	        $number = substr($card, 14, 1);
	    }
	    if ($number % 2 == 0) {
	        return '女';
	    } else {
	        return '男';
	    }
	}

	/**
	 * 获取指定字符后(前)的内容
	 * @param string $str 
	 * @param string $special_str 指定字符
	 * @param string $type 类型，before-前，behind-后 
	 * @param int $mode 模式
	 * 	1-查找字符串在另一字符串中第一次出现的位置（不区分大小写）
	 * 	2-查找字符串在另一字符串中第一次出现的位置（区分大小写）
	 * 	3-查找字符串在另一字符串中最后一次出现的位置（区分大小写）
	 * @param boolean $contain_special_str 是否包含指定字符
	 * @return string  
	 * @author xk
	 */
	public static function getContentBySpecialStr($str,$special_str,$type='before',$mode=1,$contain_special_str=true)
	{
		$location = 0;
		if($mode == 2){
			$location = strpos($str,$special_str);
		}elseif($mode == 3){
			$location = strrpos($str,$special_str);
		}else{
			$location = stripos($str,$special_str);
		}


		if($contain_special_str){
			if($type == 'before'){
				$location += 1;
			}
		}else{
			if($type == 'behind'){
				$location += 1;
			}
		}

		if($type == 'behind'){
			$result = substr($str,$location);
		}else{
			$result = substr($str,0,$location);
		}
	    return  $result;
	}

	/**
	 * 根据地址获取类名和方法
	 * @param string $url     地址
	 * @return array  [类名,方法名]
	 * @author           xk
	 */
	public static function getClassAndAction($url)
	{
	    if(empty($url)){
            throw new \RuntimeException('url不存在');
        }
        if(Str::contains($url,'/')){
            $app = explode('/',trim($url,'/'));
        }else{
            $app = explode('\\',trim($url,'\\'));
        }

		$action = array_pop($app);
		$class = implode('\\',$app);
		return [$class,$action];
	}

}