<?php
/**
* PHP crontab 
* 使用方法:在config.ini中配置要执行的计划任务
*          在php-cli执行run.php
* @author Devil
**/	
while(true){
		$config = parse_ini_file('config.ini',true);
		foreach($config as $cronName=>$info){
            if(array_key_exists('log_dir',$info) && !empty($info['log_dir'])){
                $outputCommon = ' 1>>'.$info['log_dir'].' 2>&1 &';
            }else{
                $outputCommon = '';
            }
			$runStatus = timeMark($info['run_time']);
			if($runStatus){
                $memory = convert(memory_get_usage(true));
				echo '['.date('Y-m-d H:i:s').'] Task:['.$cronName."]->Is Runing Mem:".$memory."\r\n";
				pclose( popen('cd '.$info['cd_dir'].'&'.$info['common'].$outputCommon,'r'));
				//pclose($handle);
			}else{
				echo '['.date('Y-m-d H:i:s').']'."Waiting for a task\r\n";
			}
		}
	sleep(1);
}

	
/**
*解析时间计划任务规则
*/
//$match = '*/3 18 * * *';
//$res = timeMark($match);	
function timeMark($match){
	$s = date('s');//秒
	$i = date('i');//分
	$h = date('H');//时
	$d = date('d');//日
	$m = date('m');//月
	$w = date('w');//周
	$run_time = explode(' ',$match);
	$data[] = T($run_time[0],$s,'s');
	$data[] = T($run_time[1],$i,'i');
	$data[] = T($run_time[2],$h,'h');
	$data[] = T($run_time[3],$d,'d');
	$data[] = T($run_time[4],$m,'m');
	$data[] = T($run_time[5],$w,'w');
	return !in_array(false,$data)?true:false;
}

//解析单个时间规则细节
function T($rule,$time,$timeType){
	if(is_numeric($rule)){
		return $rule == $time ?true:false;
	}elseif(strstr($rule,',')){
		$iArr = explode(',',$rule);
		return in_array($time,$iArr)?true:false;
	}elseif(strstr($rule,'/') && !strstr($rule,'-')){
		list($left,$right) = explode('/',$rule);
		return in_array($left,array('*',0)) && analysis_t($time,$right)?true:false;
	}elseif(strstr($rule,'/') && strstr($rule,'-')){
		list($left,$right) = explode('/',$rule);
		if(strstr($left,'-')){
			return analysis($left,$right,$time,$timeType);
		}
	}elseif(strstr($rule,'-')){
		list($left,$right) = explode('-',$rule);
		return $time >= $left && $time <=$right?true:false;
	}elseif($rule =='*' || $rule==0){
		return true;
	}else{
		return false;
	}
}
//解析12-2 23-22 任何时间的通用
//$rank范围  $num阀值 $time当前时间 $timeType时间类型
function analysis($rank,$num,$time,$timeType){
	$type = array(
		'i'=>59,'h'=>23,'d'=>31,'m'=>12,'w'=>6,
	);
	list($left,$right) = explode('-',$rank);
	if($left<$right){
		for($i=$left;$i<=$right;$i=$i+$num){
			$temp[] = $i;
		}
	}
	if($left > $right){
		for($i=$left;$i<=$type[$timeType]+$right;$i=$i+$num){
			$temp[] = $i>$type[$timeType]?$i-$type[$timeType]:$i;
		}
	}
	return in_array($time,$temp)?true:false;
}
//根据当前时间计算是否定时循环执行
//$time当前时间 $num周期值 
function analysis_t($time,$num){
	return $time%$num == 0?true:false;
}

//$path 日志路径 $body 日志信息
function writeLog($path,$body){
	if(!empty($path) && !empty($body)){
	    $temp = pathinfo($path);
	    if(!file_exists($temp['dirname'])){
		  mkdir($temp['dirname'],0755,true);
	    }
	    file_put_contents($path,'['.date('Y-m-d H:i:s')."]\r\n".$body."\r\n\n",FILE_APPEND);
	}
}
//字节换算
function convert($size)
{
        $unit=array('b','kb','mb','gb','tb','pb');
            return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

