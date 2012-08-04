<?php //Start
/**
 * Copyright (c) 2011 xatu.edu.cn
 * Support:185390516.qzone.qq.com
 * QQ:185390516
 * Author:LoveCrystal  Version:1.01
 * Date:Dec 2, 2011 5:31:15 PM
 */
! defined('ENGINE_DIR') and exit('Access Denied !');
class PluginsTest extends Taglib
{
    function __toString()
    {
        $str = '参数：{php $params = ' . var_export($this->params, 1) . '}';
        //返回的结果也可以是模板
        $str .= '{$params|var_dump}';
        $str .= '内容：' . $this->contents;
        return $str;
    }
}