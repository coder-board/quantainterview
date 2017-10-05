<?php
/**
 * 微信公众平台 PHP SDK 示例文件
 *
 * @author NetPuter <netputer@gmail.com>
 */

  require('src/Wechat.php');

  require_once('func/conn.php');
  /**
   * 微信公众平台演示类
   */
  class MyWechat extends Wechat {

    /**
     * 用户关注时触发，回复「欢迎关注」
     *
     * @return void
     */
    protected function onSubscribe() {
        $words="欢迎关注quanta微信公众号，本公众号在招新期间仅开启后台做面试结果查询，如造成不便，深表歉意\n";
        $words.="尝试回复您的招新编码+姓名获取面试结果吧，如“C503+张框塔”";
      $this->responseText($words);
    }

    /**
     * 用户取消关注时触发 
     *
     * @return void
     */
    protected function onUnsubscribe() {
      // 「悄悄的我走了，正如我悄悄的来；我挥一挥衣袖，不带走一片云彩。」
    }

    /**
     * 收到文本消息时触发，回复收到的文本消息内容
     *
     * @return void
     */
    protected function onText() {
    	$keyword=$this->getRequest('content');
        if ($info = $this->explode_add($keyword)){
            $returnCon=$this->secondResult($info);
        }else{
            $returnCon = "不好意思，请确认下你的姓名和招新编号有没有按格式输入或输入有没有出错~\n";
            $returnCon.="尝试回复您的招新编号+姓名获取面试结果吧，如“C503+张框塔”";
        }
        $this->responseText($returnCon);
    }

    /**
     * 收到图片消息时触发，回复由收到的图片组成的图文消息
     *
     * @return void
     */
    protected function onImage() {
      $items = array(
          new NewsResponseItem('标题一', '描述一', $this->getRequest('picurl'),$this->getRequest('picurl')),
        new NewsResponseItem('标题二,点击进入百度网站', '描述二', $this->getRequest('picurl'), "http://www.baidu.com" ),
      );

      $this->responseNews($items);
    }

    /**
     * 收到地理位置消息时触发，回复收到的地理位置
     *
     * @return void
     */
    protected function onLocation() {
      $num = 1 / 0;
      // 故意触发错误，用于演示调试功能

      $this->responseText('收到了位置消息：' . $this->getRequest('location_x') . ',' . $this->getRequest('location_y'));
    }

    /**
     * 收到链接消息时触发，回复收到的链接地址
     *
     * @return void
     */
    protected function onLink() {
      $this->responseText('收到了链接：' . $this->getRequest('url'));
    }

    /**
     * 收到未知类型消息时触发，回复收到的消息类型
     *
     * @return void
     */
    protected function onUnknown() {
      $this->responseText('收到了未知类型消息：' . $this->getRequest('msgtype'));
    }

    private function explode_add($str){
    	$tmpArr = explode('+', $str);
    	if (count($tmpArr)  != 2){
    		return false;
    	}else{
    		return $tmpArr;
    	}
    }
    function firstResult($info){//一面结果
        $fir_result = $this->get_firResult($info);
        $name=$info[1];
        $returnCon="Quanta 2017招新一面结果：\n";
        switch ($fir_result) {
            case  3:
            case  2:
                $returnCon .= $name."同学：\n".'恭喜你成功在众多面试者中杀出重围，得到Quanta经理们的青睐，希望你能再接再厉，在接下来的考验中好好表现，成为Quanta的一份子~';
                break;
            // case  2:
            //     $returnCon .=  $name."同学：\n".'您的面试结果我们正在处理中，请耐心等待～';
            //     break;
            case  1:
                $returnCon .=  $name."同学：\n".'虽然你的表现也很出众，但很遗憾你没有得到Quanta经理们的垂青，希望你不要灰心，继续寻找属于你自己的道路，向着梦想奋力前行。';
                break;
            default:
                $returnCon = '不好意思，这位同学你好像没有来参加我们的招新吧？或许你应该确认下你的姓名和招新编号有没有按格式输入或输入有没有出错~尝试回复您的招新编码+姓名获取面试结果吧，如“C503+张框塔”';
                break;
        }
        return $returnCon;
    }
    function secondResult($info){//二面结果
        $fir_result = $this->get_secResult($info);
        $name=$info[1];
        $returnCon="Quanta 2017招新二面结果：\n";
        switch ($fir_result) {
            case  3:
            case  2:
                $returnCon .= $name."同学：\n".'恭喜你成功在众多笔试者中杀出重围，得到Quanta经理们的青睐，希望你能再接再厉，在接下来的考核期中好好表现，成为我们Quanta的一份子~';
                break;
            // case  2:
            //     $returnCon .=  $name."同学：\n".'您的笔试结果我们正在处理中，请耐心等待～';
            //     break;
            case  1:
                $returnCon .=  $name."同学：\n".'虽然你的表现也很出众，但很遗憾你可能不太适合Quanta，希望你不要灰心，继续寻找属于你自己的道路，向着梦想奋力前行。';
                break;
            default:
                $returnCon = '不好意思，这位同学你好像没有来参加我们的招新吧？或许你应该确认下你的姓名和招新编号有没有按格式输入或输入有没有出错~尝试回复您的招新编码+姓名获取笔试结果吧，如“C503+张框塔”';
                break;
        }
        return $returnCon;
    }

    private function get_firResult($info){
        $code = $info[0];
        $name = $info[1];

        $sql = "SELECT itv_code FROM interviewee_info WHERE name='$name' AND itv_code = '$code'";
        $num = mysql_query($sql);
        if (mysql_num_rows($num) == 1){
            $sql = "SELECT fir_result FROM first_itv WHERE itv_code = '$code'";
            $result = mysql_query($sql);
            $fir_result = mysql_fetch_assoc($result);
            return $fir_result['fir_result'];
        }else{
            return 0;
        }
    }
    private function get_secResult($info){
        $code = $info[0];
        $name = $info[1];

        $sql = "SELECT itv_code FROM interviewee_info WHERE name='$name' AND itv_code = '$code'";
        $num = mysql_query($sql);
        if (mysql_num_rows($num) == 1){
            $sql = "SELECT sec_result FROM second_itv WHERE itv_code = '$code'";
            $result = mysql_query($sql);
            $fir_result = mysql_fetch_assoc($result);
            return $fir_result['sec_result'];
        }else{
            return 0;
        }
    }
  }
  $wechat = new MyWechat('weixin', TRUE);
  $wechat->run();
