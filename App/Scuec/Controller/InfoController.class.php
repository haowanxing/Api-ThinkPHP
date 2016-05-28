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
        $student = I('post.student', '');
        $password = I('post.password', '');
        if ($student != '' && $password != '') {
            $scuec = new Scuec($student, $password);
            $scuec->setTargeturl("http://ssfw.scuec.edu.cn/ssfw/xkgl/xkjgcx.do");
            $scuec->init();
            $scuec->run();
            $content = $scuec->getContent();
            if ($content["code"] != 200) {
                $this->showapi(array('code' => 400, 'result' => ''));
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
                $classinfo[$item][$key % 10] = str_replace('<br/>', ' ', $value);
            }
            //如果type=1,则分开上课时间字符串
            if (isset($_POST['type']) && $_POST['type'] == 1) {
                foreach ($classinfo as $key => $item) {
                    if ($key == 0) continue;
                    $ch = explode(" ", $item[5]);
                    $classinfo[$key][5] = $ch;
                }
            }
            $this->showapi(array('code' => 200, 'result' => $classinfo));
        } else {
            $this->display();
        }
    }

    public function exam()
    {
        $student = I('post.student', '');
        $password = I('post.password', '');
        if ($student != '' && $password != '') {
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
            preg_match_all('/<td.*?>(.*?)<\/td>/s', $res[0][1], $res1);
            preg_match_all('/<td.*?>(.*?)<\/td>/s', $res[0][2], $res2);
            preg_match_all('/<td.*?>(.*?)<\/td>/s', $res[0][3], $res3);
            $examArranged = array(0 => array('序号', '课程号', '课程名称', '课程性质', '任课老师', '学分', '座位号', '考试时间', '考试地点', '考试形式', '考试方式', '状态'));
            $item = 0;
            foreach ($res1[1] as $key => $value) {
                if ($key % 12 == 0) {
                    $item++;
                }
                $examArranged[$item][$key % 12] = preg_replace('/<\/*span.*>/s', '', $value);
            }

            $examArranging = array(0 => array('序号', '课程号', '课程名称', '课程性质', '任课老师', '学分', '考试时间地点'));
            $item = 0;
            foreach ($res2[1] as $key => $value) {
                if ($key % 7 == 0) {
                    $item++;
                }
                $examArranging[$item][$key % 7] = preg_replace('/<\/*span.*>/s', '', $value);
            }

            $examUnArranged = array(0 => array('序号', '课程号', '课程名称', '学分', '考试时间地点'));
            $item = 0;
            foreach ($res3[1] as $key => $value) {
                if ($key % 5 == 0) {
                    $item++;
                }
                $examUnArranged[$item][$key % 5] = preg_replace('/<\/*span.*>/s', '', $value);
            }
            $examInfo = array('arranged' => $examArranged, 'arranging' => $examArranging, 'unarranged' => $examUnArranged);
            $this->ajaxReturn(array('code' => 200, 'result' => $examInfo),'json');
        } else {
            $this->display();
        }
    }

    public function score()
    {
        $student = I('post.student', '');
        $password = I('post.password', '');
        if ($student != '' && $password != '') {
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
            $this->showapi(array('code' => 200, 'result' => $score));
        } else {
            $this->display();
        }
    }

    public function SociaScore()
    {
        $student = I('post.student', '');
        $password = I('post.password', '');
        if ($student != '' && $password != '') {
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
            $this->showapi(array('code' => 200, 'result' => $score));
        } else {
            $this->display();
        }
    }

    public function physics()
    {
        $student = I('post.student', '');
        $password = I('post.password', '');
        if ($student != '' && $password != '') {
            $url = 'http://labsystem.scuec.edu.cn/login.php';
            $targeturl = 'http://labsystem.scuec.edu.cn/labcoursearrange2_student.php';
            $data = array('myid' => $student, 'mypasswd' => $password, 'mytype' => 'student');
            import("ORG.Curl.Curl");
            $curl = new \Org\Curl\Curl();
            $cookieFile = './App/Runtime/Temp/cookie-' . $student . '.temp';
            $curl->setCookieJar($cookieFile);
            $curl->setCookieFile($cookieFile);
            $curl->post($url, $data);
            $content = iconv('GB2312', 'UTF-8//IGNORE', $curl->get($targeturl, array('labcourse' => 'DXXY-387')));
            if (strpos($content, "您无权访问此页面") !== false) {
                $this->ajaxReturn(array('code' => 400, 'msg' => '信息有误,或权限不足,请检查核对后重试'));
            }
            if (file_exists($cookieFile)) {
                @unlink($cookieFile);
            }
            $pattern = '/<tr bgcolor=.*?>(.*?)<\/tr>/s';
            preg_match_all($pattern, $content, $matchlist);
            $matchlist = $matchlist[1];
            $resultArr = array();
            $pattern = '/<td.*?>(.*?)<\/td>/s';
            foreach ($matchlist as $k => $v) {
                if (strpos($v, '&nbsp;') == false) {
                    preg_match_all($pattern, $v, $mch);
                    foreach ($mch[1] as $mk => $mv) {
                        if (strpos($mv, "必做") !== false || strpos($mv, "选做") !== false || strpos($mv, '<input') !== false) {
                            unset($mch[1][$mk]);
                        }
                    }
                    $mch[1] = array_values($mch[1]);
                    $resultArr[] = $mch[1];
                }
            }
//            $this->showapi($resultArr);
            $this->ajaxReturn(array('code' => 200, 'result' => $resultArr), 'json');
        } else {
            $this->display();
        }
    }

    public function _empty()
    {
        $this->error('你是怎么找到我的!');
    }
}