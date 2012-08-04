<?php //Start
/**
 * Copyright (c) 2011 xatu.edu.cn
 * Support:185390516.qzone.qq.com
 * QQ:185390516
 * Author:LoveCrystal  Version:1.01
 * Date:Dec 2, 2011 7:59:47 PM
 */
!defined('ENGINE_DIR') and exit('Access Denied !');
abstract class Taglib
{
	protected $params;
	protected $rawparams;
	protected $content;
	function __construct($rawparams, $contents)
	{
		$this->rawparams = $rawparams;
		foreach ($rawparams as & $value)
		{
			$quote = substr($value, 0, 1);
			if (($quote == '"' || $quote == "'") && $quote == substr($value, -1))
			{
				$value = substr($value, 1, -1);
			}
		}
		$this->params = $rawparams;
		$this->contents = $contents;
	}
	/**
	 * Enter description here ...
	 * @param unknown_type $rawparams
	 * @return unknown
	 */
	public abstract function __toString();
}
//End
 ?>