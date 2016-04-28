<?php
namespace Scuec\Controller;

use Think\Controller;

class InfoController extends Controller
{
  public function index()
  {
    $urls = array(u('score'), u('schedule'), u('exam'), u('sociascore'));
    $this->assign('urls', $urls);
    $this->display();
  }

  protected function showapi($data)
  {
//        $this->ajaxreturn($data,__EXT__);
    if ("xml" == __EXT__) {
      $this->ajaxreturn($data, 'xml');
    } else {
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
  }

  public function schedule()
  {
    if (I('get.student', '') != '' && I('get.password', '') != '') {
      $student = I('get.student', 0);
      $password = I('get.password', 0);
      $scuec = new Scuec($student, $password);
      $scuec->setTargeturl("http://ssfw.scuec.edu.cn/ssfw/xkgl/xkjgcx.do");
      $scuec->init();
      $scuec->run();
      $content = $scuec->getContent();
      if ($content["code"] != 200) {
        echo "获取网页错误!请检查配置.";
        exit(0);
      }
      $res = Array();
      preg_match_all('/<!-- 本学期开始 -->(.*?)<!-- 本学期结束 -->/s', $content['content'], $res);
      preg_match_all('/<span>(.*?)<\/span>/s', $res[0][0], $res);
      $classinfo = array(0 => array('学年学期', '课程号', '课程名称', '学时', '学分', '上课时间', '上课地点', '上课教师', '校区', '重修重考'));
      $item = 0;
      foreach ($res[1] as $key => $value) {
        if ($key % 10 == 0) {
          $item++;
        }
        $classinfo[$item][$key % 10] = $value;
      }
      if (isset($_GET['type']) && $_GET['type'] == 1) {
        foreach ($classinfo as $key => $item) {
          if ($key == 0) continue;
          $ch = explode(" ", $item[5]);
          $classinfo[$key][5] = $ch;
        }
      }
      $this->showapi($classinfo);
    } else {
      $this->display();
    }
  }

  public function exam()
  {
    if (I('get.student', '') != '' && I('get.password', '') != '') {
      $student = I('get.student', 0);
      $password = I('get.password', 0);
      $scuec = new Scuec($student, $password);
      $scuec->setTargeturl("http://ssfw.scuec.edu.cn/ssfw/xsks/kcxx.do");
      $scuec->init();
      $scuec->run();
      $content = $scuec->getContent();
      if ($content["code"] != 200) {
        echo "获取网页错误!请检查配置.";
        exit(0);
      }
      $res = Array();
      preg_match_all('/<table.*?>(.*?)<\/table>/s', $content['content'], $res);
      preg_match_all('/<td.*?>(.*?)<\/td>/s', $res[0][1], $res);
      $examinfo = array(0 => array('序号', '课程号', '课程名称', '课程性质', '任课老师', '学分', '座位号', '考试时间', '考试地点', '考试形式', '考试方式', '状态'));
      $item = 0;
      foreach ($res[1] as $key => $value) {
        if ($key % 12 == 0) {
          $item++;
        }
        $examinfo[$item][$key % 12] = preg_replace('/<\/*span.*>/s', '', $value);
      }
      $this->showapi($examinfo);
    } else {
      $this->display();
    }
  }

  public function score()
  {
    if (I('get.student', '') != '' && I('get.password', '') != '') {
      $student = I('get.student', 0);
      $password = I('get.password', 0);
      $scuec = new Scuec($student, $password);
      $scuec->init();
      $scuec->run();
      $string = $scuec->getContent();
      if ($string["code"] != 200) {
        echo "获取网页错误!请检查配置.";
        exit(0);
      } else {
        $string = $string["content"];
      }
//            $score_cache = __APP__.'/Temp/score_cache/'.$student.'.html';
//            if(!file_exists($score_cache) || (filemtime($score_cache)-time())>86400){
//                $file = fopen($score_cache,"w");
//                fwrite($file,$string);
//                fclose($file);
//            }
//            $score[0]['cache'] = dirname('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"])."/".$score_cache;
      preg_match('/<!-- 查询结束 -->(.*?)<!-- 原始成绩 -->/s', $string, $result);
      preg_match_all('/<td align="center" valign="middle">(.*?)<\/td>/s', $result[0], $result);
      $tr = array("序号", "学年学期", "课程号", "课程名称", "课程类别", "课程性质", "学分", "成绩", "修读方式", "备注");
      foreach ($tr as $ti => $tv) {
        $score[0][$ti] = $tv;
      }
      $k = 0;
      $i = 0;
      foreach ($result[1] as $key => $value) {
        if ($key % 10 == 0) {
          $k++;
          $i = 0;
        }
        if ($i == 9) {
          if (!preg_match('/(\w)/s', $value)) {
            $value = '';
          }
        }
        if ($i == 4 || $i == 5 || $i == 8) {
          $value = substr($value, 0, -6); //去掉最后的空格符'&nbsp;'
        }
        if ($i == 7) {
          preg_match_all('/<span><strong>(.*?)<\/strong><\/span>/s', $value, $match);
          if (empty($match[0])) {
            preg_match_all('/<span>(.*?)<\/span>/s', $value, $match);
          }
          $value = $match[1][0];
        }
        $score[$k][$i] = $value;
        $i++;
      }
      $this->showapi($score);
    } else {
      $this->display();
    }
  }

  public function SociaScore()
  {
    if (I('get.student', '') != '' && I('get.password', '') != '') {
      $student = I('get.student', 0);
      $password = I('get.password', 0);
      $scuec = new Scuec($student, $password);
      $scuec->setTargeturl("http://ssfw.scuec.edu.cn/ssfw/ksbm/xsbm.do");
      $scuec->init();
      $scuec->run();
      $string = $scuec->getContent();
      if ($string["code"] != 200) {
        echo "获取网页错误!请检查配置.";
        exit(0);
      } else {
        $string = $string["content"];
      }
      preg_match('/<div id="margintop" style="height: 2px;"><\/div>(.*?)<!-- 表格结束 -->/s', $string, $result);
      preg_match_all('/<td align="center" valign="middle">(<span>)?(<strong>)?(?P<need>(.*?))(<\/strong>)?(<\/span>)?<\/td>/s', $result[0], $result);
      $tr = array("学年学期", "考试批次", "考试项目", "准考证号", "总成绩", "报名日期", "考试日期");
      foreach ($tr as $ti => $tv) {
        $score[0][$ti] = $tv;
      }
      $k = 0;
      $i = 0;
      $row = 8;
      foreach ($result['need'] as $key => $value) {
        if (($key + 1) % 8 == 0)
          continue;
        if ($key % $row == 0) {
          $k++;
          $i = 0;
        }
        $score[$k][$i] = $value;
        $i++;
      }
      $this->showapi($score);
    } else {
      $this->display();
    }
  }

  public function _empty()
  {
    $this->error('你是怎么找到我的!');
  }
}