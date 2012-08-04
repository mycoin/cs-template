<?php
/**
 * Copyright (c) 2011 xatu.edu.cn
 * Support:185390516.qzone.qq.com
 * QQ:185390516
 * Author:LoveCrystal  Version:1.01
 * Date:Dec 2, 2011 3:46:08 PM
 */
! defined('WORKSPACE') and exit('Access Denied !');
/**
 * define shorthand directory separator constant because of the different OS
 */
! defined('DS') && define('DS', DIRECTORY_SEPARATOR, false);
/**
 * Enter description here ...
 * @var define engine directory
 */
! defined('ENGINE_DIR') && define('ENGINE_DIR', dirname(__FILE__) . DS, false);
/**
 * Enter description here ...
 * @var define libraries directory
 */
! defined('ENGINE_LIB') && define('ENGINE_LIB', ENGINE_DIR . 'Library' . DS, false);
/**
 * Enter description here ...
 * @var define system work directory, instore temps and compiled files
 */
! defined('ENGINE_TAG') && define('ENGINE_TAG', ENGINE_DIR . 'Taglib' . DS, false);
/**
 * Enter description here ...
 * @var define system templates
 */
! defined('ENGINE_CONFIG') && define('ENGINE_CONFIG', ENGINE_DIR . 'Configure' . DS, false);
/**
 * Enter description here ...
 * @var define system templates
 */
! defined('ENGINE_DATA') && define('ENGINE_DATA', ENGINE_DIR . 'works' . DS, false);
/**
 * Enter description here ...
 * @var define system templates
 */
! defined('ENGINE_PLUG') && define('ENGINE_PLUG', ENGINE_DIR . 'Plugins' . DS, false);
/**
 * Enter description here ...
 * @var define system templates
 */
! defined('ENGINE_TEMPLATE') && define('ENGINE_TEMPLATE', ENGINE_DIR . 'templates' . DS, false);
/**
 * Enter description here ...
 * @param class $param
 */
if (! function_exists('setPath'))
{
	/**
	 * Enter description here ...
	 * @param path $class
	 * @throws Error
	 */
	function setPath($mixed)
	{
		$args = func_get_args();
		$action = array_pop($args);
		if (false === $action)
		{
			$new = implode(PATH_SEPARATOR, array_diff(explode(PATH_SEPARATOR, get_include_path()), $args));
		} elseif (true === $action)
		{
			$new = implode(PATH_SEPARATOR, $args) . PATH_SEPARATOR . get_include_path();
		} else
		{
			throw new Error('The last Arguments should be Booean;', __LINE__);
		}
		set_include_path($new);
	}
}
/**
 * Enter description here ...
 * @param class $param
 */
if (! function_exists('__autoload'))
{
	function __autoload($class)
	{
		$filename = $class . '.class.php';
		if (include_once $filename)
		{
			return true;
		}
		throw new csException('Fatal error: Class ' . $class . ' not found ...', __LINE__);
	}
	spl_autoload_register('__autoload');
}
/**
 * Enter description here ...
 * @author LoveCrystal
 */
