<?php
//生成奖池
$arr_award = array("first_prize" => 8, "second_prize"=> 16, "third_prize"=> 32, "award_num"=>0 );
	// 并发访问，php文件锁,加锁
	$fp = fopen('./award_pool.json', 'w+'); // php的文件锁和表没关系，随便一个文件即可
	if(flock($fp, LOCK_EX)){    //排他锁
		$json_award_pool = json_encode($arr_award);
		fwrite($fp , $json_award_pool);
		// php的文件锁，释放锁
		flock($fp, LOCK_UN);
	}else{
		echo "获取并发访问文件锁失败";
	}
	//关闭文件
	fclose($fp);