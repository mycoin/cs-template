<?php //Start
/**
 * Copyright (c) 2011 xatu.edu.cn
 * Support:185390516.qzone.qq.com
 * QQ:185390516
 * Author:LoveCrystal  Version:1.01
 * Date:Dec 2, 2011 3:16:52 PM
 */
/*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
$m = memory_get_usage();
$t = microtime(TRUE) * 1000;
function memory($exit = false)
{
	$unit = array('Byte', 'KB', 'MB', 'GB', 'TB', 'PB');
	$html = "<div onclick=\"this.style.display='none';\" style='display:block;position:fixed;z-index:9999; bottom:12px;right:12px;padding:10px; font-size:12px; font-weight:bold; border:1px solid #CCC; width:auto'>内存：<font color=red>{MEMORY}</font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;时间：<font color=red>{TIME}</font>ms</div>";
	$size = (memory_get_usage() - $GLOBALS['m']);
	$time = number_format(microtime(TRUE) * 1000 - $GLOBALS['t'], 4);
	$write = number_format(@round($size / pow(1024, ($i = floor(log((memory_get_usage(1) - $GLOBALS['m']), 1024)))), 2), 
	3) . ' ' . $unit[$i];
	$html = str_replace('{MEMORY}', $write, $html);
	echo str_replace('{TIME}', $time, $html);
	$exit && exit($exit);
}
/*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
header('Content-Type: text/html; charset=utf-8');
define('WORKSPACE', realpath('') . DIRECTORY_SEPARATOR);
define('ENGINE_TEMPLATE', realpath('') . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR);

include_once '../lib/csTemplate.php';
$o = new csTemplate();

$o->assign('site', '欢迎使用csTemplate模板引擎');
if (isset($_GET['clean']))
{
	$o->clean(true, '*');
}
if (isset($_GET['cache']))
{
	$o->_expired_time = 10;
}
$o->assign('url', array('skin' => "./" . str_ireplace(array(WORKSPACE, DS), array(null, '/'),csTemplate::$_template_dir)));
$o->assign('sitting', array('copy' => 'Designed By Carrey Lau'));
$o->display('index.tpl', 1);

memory();