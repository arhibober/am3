<?php defined('_JEXEC') or die; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<jdoc:include type="head" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
</head>
<body>
<div class="cont">
  <div id="all">
    <div id="container">
      <div id="header">
		<div class="link">
			<a href="http://club.apostrof.in.ua" target="_blank"></a>
		</div>
        <jdoc:include type="modules" name="header" style="xhtml" />
      </div>
      <div id="wrapper">
        <div id="top">
          <jdoc:include type="modules" name="top" style="xhtml" />
          <div style="clear:both"></div>
        </div>
        <div id="content">
          <jdoc:include type="message" />
          <jdoc:include type="component" />
        </div>
      </div>
      <div id="footer">
        <jdoc:include type="modules" name="bottom" style="xhtml" />
      </div>
    </div>
  </div>
</div><div class="foot">
  <div class="ft">
  <jdoc:include type="modules" name="footer" style="xhtml" />
  </div><br/><br/>
  <table width="100%">
  <tr>
<td>
    <div id="red">

    <p style="text-align: center;" lang="en-US"><span style="font-size: x-small;"><a href="http://lux-d.com.ua" target="_blank"><span style="font-size: x-small;">© </span>дизайн розроблено в Lux Design</a><a href="http://lux-d.com.ua" target="_blank">, 2011</a></span></p>
<p style="text-align: center;"><span style="font-size: x-small;"><a href="http://studiosdl.com" target="_blank"><span style="font-size: x-small;">© </span>сайт створено в DL Studio</a><a href="http://studiosdl.com" target="_blank">, 2011</a></span></p>
</div>
	  


	</td>
	</tr>
	<tr>
	<td>
	<div style="text-align: center; margin: 5px 0 5px 0;">
	  <!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='http://www.liveinternet.ru/click' "+
"target=_blank><img src='//counter.yadro.ru/hit?t11.7;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet: показане число переглядів за 24"+
" години, відвідувачів за 24 години й за сьогодні' "+
"border='0' width='88' height='31'><\/a>")
//--></script><!--/LiveInternet-->
<<!-- I.UA counter --><a href="http://www.i.ua/" target="_blank" onclick="this.href='http://i.ua/r.php?174196';" title="Rated by I.UA">
<script type="text/javascript" language="javascript"><!--
iS='<img src="'+(window.location.protocol=='https:'?'https':'http')+
'://r.i.ua/s?u174196&p2&n'+Math.random();
iD=document;if(!iD.cookie)iD.cookie="b=b; path=/";if(iD.cookie)iS+='&c1';
iS+='&d'+(screen.colorDepth?screen.colorDepth:screen.pixelDepth)
+"&w"+screen.width+'&h'+screen.height;
iT=iR=iD.referrer.replace(iP=/^[a-z]*:\/\//,'');iH=window.location.href.replace(iP,'');
((iI=iT.indexOf('/'))!=-1)?(iT=iT.substring(0,iI)):(iI=iT.length);
if(iT!=iH.substring(0,iI))iS+='&f'+escape(iR);
iS+='&r'+escape(iH);
iD.write(iS+'" border="0" width="88" height="31" />');
//--></script></a><!-- End of I.UA counter -->
</div>
	</td>
	</tr>
	</table>	  
  </div>
</div>
</body>
</html>