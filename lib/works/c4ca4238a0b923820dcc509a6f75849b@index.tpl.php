<?php if(!defined('ENGINE_DIR') || filemtime('E:\wampRoot\csTemplatev2.1\demo\templates\index.tpl') > 1344071461) return FALSE; ?><?php include_once 'E:\\wampRoot\\csTemplatev2.1\\lib\\Plugins\\plugins_now.php';include_once 'E:\\wampRoot\\csTemplatev2.1\\lib\\Plugins\\plugins_test.php';include_once 'E:\\wampRoot\\csTemplatev2.1\\lib\\Plugins\\func_utruncate.php';?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>csTemplate v1.2</title>
<style> 
body{
    font: 12px/1.5 tahoma, arial, \5b8b\4f53, sans-serif; color:#111; width:830px; margin-left:30px; 
	background: no-repeat fixed url(<?php echo $url['skin']; ?>images/bg.jpg);
}
#Logo{position: absolute;top:30px;left:340px;font: 32px/1.5 arial, sans-serif;color:green}
code{ font-family: courier new, courier, monospace; } 
h3,p,code{margin-left:30px;}
h3{display: block;padding-left: 25px;background: no-repeat 5px url(<?php echo $url['skin']; ?>images/ok.gif)}
.run{background:#E2EDFB; border:solid #AACBF0 1px; padding:10px; font-size:12px}
.footer{text-align:center; color: #666}
.Abs{ padding:5px;position: fixed;line-height:150%; background:#E2EDFB; width:320px; right:10px; border:1px solid #AACBF0; height:auto; font-size:12px}
.Abs a{color:green；margin:0 4px; display: inline-block;}
</style>
</head>
<body>

<div id="Display">
<?php if(isset($_GET['view']) && $_GET['view'] == 1) { ?>
    <?php 
        highlight_file(__FILE__);
        exit;
    ; ?><?php } ?>
</div>
<div id="Logo"><font face="Calibri">cs</font>Template v1.2</div>
<div class="Abs">
<span style="display: block; padding: 5px 10px;">
<h2 style="padding:5px 0px;margin:0;color:red">Welcome !</h2>
<li>php5以上环境，不支持对变量控制缓存与否。</li>
<li>模板引擎支持插件功能!</li>
<li>访问<a href="?cache=1&clean=1">最最差的情况</a>，重新编译并缓存。</li>
<li>访问<a href="?cache=1">开启缓存</a>，插件，赋值不更新。</li>
<li>访问<a href="?">不缓存</a>，$_expired_time = 0正常编译。</li>
<li>查看编译<a href="?view=1" target="_blank">源码</a>，重新编译源码。</li>
<li>有问题或者建议QQ:<a href="tencent://message/?Menu=yes&amp;uin=185390516&amp;Site=&amp;Service=201&amp;sigT=e2427c79b66e6ea8bb5890b6b23c58664d726b25dc0b8f81bb14b16e6e7d3f5e7735bb09ca34b32fb4d1c1cc944373b1&amp;sigU=d73cd2289d1aaa294d5a77289e82a84b3b852f62ba98247239359b482078fddd31f3419ebd026c7e">185390516</a>,欢迎交流！</li>
<li>项目<a style="color:red" href="http://www.relymate.com/index.php?module=project&amp;case=main&amp;id=1">官方网站</a>地址，欢迎指正。</li>
<li>当前时间：<?php echo PluginsNow::now();; ?>，测试缓存！</li>
</span>
</div>
<div id="Code">
<h2>A、基础语法</h2>
<h3>print var</h3>
<code>变量{<span style="color: #0000BB">$var</span>}</code>
<p class="run"><?php echo $site; ?></p>
<code>数组{<span style="color: #0000BB">$sitting.copy</span>}</code>
<p class="run"><?php echo $sitting['copy']; ?></p>
<h3>Config</h3>
<code>导入配置文件名称demo：{<span style="color: #007700">config <span style="color: #DD0000">"demo"</span></span> /}调用数组值，如果未定义则使用默认值{<span style="color: #007700">@:demo.action | "<span style="color: #0000BB">默认配置</span>" /}</span></code>
<p class="run">/index.php?module=admin&case=login&submit=1</p>
<p class="run">默认配置，因为{ @:demo.Wrong }不存在</p>
<h3>template/include</h3>
<code>{<span style="color: #007700">include <span style="color: #DD0000">"inc/filename.htm"</span></span> /}</code>注意：该标签不可引用自身
<p class="run">模板引擎中可以通过<font color="#0000FF">{include “index.tpl” /}</font>格式引用子模板，但是在解析的时候存在循环引用的判断，例如：index.tpl中同样引用了本身自己，在编译的时候会因为循环匹配导致服务器内存耗尽。
解决办法，在用户编写模板文档时，不可以引用循环引用的文件。
</p>
<h3>if/else</h3>
<code>{<span style="color: #007700">if <span style="color: #0000BB">1</span><span style="color: #007700">+</span><span style="color: #0000BB">1</span><span style="color: #007700">==</span><span style="color: #0000BB">2</span></span>}1+1==2{<span style="color: #007700">else</span>}oh god{/<span style="color: #007700">if</span>}</code>
<p class="run"><?php if(1+1==2) { ?>1+1==2<?php } else { ?>oh god<?php } ?></p>
<h3>loop/loopelse</h3>
<code>{<span style="color: #007700">loop <span style="color: #0000BB">$_GET $key $value</span></span>}Key=【{<span style="color: #0000BB">$key</span>}】，Value=【{<span style="color: #0000BB">$value</span>}】；{<span style="color: #007700">loopelse</span>}变量是空的{/<span style="color: #007700">loop</span>}</code>
<p class="run"><?php $vars = $_GET; if(is_array($vars) && count($vars)>0){ foreach($_GET as $key => $value) { ?>Key=【<?php echo $key; ?>】，Value=【<?php echo $value; ?>】；<?php } } else {?>变量是空的<a href="?id=1&title=admin">访问连接试试</a><?php }unset($vars); ?></p>
<h3>php</h3>
<code>{<span style="color: #0000BB">php</span>}<span style="color: #007700">echo&nbsp;</span><span style="color: #DD0000">__FILE__</span><span style="color: #007700">;</span>{/<span style="color: #0000BB">php</span></span>}或者{<span style="color: #0000BB">php </span><span style="color: #007700">echo&nbsp;</span><span style="color: #DD0000">__FILE__</span><span style="color: #007700">;</span></span>}</code>
<p class="run"><?php echo __FILE__;; ?></p>
<h3>Tag</h3>
<code>{<span style="color: #0000BB">tag_</span><span style="color: #DD0000">友情连接</span>}</code>该标签文件保存在Taglib目录下，在$_taglib_dirs变量数组中遍历
<p class="run"><a href="http://www.mbsky.com/" target="_blank" title="">模板天下</a>
<a href="http://www.xinnong.com" target="_blank" title="">新农网</a>
<a href="http://down.admin5.com" target="_blank" title="">网站源码</a>
<a href="http://www.php100.com" target="_blank" title="">PHP100中文网</a>
<a href="http://club.domain.cn" target="_blank" title="">域名城</a>
<a href="http://www.webjx.com/" target="_blank" title="">网页教学网</a>
<a href="http://bbs.dedecms.com/" target="_blank" title="">Dede官方论坛</a>
<a href="http://www.chaxun.la/" target="_blank" title="">站长工具</a>
<a href="http://www.php100.com" target="_blank" title="">PHP100中文网</a>
<a href="http://club.domain.cn" target="_blank" title="">域名城</a>
<a href="http://www.webjx.com/" target="_blank" title="">网页教学网</a>
<a href="http://bbs.cnzz.com/" target="_blank" title="">CNZZ论坛</a></p>
<h3>注释</h3>
<code>{<span style="color: #FF8000">*卑鄙是卑鄙者的通行证，高尚是高尚者的墓志铭*</span>}</code>
<p class="run"></p>
<h2>B、扩展</h2>
<h3>修改器</h3>
<code>{<span style="color: #0000BB">$_GET</span><span style="color: #007700">|</span><span style="color: #0000BB">var_dump</span>}</code>
<p class="run"><?php echo var_dump($_GET) ;?></p>
<h3>数组修改器</h3>
<code>{<span style="color: #0000BB">$_SERVER.SERVER_SOFTWARE</span><span style="color: #007700">|</span><span style="color: #0000BB">var_dump</span>}</code>
<p class="run"><?php echo var_dump($_SERVER['SERVER_SOFTWARE']) ;?></p>
<h3>自定义修改器</h3>
<code>{<span style="color: #0000BB">php&nbsp;</span><span style="color: #0000BB">$title</span><span style="color: #007700">=</span><span style="color: #DD0000">'感恩西安工业大学，给予我梦想的舞台!'</span><span style="color: #007700">;</span>}{<span style="color: #0000BB">$title</span><span style="color: #007700">|</span><span style="color: #0000BB">utruncate:<span style="color: #DD0000">4</span>:<span style="color: #DD0000">'……'</span></span>}</code>函数utruncate在Plugins目录下，请参考例子。
<p class="run"><?php $title='感恩西安工业大学，给予我梦想的舞台!';; ?> <?php echo func_utruncate($title,12, '……') ;?></p>
<h3>自定义标签</h3>
<code>{<span style="color: #0000BB">cs:test</span>&nbsp;<span style="color: #0000BB">id</span>=<span style="color: #DD0000">"1"</span>&nbsp;<span style="color: #0000BB">title</span><span style="color: #007700">=</span><span style="color: #DD0000">"支持属性"</span>}以及内容{/<span style="color: #0000BB">cs:test</span>}</code>
<p class="run">参数：<?php $params = array (
  'id' => '1',
  'title' => '支持属性',
); echo var_dump($params) ;?>内容：以及内容</p>
<code>{<span style="color: #0000BB">cs:now</span>&nbsp;/}</code>
<p class="run"><?php echo PluginsNow::now();; ?></p>
<code>{<span style="color: #0000BB">php </span><span style="color: #007700">print_r&nbsp;</span><span style="color: #DD0000">(get_included_files()</span><span style="color: #007700">;</span></span>}</code>
<p class="run"><?php print_r(get_included_files());; ?></p>

<h2>C、At Last</h2>
<p>笔者php初学者，希望多多指教。由于技术有限，参考了众多的模板引擎，最后重新编写了这款消费内存非常少的模板引擎。</p>

<h2>D、鸣谢</h2>
<p>感恩西安工业大学，给予我梦想的舞台。<br />感谢所有支持我的童鞋，最后感谢百度爱好者，XingTemplate团队，本程序参考自XingTemplate模板引擎。</p>  
</div>
</body>  
</html>
