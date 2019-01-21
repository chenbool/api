<?php
use DiDom\Document;

class Kjh
{
    protected $baseUrl = 'https://kjh.55128.cn/k/kjls/';
    protected $name;
    protected $url;

    function __construct(){
        
    }

    // 获取dom
    protected function getDoc($code){
        // url
        $this->url = $this->baseUrl.$code.'.html';
        $doc = new Document($this->url, true);
        // 名称
        $name = $doc->find('.kaij-data')[0]->find('.kaij-name')[0]->text();
        $this->name = $this->trim_space($name);
        return $doc;
    }

    // 查询开奖结果
    public function find($code='gpc-tjssc'){

        $doc = $this->getDoc($code);

        // 获取开奖区域
        $res = $doc->find('.kaij-data')[0];

        //期号
        $expect = $res->find('.kaij-qs')[0]->text();
        // 开奖号码
        $ball   = $res->find('.kaij-cartoon')[0]->text();

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
    public function getList($code='gpc-chongqingssc'){
        $doc = $this->getDoc($code);

        // 获取开奖区域列表
        $res = $doc->find('#table')[0]->find('tbody')[0]->find('tr');

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
        return [  
            'name'      =>  $this->name,
            'expect'    =>  $data[1]->text(),
            'opencode'  =>  $this->trim_space( $data[2]->text() ),
            'splitcode' =>  $this->splitCode( $data[2]->text() ),
            'hezhi'     =>  $this->trim_space( $data[3]->text() ),
            'kuadu'     =>  $this->trim_space( $data[4]->text() ),
            'opentime'  =>  strtotime( $data[0]->text() ),
            'opendate'  =>  $data[0]->text(),
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
        return preg_replace('#(\s|\r\n)#','',$arg);
    }

}

/*
    // 开奖助手
    $kjh = new Kjh();

    // 获取单个
    $res = $kjh->find('gpc-tjssc');
    dump($res);

    // 获取列表
    // $list = $kjh->getList('gpc-chongqingssc');
    // dump( $list );
*/