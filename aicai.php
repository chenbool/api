<?php
use DiDom\Document;

class AiCai
{
    protected $baseUrl = 'https://kaijiang.aicai.com/';
    protected $name;
    protected $url;

    function __construct(){

    }

    // 获取dom
    protected function getDoc($code){
        // url
        $this->url = $this->baseUrl.$code.'/';
        $doc = new Document($this->url, true);

        // 名称
        $name = $doc->find('.lot_js')[0]->find('.fs14')[0]->text();
        $this->name = $this->trim_space($name);

        return $doc;
    }

    // 查询开奖结果
    public function find($code='cqssc'){

        $doc = $this->getDoc($code);
        $res = $doc->find('.lot_box')[0];

        // 开奖区域
        $kj_area = $res->find('.lotb_top')[0]->find('.lot_js')[0];

        // 获取期号
        preg_match('#\d{2,}\d#',$kj_area->text(),$expect);
        $expect = $expect[0];

        // 获取开奖号码
        $ball = $kj_area->find('.lot_kjmub')[0]->find('.kj_ball')[0]->text();

        return [
            'name'      =>  $this->name,
            'expect'    =>  $this->trim_space( $expect),
            'opencode'  =>  $this->trim_space( $ball ),
            'splitcode' =>  $this->splitCode($ball),
            'opentime'  =>  time(),
            'opendate'  =>  date('Y-m-d H:i',time()),
            'url'       =>  $this->url
        ];
    }

    // 获取开奖列表
    public function getList($code='cqssc'){
        $doc = $this->getDoc($code);

        // 获取开奖区域列表
        $res = $doc->find('#jq_body_kc_result')[0]->find('tr');

        $tempArr = [];
        // 循环采集
        foreach ($res as $k => $v) {
            // 获取每行数据
            $tempArr[] = $this->getArgs($v->find('td'));
        }

        return $tempArr;
    }

    // 获取列表当中的详情
    protected function getArgs($data){
        //处理期号
        $expect = $this->get_pattern( $data[0]->text(),'\d{2,}-\d+\d');

        return [  
            'name'      =>  $this->name,
            'expect'    =>  $expect,
            'opencode'  =>  $this->trim_space( $data[2]->text() ),
            'splitcode' =>  $this->splitCode( $data[2]->text() ),
            'hezhi'     =>  false,
            'kuadu'     =>  false,
            'opentime'  =>  strtotime( $data[1]->text() ),
            'opendate'  =>  $data[1]->text(),
            'url'       =>  $this->url
        ];
    }

    // 分割号码 分割开奖号码
    protected function splitCode($code){
        // 分割开奖号码
        preg_match_all('#\d{1}#',$code,$split);
        return implode(",", $split[0]);
    }

    // 去除 空格 换行符
    protected function trim_space($arg){
        return preg_replace('#(\s|\r\n|\|)#','',$arg);
    }

    //获取正则匹配结果
    protected function get_pattern($data,$pattern='\d{1}'){
        preg_match('#'.$pattern.'#',$data,$res);
        return $res[0];
    }

}

/*
    // 开奖助手
    $kjh = new AiCai();

    // 获取单个
    $res = $kjh->find();
    dump($res);

    // 获取列表
    // $list = $kjh->getList();
    // dump( $list );
*/