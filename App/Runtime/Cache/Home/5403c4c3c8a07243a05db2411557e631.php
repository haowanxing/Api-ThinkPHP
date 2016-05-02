<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>新浪短链接生成与还原 - FanTwo</title>
  <link rel="stylesheet" type="text/css" href="/PhpstormProjects/thinkproject/Public/css/bootstrap.min.css" />
  <meta name="description" content="t.cn短链接复原和长连接缩短成短链接t.cn"/>
  <meta name="keywords" content="短链接,长链接,新浪链接,API,Fantwo"/>
  <!--[if lt IE 9]>
  <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
<nav class="navbar navbar-default">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
            data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="http://api.fantwo.com">Fantwo</a>
  </div>
  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav">
      <li class=""><a href="http://www.fantwo.com">博客 <span class="sr-only">(current)</span></a></li>
      <li><a href="http://www.fantwo.com/static/sqrpay.html">赞助</a></li>
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
           aria-expanded="false">常用 <span class="caret"></span></a>
        <ul class="dropdown-menu" style="width: 470px;">
          <li>
            <table id="nav-menu" class="table table-responsive">
              <tr>
                <td colspan="4" class="disabled">民大学子</td>
              </tr>
              <tr>
                <td><a style="display:block;" href="<?php echo u('Scuec/Info/score'); ?>">成绩</a></td>
                <td><a style="display:block;" href="<?php echo u('Scuec/Info/schedule'); ?>">课表</a></td>
                <td><a style="display:block;" href="<?php echo u('Scuec/Info/exam'); ?>">考试安排</a></td>
                <td><a style="display:block;" href="<?php echo u('Scuec/Info/sociascore'); ?>">社考成绩</a></td>
              </tr>
              <!--<li role="separator" class="divider"></li>-->
              <tr>
                <td colspan="4" class="disabled">便民工具</td>
              </tr>
              <tr>
                <td><a style="display:block;" href="<?php echo u('Home/index/weather'); ?>">天气预报</a></td>
                <td><a style="display:block;" href="<?php echo u('Home/index/translate'); ?>">中英互译</a></td>
                <td><a style="display:block;" href="<?php echo u('Home/index/qrcode'); ?>">二维码生成</a></td>
                <td><a style="display:block;" href="<?php echo u('Home/index/iplocation'); ?>">IP归属地查询</a></td>
              </tr>
              <tr>
                <td><a style="display:block;" href="<?php echo u('Home/index/short_url'); ?>">新浪短链接</a></td>
                <td><a style="display:block;" href="#"></a></td>
                <td><a style="display:block;" href="#"></a></td>
                <td><a style="display:block;" href="#"></a></td>
              </tr>
            </table>
          </li>
        </ul>
      </li>
    </ul>
    <form class="navbar-form navbar-left" role="search">
      <div class="form-group">
        <input type="text" id="bdcsMain" class="form-control" placeholder="Search">
      </div>
      <!--<button type="button" class="btn btn-default" onclick="javascript:alert('功能暂未提供')">Submit</button>-->
      <button type="submit" class="btn btn-default">Submit</button>
    </form>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="http://www.fantwo.com/start-page.html">关于</a></li>
      <li><a href="#" style="max-height:52px">
        <wb:share-button appkey="3613047781" addition="simple" type="button" ralateUid="2214092525"
                         pic="http%3A%2F%2Fww2.sinaimg.cn%2Fsquare%2F5f4b9a60gw1f14iroe62rj2028028jr8.jpg"></wb:share-button>
        </a>
      </li>
    </ul>
  </div>
</nav>
<div class="container">
  <h3>新浪短链接生成与还原API</h3>
  <div class="row">
    <form method="post" action="" name="shorturl_form">
      <input type="hidden" name="type" id="hidden_type" value="1"/>
      <div class="col-md-5">
        <div class="form-group">
          <label for="turl">您准备处理的网址:</label>
          <input name="url" id="turl" class="form-control" type="text" placeholder="http://www.fantwo.com"/><br>
        </div>
        <button type="button" onclick="do_post(1)" class="btn btn-primary">生成</button>
        <button type="button" onclick="do_post(2)" class="btn btn-success">还原</button>
      </div>
    </form>
    <div class="col-md-offset-0 col-md-2"></div>
    <div class="col-md-5">
      <div class="panel panel-default">
        <div class="panel-heading">查询结果:<?php echo ($tips); ?></div>
        <div class="panel-body">
          <p>短链接:<a target="_blank" href="<?php echo ($result['url_short']); ?>"><?php echo ($result['url_short']); ?></a></p>
          <p>原始网址:<a target="_blank" href="<?php echo ($result['url_long']); ?>"><?php echo ($result['url_long']); ?></a></p>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">功能介绍</h3>
      </div>
      <div class="panel-body">
        <ol>
          <li>原网址与短链接相互转换,返回json数据</li>
          <li>type=1:原->短 | type=2:短->原</li>
          <li>API调用方式:<span>
            <?php echo "http://".I('server.SERVER_NAME').explode('?',I('server.REQUEST_URI'))[0]; ?>?qq=你的QQ号&type=1&url=链接</span>
            <small>(仅支持post请求)</small>
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="/PhpstormProjects/thinkproject/Public/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="/PhpstormProjects/thinkproject/Public/js/bootstrap.min.js"></script>
<script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">(function(){document.write(unescape('%3Cdiv id="bdcs"%3E%3C/div%3E'));var bdcs = document.createElement('script');bdcs.type = 'text/javascript';bdcs.async = true;bdcs.src = 'http://znsv.baidu.com/customer_search/api/js?sid=681985830315117204' + '&plate_url=' + encodeURIComponent(window.location.href) + '&t=' + Math.ceil(new Date()/3600000);var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(bdcs, s);})();</script>
<script type="text/javascript">
  function do_post(d) {
    document.getElementById('hidden_type').value = d;
    document.shorturl_form.submit();
  }
</script>
</body>
</html>