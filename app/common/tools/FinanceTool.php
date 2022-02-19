<?php

namespace app\common\lib;

/**
 * 金融计算相关工具类
 */
class FinanceTool
{
	/**
	 * 等额本息
	 * @param int|float $M 贷款金额(元)
	 * @param int $Y 贷款期数(单位月)
	 * @param int $R 年利率(单位%)
	 * @param int $P 计算精度
	 * @return array 包含还款信息的数组 [月供,总利息,总利息+本金,每月月供]
	 * @author 艾伟
	 */
	public static function calculateDEBX($M, $Y, $R, $P = 2)
	{

	    $results = array();
	    $list = array();
	    $C = 0;
	    $B = 0;
	    //剩余本金
	    $D = 0;
	    $I = 0;
	    $A = 0;
	    $totalInt = 0;
	    $totalAmt = 0;
	    //月利率
	    $R = $R / 100 / 12;
	    //贷款期数
	    $N = $Y;
	    $C = $M * $R * pow(1 + $R, $N) / (pow(1 + $R, $N) - 1);
	    $totalAmt = $C * $N;
	    for ($i = 0; $i < $N; $i++) {
	        // I = 剩余本金 × 月利率
	        $D = $i == 0 ? $M : $list[$i - 1][3]; //剩余本金
	        $I = $D * $R;
	        $totalInt += $I;
	        //$list.push( [ $I.toFixed(2), ($C-$I).toFixed(2), C.toFixed(2), (D-( C-I )).toFixed(2)] );
	        $list[$i][] = round($I, $P);
	        $list[$i][] = round($C - $I, $P);
	        $list[$i][] = round($C, $P);
	        if ($i == $Y - 1) {
	            $list[$i][] = 0;
	        } else {
	            $list[$i][] = round($D - ($C - $I), $P);
	        }

	    };
	    //results.push(C.toFixed(2),totalInt.toFixed(2),(totalInt+M).toFixed(2),list)
	    $results[] = round($C, $P);
	    $results[] = round($totalInt, $P);
	    $results[] = round($totalInt + $M, $P);
	    $results[] = $list;

	    return $results;
	}



	/**
	 * 先息后本
	 * @param int|float $M 贷款金额(元)
	 * @param int $Y 贷款期数(单位月)
	 * @param int $R 年利率(单位%)
	 * @param int $P 计算精度
	 * @return array [月供,总利息,总利息+本金,每月月供]
	 * @author 艾伟
	 */
	public static function calculateXXHB($M, $Y, $R, $P = 2)
	{
	    $results = array();
	    $list = array();
	    $R = $R / 100 / 12;
	    $month_interest = $M * $R;

	    $results[] = round($month_interest, $P);
	    $results[] = round($month_interest * $Y, $P);
	    $results[] = round($month_interest * $Y + $M, $P);

	    for ($i = 0; $i < $Y; $i++) {
	        if ($i < ($Y - 1)) {
	            $list[$i][] = round($month_interest, $P);
	            $list[$i][] = 0;
	            $list[$i][] = round($month_interest, $P) + 0;
	            $list[$i][] = $M;
	        } else {
	            $list[$i][] = round($month_interest, $P);
	            $list[$i][] = $M;
	            $list[$i][] = round($month_interest, $P) + $M;
	            $list[$i][] = 0;
	        }
	    }
	    $results[] = $list;
	    return $results;
	}

	/**
	 * 等本等息
	 * @param int|float $M 贷款金额(元)
	 * @param int $Y 贷款期数(单位月)
	 * @param int $R 年利率(单位%)
	 * @param int $P 计算精度
	 * @return array [月供,总利息,总利息+本金,每月月供]
	 * @author 艾伟
	 */
	public static function calculateDBDX($M, $Y, $R, $P = 2)
	{
		$results = array();
	    $list = array();
		$R = $R / 100 / 12;
		$total_interest = $M * $R * $Y;
		$month_pay = ($M + $total_interest) / $Y;

		$results[] = round($month_pay, $P);
	    $results[] = round($total_interest, $P);
		$results[] = round($total_interest + $M, $P);
		
		for ($i = 0; $i < $Y; $i++) {
	        $list[$i][] = round($month_pay, $P);
		}
		$results[] = $list;
	    return $results;
	}
}