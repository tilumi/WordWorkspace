<?php
class Weekly{

    function getWeekDay1( $date='', $timestamp=false ){
        if( is_numeric($date) && $date>0 ){
            $ts=$date;
        }elseif( preg_match('/^\d{4}-\d{2}$/', $date) ){
            $week=Weekly::getWeekRegion($date, $timestamp);
            return substr($week['begin'], 0, 10);
        }else{
            if( empty($date) ){ $ts=mktime(); }else{ $ts=strtotime($date); }
        }
        $wday=date('w', $ts);
        $theday=date('Y-m-d', $ts);
        if( !$timestamp ){
            return date('Y-m-d', strtotime($theday)-$wday*24*60*60 );
        }
        return strtotime($theday)-$wday*24*60*60;
    }
    function getYearSunday1( $year='', $timestamp=false ){
        //取得一年的第一個禮拜天
        if( empty($year) ){ $year=date('Y'); }
        $wday1=date('w', strtotime($year.'-01-01'));
        $ts_sunday1=strtotime($year.'-01-01 00:00:00');
        if( $wday1!=0 ){ $ts_sunday1=strtotime($year.'-01-01 00:00:00')+(7-$wday1)*24*60*60; }
        if( !$timestamp ){
            return date('Y-m-d', $ts_sunday1);
        }else{
            return $ts_sunday1;
        }
    }
    function getYearWeek( $date='' ){
        if( is_numeric($date) && $date>0 ){
            $ts=$date;
        }elseif( empty($date) ){
            $ts=mktime();
        }else{
            $ts=strtotime($date);
        }
        //取得本周第一天的ts
        $ts_day1=self::getWeekDay1($ts, true);
        //計算相對於本周第一天的，今年第一個星期天的ts
        $theyear=date('Y', $ts_day1);
        //計算年度的第一個周日
        $ts_sunday1=self::getYearSunday1($theyear, true);
        //用兩次周日的秒差計算週數
        $week=( $ts_day1-$ts_sunday1 )/(7*24*60*60)+1;
        return $theyear.'-'.sprintf('%02d', $week);
    }
    function getWeek( $date='' ){ //與getYearWeek完全相同，只是僅回傳週數
        $yearweek=self::getYearWeek( $date );
        $_ = explode('-', $yearweek);
        $week=(int)$_[1];
        return $week;
    }
    function getWeekRegion( $yearweek='', $timestamp=false ){
        //取得指定週的時間範圍
        if( empty($yearweek) ){ $yearweek=self::getYearWeek(); }
        list($year, $week)=explode('-', $yearweek);
        $ts_sunday1 = self::getYearSunday1($year, true);
        $ts_wday1 = $ts_sunday1 + ($week-1)*7*24*60*60;
        $ts_wday6 = $ts_wday1 + (7*24*60*60-1);
        if( $timestamp ){
            return array('begin'=>$ts_wday1, 'end'=>$ts_wday6 );
        }
        return array('begin'=>date('Y-m-d H:i:s', $ts_wday1), 'end'=>date('Y-m-d H:i:s', $ts_wday6) );
    }
}
?>