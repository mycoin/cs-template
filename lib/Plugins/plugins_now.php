<?php //Start
/**
 * Copyright (c) 2011 xatu.edu.cn
 * Support:185390516.qzone.qq.com
 * QQ:185390516
 * Author:LoveCrystal  Version:1.01
 * Date:Dec 2, 2011 5:31:15 PM
 */
! defined('ENGINE_DIR') and exit('Access Denied !');
class PluginsNow extends Taglib
{
    /* (non-PHPdoc)
     * @see Taglib::__toString()
     */
    function __toString()
    {
        return '{php}echo PluginsNow::now();{/php}';
    }
    /**
     * Enter description here ...
     * @return string
     */
    static function now()
    {
        return date('Y-m-d H:i:s');
    }
}
