<?php //Start
/**
 * Copyright (c) 2011 xatu.edu.cn
 * Support:185390516.qzone.qq.com
 * QQ:185390516
 * Author:LoveCrystal  Version:1.01
 * Date:Dec 3, 2011 3:24:22 PM
 */
!defined('ENGINE_DIR') and exit('Access Denied !');
class csException extends Exception
{
	//TODO - Insert your code here
	/**
	 * Enter description here ...
	 * @return boolean
	 */
	function is_ajax()
	{
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) return true;
		if (!empty($_POST['AJAX_SUBMIT']) || !empty($_GET['AJAX_SUBMIT'])) return true;
		return false;
	}
	public function __construct($message, $code = __LINE__)
	{
		@error_reporting(E_ERROR);
		$this->code = $code;
		$array = array();
		$this->message = empty($message) ? '' : $message;
		$dedail = "";
		$fatal = array();
		foreach ($this->getTrace() as $row)
		{
			$fatals = '<font color=red><b></b></font>' . @$row['class'] . @$row['type'] . @$row['function'] . '(' . implode(', ', @$row['args']) . ')';
			$fatals .= " in File " . @$row['file'] . '(line:' . @$row['line'] . ')';
			$fatal[] = $fatals;
		}
		array_pop($fatal);
		$from = '<br><font color=red>Thrown in</font><b> ' . $fatal[count($fatal) - 1] . '</b>';
		$dedail = implode('<br><b>Exception</b>: ', $fatal) . $from;
		$m = (memory_get_usage() - @$GLOBALS['m']) / 1024;
		$html = "<DIV style=\"MARGIN:10PX;font-family:微软雅黑;line-height:22px;font-size: 12px; background: #FFFFFF;\">";
		$script = "<script>function s(){var to = document.getElementById('Com').style.display =='none' ? '' : 'none';document.getElementById('Com').style.display = to;}</script>";
		$array[] = "<b>Error</b>: " . $this->message;
		$array[] = "<b>Error Code</b>: " . $this->code;
		$array[] = "<b>Memory:</b>: " . $m . 'KB';
		$array[] = "<b>File Path</b>: " . $this->file;
		$array[] = "<b>Line</b>: " . $this->line;
		$array[] = "<b>Time</b>: " . date('Y-m-d H:i:s');
		$array[] = "<b>Please</b>: " . "Content the Webmaster or <a href='/'> back</a>.";
		$array[] = "<b>Detail</b>: <a href=\"javascript:s();\">Click Here</a>";
		$array[] = "<div id='Com' style='display:none;padding:0 50px'>" . $dedail . "</div>";
		echo $this->is_ajax() ? 'Exception' : $script . $html . implode('<br>', $array) . '</div>';
	}
}