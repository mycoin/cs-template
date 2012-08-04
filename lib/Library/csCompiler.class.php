<?php //Start
/**
 * Copyright (c) 2011 xatu.edu.cn
 * Support:185390516.qzone.qq.com
 * QQ:185390516
 * Author:LoveCrystal  Version:1.01
 * Date:Dec 29, 2011 3:24:22 AM
 */
! defined('ENGINE_DIR') and exit('Access Denied !');
class csCompiler
{
	/**
	 * Enter description here ...
	 * @param plugin $pluginname
	 */
	public static function init($content)
	{
		self::explainConfig('default');
		self::$_content = $content;
	}
	/**
	 * Enter description here ...
	 * @param html $content
	 */
	public static function getFlushedCode()
	{
		$tmp = '<?php ';
		foreach ( self::$_plugins as $file )
		{
			if (file_exists($file))
			{
				$tmp .= 'include_once ' . var_export($file, 1) . ';';
			}
		}
		return $tmp . '?>';
	}
	/**
	 * Enter description here ...
	 * @param var $var
	 * @param modify $modify
	 * @return string
	 */
	public static function getPlugins($file)
	{
		$plug_file = csTemplate::$_plugins_dir . $file;
		if (! file_exists($plug_file))
		{
			throw new csException('Plugins ' . $plug_file . ' not found in class Compiler ...', __LINE__);
		}
		if (! in_array($plug_file, self::$_plugins))
		{
			array_push(self::$_plugins, $plug_file);
			return include_once $plug_file;
		}
	}
	/**
	 * Enter description here ...
	 * @param unknown_type $content
	 * @throws csException
	 * @return Ambigous <unknown, unknown_type, mixed>
	 */
	public static function explain($content = false)
	{
		$content = $content ? $content : self::$_content;
		foreach ( self::$_pattern as $pattern => $replacement )
		{
			if (! is_array($replacement))
			{
				$content = self::explainPattern($pattern, $replacement, $content);
				continue;
			}
			for($i = 0; $i < $pattern; $i ++)
			{
				foreach ( $replacement as $reg => $replace )
				{
					$content = self::explainPattern($reg, $replace, $content);
				}
			}
		}
		return $content;
	}
	/**
	 * Enter description here ...
	 * @param taglib $source
	 * @return string
	 */
	public static function explainImport($sourceName)
	{
		$path = csTemplate::$_taglib_dir;
		if (PHP_OS == 'Linux')
		{
			$filename = realpath($path) . DS . $sourceName . '.html';
		} else
		{
			$filename = iconv('UTF-8', 'GB2312', realpath($path) . DS . $sourceName . '.html');
		}
		if (file_exists($filename))
		{
			return file_get_contents($filename);
		}
		throw new csException('Taglib html file ' . $sourceName . '.html not found ...', __LINE__);
	}
	/**
	 * Enter description here ...
	 * @param key $key
	 * @param value $value
	 * @param tplcal $source
	 * @throws csException
	 * @return mixed
	 */
	public static function explainPattern($key, $value, $source)
	{
		$source = preg_replace($key, $value, $source);
		if (is_null($source))
		{
			throw new csException(preg_last_error(), __LINE__);
		}
		return $source;
	}
	/**
	 * Enter description here ...
	 * @param unknown_type $content
	 * @return string
	 */
	public static function explainArray($content)
	{
		$content = explode('.', $content);
		$arr = '';
		foreach ( $content as $item )
		{
			$arr .= '[\'' . $item . '\']';
		}
		return $arr;
	}
	/**
	 * Enter description here ...
	 * @param unknown_type $fileName
	 */
	public static function explainInclude($fileName)
	{
		$path = csTemplate::$_template_dir;
		if (PHP_OS == 'Linux')
		{
			$filename = realpath($path) . DS . $fileName;
		} else
		{
			$filename = iconv('UTF-8', 'GB2312', realpath($path) . DS . $fileName);
		}
		if (is_readable($filename))
		{
			touch($filename); //File time verified ...
			return file_get_contents($filename);
		}
		throw new csException('Include html file ' . $fileName . '.html not readable ...', __LINE__);
	}
	/**
	 * Enter description here ...
	 * @param unknown_type $var
	 * @param unknown_type $modify
	 * @return string
	 */
	public static function explainModify($var, $modify)
	{
		foreach ( explode('|', $modify) as $func )
		{
			$params = explode(':', $func);
			$function_name = array_shift($params);
			if (! function_exists($function_name))
			{
				$function_name = 'func_' . $function_name;
				self::getPlugins($function_name . '.php');
			}
			if (strpos($func, '@me') === false)
			{
				$param_string = empty($params) ? '' : ',' . implode(", ", $params);
				$var = $function_name . '(' . $var . $param_string . ')';
			} else
			{
				$var = $function_name . '(' . str_replace('@me', $var, implode(",", $params)) . ')';
			}
		}
		return '<?php echo ' . $var . ' ;?>';
	}
	/**
	 * Enter description here ...
	 * @param taglib $tag_name
	 * @param params $params
	 * @param template $content
	 * @param bool $mutitag
	 * @throws csException
	 */
	public static function explainTags($tag_name, $params = '', $content = '', $mutitag = false)
	{
		$ext_content = $mutitag ? stripslashes(self::expainMutiTags($tag_name, $content)) : '';
		$tmp = array();
		$class_name = $tag_name;
		if (preg_match_all("/\s*([a-zA-Z0-9_]+)=([\"'])(.+?)\\2/", stripslashes($params), $matches))
		{
			for($i = 0; $i < count($matches [1]); $i ++)
			{
				$tmp [$matches [1] [$i]] = $matches [2] [$i] . $matches [3] [$i] . $matches [2] [$i];
			}
		}
		$content = stripslashes($content);
		if (! class_exists($class_name, false))
		{
			self::getPlugins('plugins_' . $tag_name . '.php');
			$class_name = 'Plugins' . ucfirst($tag_name);
		}
		if (! class_exists($class_name, false))
		{
			throw new csException('Sorry taglib ' . $tag_name . ' could not be loaded. ');
		} else
		{
			return self::explain(strval(new $class_name($tmp, $content)) . $ext_content);
		}
	}
	/**
	 * Enter description here ...
	 * @param taglib $tag_name
	 * @param content $content
	 * @param unknown_type $endpos
	 * @throws csException
	 * @return string|NULL
	 */
	public static function expainMutiTags($tag_name, &$content, $endpos = 0)
	{
		$start_tag = '{cs:' . $tag_name;
		$end_tag = '{/cs:' . $tag_name . '}';
		$tag_length = strlen($end_tag);
		$start = $end = array();
		$pos = 0;
		while ( $pos !== false )
		{
			$pos = stripos($content, $start_tag, $pos);
			$start [] = $pos;
			if ($pos !== false) $pos ++;
		}
		$pos = 0;
		while ( $pos !== false )
		{
			$pos = stripos($content, $end_tag, $pos);
			$end [] = $pos;
			if ($pos !== false) $pos ++;
		}
		if (count($start) !== count($end))
		{
			throw new csException('Your mititag is not closed. ', __LINE__);
		}
		$i = 0;
		while ( $start [$i] !== false )
		{
			if ($start [$i] > $end [$i])
			{
				$ext_content = substr($content, $end [$i] + $tag_length) . $end_tag;
				$content = substr($content, 0, $end [$i]);
				return $ext_content;
				break;
			}
			$i ++;
		}
		return null;
	}
	/**
	 * Enter description here ...
	 * @param SpecialChars $str
	 * @return mixed
	 */
	public static function explainSpecialChars($str)
	{
		$str = str_replace('&', '&amp;', $str);
		$str = str_replace('&amp;amp;', '&amp;', $str);
		$str = str_replace('\"', '"', $str);
		return $str;
	}
	/**
	 * Enter description here ...
	 * @param unknown_type $param
	 */
	public static function explainConfig($configName)
	{
		$config_file = csTemplate::$_configure_dir . $configName . '.inc.php';
		if (! file_exists($config_file))
		{
			throw new csException('Configure ' . $config_file . ' not found in class Compiler ...', __LINE__);
		}
		$config = include $config_file;
		if (is_array($config))
		{
			self::$_vars = array_merge(self::$_vars, $config);
		} else
		{
			throw new csException("Config config_{$configName} does not Obtain a Return value ...", __LINE__);
		}
	}
	/**
	 * Enter description here ...
	 * @param explainValues $str
	 * @return mixed
	 */
	public static function explainValues($key1, $key2 = null, $key3 = null)
	{
		$return = false;
		if ($key3 && isset(self::$_vars [$key1] [$key2] [$key3]))
		{
			$return = self::$_vars [$key1] [$key2] [$key3];
		} elseif ($key2 && isset(self::$_vars [$key1] [$key2]))
		{
			$return = self::$_vars [$key1] [$key2];
		} elseif ($key1 && isset(self::$_vars [$key1]))
		{
			$return = self::$_vars [$key1];
		}
		return is_string($return) ? $return : false;
	}
	/**
	 * Enter description here ...
	 * @param unknown_type $mudule
	 * @param unknown_type $case
	 * @param unknown_type $param
	 * @return string
	 */
	public static function explainUrl($mudule, $case, $param = null)
	{
		$params = array();
		if ($param)
		{
			$array = explode('.', $param);
			$key = Application::$_key;
			foreach ( $array as $value )
			{
				$key = strpos($value, '=') ? explode('=', $value) : array($key [2], $value);
				$params = array_merge($params, array($key [0] => $key [1]));
			}
			unset($key);
		}
		if (! class_exists('Application', false)) return null;
		return Application::Url($mudule, $case, $params);
	}
	/**
	 * Enter description here ...
	 * @var RegexPattern
	 */
	private static $_vars = array();
	/**
	 * Enter description here ...
	 * @var RegexPattern
	 */
	private static $_pattern = array(//Array...
"/\{~(\w+)~\}/" => "", // description
"/[\n\r\t]*\{template\s+([\"'\s]*)([a-z0-9\-_:\/.]+)\\1[,\s]*(\w+)*[\s\/]*\}/is" => "<?php \$this->display('\\2', '\\3'); ?>", "/[\n\r\t]*\{config\s+([\"'\s]*)([a-z0-9\-_:\/.]+)\\1[\s\/]*\}/ies" => "self::explainConfig('\\2');", //
"/\{\*(.+?)\*\}/s" => '', "/\{~(\w+)~\}/" => "", // description
"/[\n\r\t]*\{include\s+([\"'\s]*)([a-z0-9\-_:\/.]+)\\1[\s\/]*\}/ies" => "self::explainInclude('\\2');", "/\{url:(\w+)\.(\w+)\.*([^\}|^\/]+)*([\s\/]*)\}/ies" => "self::explainUrl('\\1', '\\2', '\\3');", //
"/[\n\r\t]*\{tag_([^\}\']+)\}/ies" => "self::explainImport(trim('\\1'));", //Config...
'/[\n\r\t]*\{cs:([a-zA-Z0-9_]+)\s*(.*?)\/\}/ime' => "self::explainTags('\\1','\\2');", //Tags
'/[\n\r\t]*\{cs:([a-zA-Z0-9_]+)([^}.]*)\}(.*)\{\/cs:\\1\}/imes' => "self::explainTags('\\1','\\2','\\3',true);", //tag surpput....
"/ \?\>[\n\r]*\<\?php /s" => " ", "/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/e" => "self::explainSpecialChars('\\0')", //url SpecialChars...
"/\{(\\\$[a-zA-Z0-9_\[\]\'\"\-\>\$\x7f-\xff]+)+\.([a-zA-Z0-9_\[\]\'\"\.\$\x7f-\xff]+)\}/ies" => "'<?php echo \\1'.self::explainArray('\\2').'; ?>'", //Array
"/\{(\\\$[a-zA-Z0-9_\[\]\'\"\-\>\$\x7f-\xff]+)+\.([a-zA-Z0-9_\[\]\'\"\.\$\x7f-\xff]+)\|(.+?)\}/ies" => "self::explainModify('\\1'.self::explainArray('\\2'),'\\3')", //var...
"/\{(\\\$[a-zA-Z0-9_\[\]\'\"\\-\>$\.\x7f-\xff]+)\}/s" => "<?php echo \\1; ?>", //just echo ...
"/\{(\\\$[a-zA-Z0-9_\[\]\'\"\-\>\$\.\x7f-\xff]+)\|([^{]+?)\}/ies" => "self::explainModify('\\1','\\2')", //Modify...
"/[\n\r\t]*\{php\}(.+?)\{\/php\}[\n\r\t]*/is" => "<?php \\1; ?>", //php ...
"/[\n\r\t]*\{php\s+(.+?)\}[\n\r\t]*/is" => "<?php \\1; ?>",

	/*condetion....*/
	1 => array("/\{@:(\w+)\.(\w+)\.(\w+)\s*\|\s*([\"\'])([^\'^\"]+)\\4([\s\/]*)\}/ies" => "is_string(self::explainValues('\\1', '\\2', '\\3')) ? self::explainValues('\\1', '\\2', '\\3') : trim('\\5');", //Values... 
"/\{@:(\w+)\.(\w+)\s*\|\s*([\"\'])([^\'^\"]+)\\3([\s\/]*)\}/ies" => "is_string(self::explainValues('\\1', '\\2')) ? self::explainValues('\\1', '\\2') : trim('\\4');", //Values...
"/\{@:(\w+)\s*\|\s*([\"\'])([^\'^\"]+)\\2([\s\/]*)\}/ies" => "is_string(self::explainValues('\\1')) ? self::explainValues('\\1') : trim('\\3');"), //Values...
"21" => array("/\{@:(\w+)\.(\w+)\.(\w+)([\s\/]*)\}/ies" => "self::explainValues('\\1', '\\2', '\\3');", //Values...
"/\{@:(\w+)\.(\w+)([\s\/]*)\}/ies" => "self::explainValues('\\1', '\\2');", //Values...
"/\{@:(\w+)([\s\/]*)\}/ies" => "self::explainValues('\\1');"), //Values...
/*condetion....*/
	
	/*config explain....*/
	2 => array('/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/is' => '\\1<?php } elseif(\\2) { ?>\\3', //elseif
"/([\n\r\t]*)\{else\}([\n\r\t]*)/is" => "\\1<?php } else { ?>\\2", //else
"/([\n\r\t]*)\{if\s+(.+?)\}([\n\r]*)(.+?)([\n\r]*)\{\/if\}([\n\r\t]*)/is" => "\\1<?php if(\\2) { ?>\\3\\4\\5<?php } ?>\\6"), //if
/*config explain....*/
	
	/*loop explain....*/
	3 => array(//loopelse的支持
"/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\S]*(.+?)[\n\r\t]*\{loopelse\}[\n\r]*(.+?)[\n\r]*\{\/loop\}[\n\r\t]*/is" => "<?php \$vars = \\1; if(is_array(\$vars) && count(\$vars)>0){ foreach(\$vars as \\2) { ?>\\3<?php } } else { ?>\\4<?php } unset(\$vars); ?>", //foreach( $source as $item)
"/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{\/loop\}[\n\r\t]*/is" => "<?php \$vars = \\1; if(is_array(\$vars) && count(\$vars)>0){ foreach(\$vars as \\2) { ?>\\3<?php } } unset(\$vars); ?>", //foreach($source as $key=>$value)
"/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{loopelse\}[\n\r\t]*(.+?)[\n\r\t]*\{\/loop\}[\n\r\t]*/is" => "<?php \$vars = \\1; if(is_array(\$vars) && count(\$vars)>0){ foreach(\\1 as \\2 => \\3) { ?>\\4<?php } } else {?>\\5<?php }unset(\$vars); ?>", //foreach($source as $key=>$value)
"/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{\/loop\}[\n\r\t]*/is" => "<?php \$vars = \\1; if(is_array(\$vars)){ foreach(\\1 as \\2 => \\3) { ?>\\4<?php } } unset(\$vars); ?>")); //noelse ....
	/*loop explain....*/
	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private static $_content = null;
	private static $_plugins = array();
}
