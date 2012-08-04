<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>csTemplate v1.2</title>
<style> 
body{
    font: 12px/1.5 tahoma, arial, \5b8b\4f53, sans-serif; color:#111; width:830px; margin-left:30px; 
	background: no-repeat fixed url({$url.skin}images/bg.jpg);
}
#Logo{position: absolute;top:30px;left:340px;font: 32px/1.5 arial, sans-serif;color:green}
code{ font-family: courier new, courier, monospace; } 
h3,p,code{margin-left:30px;}
h3{display: block;padding-left: 25px;background: no-repeat 5px url({$url.skin}images/ok.gif)}
.run{background:#E2EDFB; border:solid #AACBF0 1px; padding:10px; font-size:12px}
.footer{text-align:center; color: #666}
.Abs{ padding:5px;position: fixed;line-height:150%; background:#E2EDFB; width:320px; right:10px; border:1px solid #AACBF0; height:auto; font-size:12px}
.Abs a{color:green；margin:0 4px; display: inline-block;}
</style>
</head>
<body>

<div id="Display">
{if isset($_GET['view']) && $_GET['view'] == 1}
    {php}
        highlight_file(__FILE__);
        exit;
    {/php}
{/if}
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
<li>有问题或者建议QQ:<a href="tencent://message/?Menu=yes&uin=185390516&Site=&Service=201&sigT=e2427c79b66e6ea8bb5890b6b23c58664d726b25dc0b8f81bb14b16e6e7d3f5e7735bb09ca34b32fb4d1c1cc944373b1&sigU=d73cd2289d1aaa294d5a77289e82a84b3b852f62ba98247239359b482078fddd31f3419ebd026c7e">185390516</a>,欢迎交流！</li>
<li>项目<a style="color:red" href="http://www.relymate.com/index.php?module=project&case=main&id=1">官方网站</a>地址，欢迎指正。</li>
<li>当前时间：{cs:now/}，测试缓存！</li>
</span>
</div>
<div id="Code">
<h2>A、基础语法</h2>
<h3>print var</h3>
<code>变量{<span style="color: #0000BB">$var</span>}</code>
<p class="run">{$site}</p>
<code>数组{<span style="color: #0000BB">$sitting.copy</span>}</code>
<p class="run">{$sitting.copy}</p>
<h3>Config</h3>
<code>导入配置文件名称demo：{<span style="color: #007700">config <span style="color: #DD0000">"demo"</span></span> /}调用数组值，如果未定义则使用默认值{<span style="color: #007700">@:demo.action | "<span style="color: #0000BB">默认配置</span>" /}</span></code>
<p class="run">{config "demo" /}{@:demo.action | "默认配置" /}</p>
<p class="run">{config "demo" /}{@:demo.Wrong | "默认配置，因为{ @:demo.Wrong }不存在" /}</p>
<h3>template/include</h3>
<code>{<span style="color: #007700">include <span style="color: #DD0000">"inc/filename.htm"</span></span> /}</code>注意：该标签不可引用自身
<p class="run">{include "head.tpl"}</p>
<h3>if/else</h3>
<code>{<span style="color: #007700">if <span style="color: #0000BB">1</span><span style="color: #007700">+</span><span style="color: #0000BB">1</span><span style="color: #007700">==</span><span style="color: #0000BB">2</span></span>}1+1==2{<span style="color: #007700">else</span>}oh god{/<span style="color: #007700">if</span>}</code>
<p class="run">{if 1+1==2}1+1==2{else}oh god{/if}</p>
<h3>loop/loopelse</h3>
<code>{<span style="color: #007700">loop <span style="color: #0000BB">$_GET $key $value</span></span>}Key=【{<span style="color: #0000BB">$key</span>}】，Value=【{<span style="color: #0000BB">$value</span>}】；{<span style="color: #007700">loopelse</span>}变量是空的{/<span style="color: #007700">loop</span>}</code>
<p class="run">{loop $_GET $key $value}Key=【{$key}】，Value=【{$value}】；{loopelse}变量是空的<a href="?id=1&title=admin">访问连接试试</a>{/loop}</p>
<h3>php</h3>
<code>{<span style="color: #0000BB">php</span>}<span style="color: #007700">echo&nbsp;</span><span style="color: #DD0000">__FILE__</span><span style="color: #007700">;</span>{/<span style="color: #0000BB">php</span></span>}或者{<span style="color: #0000BB">php </span><span style="color: #007700">echo&nbsp;</span><span style="color: #DD0000">__FILE__</span><span style="color: #007700">;</span></span>}</code>
<p class="run">{php echo __FILE__;}</p>
<h3>Tag</h3>
<code>{<span style="color: #0000BB">tag_</span><span style="color: #DD0000">友情连接</span>}</code>该标签文件保存在Taglib目录下，在$_taglib_dirs变量数组中遍历
<p class="run">{tag_友情连接}</p>
<h3>注释</h3>
<code>{<span style="color: #FF8000">*卑鄙是卑鄙者的通行证，高尚是高尚者的墓志铭*</span>}</code>
<p class="run">{*卑鄙是卑鄙者的通行证，高尚是高尚者的墓志铭*}</p>
<h2>B、扩展</h2>
<h3>修改器</h3>
<code>{<span style="color: #0000BB">$_GET</span><span style="color: #007700">|</span><span style="color: #0000BB">var_dump</span>}</code>
<p class="run">{$_GET|var_dump}</p>
<h3>数组修改器</h3>
<code>{<span style="color: #0000BB">$_SERVER.SERVER_SOFTWARE</span><span style="color: #007700">|</span><span style="color: #0000BB">var_dump</span>}</code>
<p class="run">{$_SERVER.SERVER_SOFTWARE|var_dump}</p>
<h3>自定义修改器</h3>
<code>{<span style="color: #0000BB">php&nbsp;</span><span style="color: #0000BB">$title</span><span style="color: #007700">=</span><span style="color: #DD0000">'感恩西安工业大学，给予我梦想的舞台!'</span><span style="color: #007700">;</span>}{<span style="color: #0000BB">$title</span><span style="color: #007700">|</span><span style="color: #0000BB">utruncate:<span style="color: #DD0000">4</span>:<span style="color: #DD0000">'……'</span></span>}</code>函数utruncate在Plugins目录下，请参考例子。
<p class="run">{php $title='感恩西安工业大学，给予我梦想的舞台!';} {$title|utruncate:12:'……'}</p>
<h3>自定义标签</h3>
<code>{<span style="color: #0000BB">cs:test</span>&nbsp;<span style="color: #0000BB">id</span>=<span style="color: #DD0000">"1"</span>&nbsp;<span style="color: #0000BB">title</span><span style="color: #007700">=</span><span style="color: #DD0000">"支持属性"</span>}以及内容{/<span style="color: #0000BB">cs:test</span>}</code>
<p class="run">{cs:test id="1" title="支持属性"}以及内容{/cs:test}</p>
<code>{<span style="color: #0000BB">cs:now</span>&nbsp;/}</code>
<p class="run">{cs:now/}</p>
<code>{<span style="color: #0000BB">php </span><span style="color: #007700">print_r&nbsp;</span><span style="color: #DD0000">(get_included_files()</span><span style="color: #007700">;</span></span>}</code>
<p class="run">{php print_r(get_included_files());}</p>

<h2>C、At Last</h2>
<p>笔者php初学者，希望多多指教。由于技术有限，参考了众多的模板引擎，最后重新编写了这款消费内存非常少的模板引擎。</p>

<h2>D、鸣谢</h2>
<p>感恩西安工业大学，给予我梦想的舞台。<br />感谢所有支持我的童鞋，最后感谢百度爱好者，XingTemplate团队，本程序参考自XingTemplate模板引擎。</p>  
</div>
</body>  
</html>
