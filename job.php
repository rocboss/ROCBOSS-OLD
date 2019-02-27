<?php
/**
 * Batio Jobs Entrance
 */
require __DIR__."/bootstrap/init.php";

app()->set('isJob', true);

Batio::bootstrap();

while (true) {
    foreach (app()->get('jobs') as $job) {
        $runStatus = timeMark($job['runTime']);

        $name = $job['name'];
        $controller = $job['controller'];
        $action = $job['action'];

        $lockTime = $job['maxTime'] + time();

        if ($runStatus
            && (
                !empty(app()->redis()->setnx('jobs:'.$name, $lockTime))
                || (
                    app()->redis()->get('jobs:'.$name) < time()
                    && app()->redis()->getSet('jobs:'.$name, $lockTime) < time()
                )
            )) {

            app()->redis()->expire('jobs:'.$name, $job['maxTime']);

            $ret = $controller::$action();

            app()->log()->info('RunJob-'.$name, [
                'timestamp' => time(),
                'result' => $ret,
            ]);

            // 检测锁是否过期，过期锁没必要删除
            if (app()->redis()->ttl('jobs:'.$name)) {
                app()->redis()->del('jobs:'.$name);
            }
        }
    }
    sleep(1);
}

/**
 * timeMark
 *
 * @param string $match
 * @return boolean
 */
function timeMark($match)
{
    $s = date('s');//秒
    $i = date('i');//分
    $h = date('H');//时
    $d = date('d');//日
    $m = date('m');//月
    $w = date('w');//周
    $runTime = explode(' ', $match);
    $data[] = T($runTime[0], $s, 's');
    $data[] = T($runTime[1], $i, 'i');
    $data[] = T($runTime[2], $h, 'h');
    $data[] = T($runTime[3], $d, 'd');
    $data[] = T($runTime[4], $m, 'm');
    $data[] = T($runTime[5], $w, 'w');
    return !in_array(false, $data) ? true : false;
}

/**
 * T
 *
 * @param string $rule
 * @param string $time
 * @param string $timeType
 * @return boolean
 */
function T($rule, $time, $timeType)
{
    if (is_numeric($rule)) {
        return $rule == $time ? true : false;
    } elseif (strstr($rule, ',')) {
        $iArr = explode(',', $rule);

        return in_array($time, $iArr) ? true : false;
    } elseif (strstr($rule, '/') && !strstr($rule, '-')) {
        list($left, $right) = explode('/', $rule);

        return in_array($left, array('*', 0)) && analysisTime($time, $right) ? true : false;
    } elseif (strstr($rule, '/') && strstr($rule, '-')) {
        list($left, $right) = explode('/', $rule);

        if (strstr($left, '-')) {
            return analysis($left, $right, $time, $timeType);
        }
    } elseif (strstr($rule, '-')) {
        list($left, $right) = explode('-', $rule);

        return $time >= $left && $time <= $right ? true : false;
    } elseif ($rule == '*' || $rule == 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * analysis
 *
 * @param string $rank 
 * @param integer $num 
 * @param string $time 
 * @param string $timeType 
 * @return boolean
 */
function analysis($rank, $num, $time, $timeType)
{
    $type = array(
        'i' => 59, 'h' => 23, 'd' => 31, 'm' => 12, 'w' => 6,
    );
    list($left, $right) = explode('-', $rank);
    if ($left < $right) {
        for ($i = $left; $i <= $right; $i = $i + $num) {
            $temp[] = $i;
        }
    }
    if ($left > $right) {
        for ($i = $left; $i <= $type[$timeType] + $right; $i = $i + $num) {
            $temp[] = $i > $type[$timeType] ? $i - $type[$timeType] : $i;
        }
    }
    return in_array($time, $temp) ? true : false;
}

/**
 * analysisTime
 *
 * @param string $time
 * @param string $num
 * @return boolean
 */
function analysisTime($time, $num)
{
    return $time % $num == 0 ? true : false;
}