class csTemplate
{
	/**
	 * Enter description here ...
	 * @param className $fuction
	 */
	public function __construct()
	{
		setPath(dirname(__FILE__) . DS . 'Library' . DS, true);
	}
	/**
	 * Enter description here ...
	 * @param template $template
	 * @param id $cache_id
	 * @param cid $compile_id
	 */
	private function compile($template)
	{
		$cache_path = self::$_runtime_dir . md5($this->_serialize_id) . '@' . str_ireplace(array('/', DS, '\\\\'), '.', $template) . '.tmp';
		if ($this->_expired_time > 0 && file_exists($cache_path))
		{
			/* read the cache file */
			if (false === include ($cache_path))
			{
				if (false === unlink($cache_path))
				{
					throw new csException('Permission Denied When Delete Expired Cache ' . $cache_path, __LINE__);
				}
				/* rebuild the template and write cache .*/
				return $this->compile($template);
			}
			/* Get all htmls that Caches included and return it.*/
			return ob_get_clean();
		}
		$compile_path = self::$_runtime_dir . md5($this->_serialize_id) . '@' . str_ireplace(array('/', DS, '\\\\'), '.', $template) . '.php';
		if (! file_exists($compile_path))
		{
			/* need to compile and rebuild the Compile file*/
			$template_file = self::$_template_dir . $template;
			if (! file_exists($template_file))
			{
				throw new csException("Template {$template_file} could not be loaded . ", __LINE__);
			}
			if (filemtime($template_file) > time())
			{
				touch($template_file); //File time verified ...
			}
			$flushed_code = '<?php if(!defined(\'ENGINE_DIR\') || filemtime(\'' . $template_file . '\') > ' . $_SERVER ['REQUEST_TIME'] . ') return FALSE; ?>';
			csCompiler::init(file_get_contents($template_file)); //Init the explainer module...
			$comiple_code = csCompiler::explain();
			$flushed_code .= csCompiler::getFlushedCode();
			if (file_put_contents($compile_path, $flushed_code . $comiple_code) === false)
			{
				/*Permission Denied .*/
				throw new csException('Permission Denied When Write html Code to ' . $compile_path, __LINE__);
			}
			chmod($compile_path, 0775);
		}
		/* Import variables from an array into the current symbol table.*/
		extract($this->_vars);
		ob_start();
		if (false === include ($compile_path))
		{
			/*Expired compiled File*/
			if (false === unlink($compile_path))
			{
				/*Permission Denied .*/
				throw new csException('Permission denied when delete expired file ' . $compile_path, __LINE__);
			}
			/* rebuild the template and write cache .*/
			return $this->compile($template);
		}
		$html = ob_get_clean();
		if ($this->_expired_time && $template === $this->_main)
		{
			/*Allow to cache*/
			$_flushed_code = "<?php if(!defined('ENGINE_DIR') || \$_SERVER['REQUEST_TIME'] > " . ($_SERVER ['REQUEST_TIME'] + $this->_expired_time) . ") return FALSE; ?>";
			if (file_put_contents($cache_path, $_flushed_code . $html) === false)
			{
				/*Permission Denied .*/
				throw new csException('Permission denied when write data to ' . $cache_path, __LINE__);
			}
			chmod($cache_path, 0775);
		}
		return $html;
	}
	/**
	 * Enter description here ...
	 * @param key $key
	 * @param value $value
	 * @return Template
	 */
	function assign($key, $value = false)
	{
		if (is_array($key))
		{
			$this->_vars = array_merge($this->_vars, $key);
		} else
		{
			$this->_vars [$key] = $value;
		}
		return $this;
	}
	/**
	 * displays a template
	 *
	 * @param template cache id to be used with this template
	 * @param id compile id to be used with this template
	 * @param cid $parent next higher level of template variables
	 */
	public function display($template, $_serialize_id = __LINE__)
	{
		$this->_serialize_id = $_serialize_id; //Reset the serialize id...
		$this->_main = $this->_main === null ? $template : $this->_main;
		//ob_implicit_flush();
		echo $this->compile($template); // display template
	}
	/**
	 * Enter description here ...
	 * @param id $id
	 * @param ext $ext
	 */
	public function clean($_serialize_id, $ext = 'tmp')
	{
		$cache_path = $_serialize_id === true ? '' : md5($_serialize_id) . '@';
		foreach ( glob(self::$_runtime_dir . $cache_path . "*.$ext") as $filename )
		{
			if (file_exists($filename) && preg_match("/[^\w]*[a-f0-9]{32}@\S+/", $filename))
			{
				chmod($filename, 0775);
				unlink($filename);
			}
		}
	}
	/**
	 * Enter description here ...
	 */
	public function __destruct()
	{
		setPath(dirname(__FILE__) . DS . 'Library' . DS, false);
	}
	/**
	 * Enter description here ...
	 * @var object
	 */
	private $_vars = array();
	/**
	 * Enter description here ...
	 * @var Directory
	 */
	private $_main = null;
	/**
	 * Enter description here ...
	 * @var Directory
	 */
	private $_serialize_id = __LINE__;
	/**
	 * Enter description here ...
	 * @var time
	 */
	public $_expired_time = false;
	/**
	 * Enter description here ...
	 * @var Directory
	 */
	public static $_configure_dir = ENGINE_CONFIG;
	/**
	 * Enter description here ...
	 * @var Directory
	 */
	public static $_plugins_dir = ENGINE_PLUG;
	/**
	 * Enter description here ...
	 * @var Directory
	 */
	public static $_runtime_dir = ENGINE_DATA;
	/**
	 * Enter description here ...
	 * @var Directory
	 */
	public static $_taglib_dir = ENGINE_TAG;
	/**
	 * Enter description here ...
	 * @var Directory
	 */
	public static $_template_dir = ENGINE_TEMPLATE;
}