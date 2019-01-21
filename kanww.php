<?php
use DiDom\Document;

class Kanww
{
    /*
    https://cdn.yangju.vip/k/?url=后面加上播放的地址即可
    https://cdn.yangju.vip/k/?url=
    https://jx.lache.me/cc/?url=
    https://api.653520.top/vip/?url=
    https://jx.ab33.top/vip/?url=
    https://vip.mpos.ren/v/?url=
    https://jx.000180.top/jx/?url=
    https://jx.km58.top/jx/?url=
    http://jx.taquu.com/index.php?url=
    
    https://blog.csdn.net/c327044572/article/details/80013316
    https://blog.csdn.net/Jiage_666/article/details/81866231
    */
    protected $baseUrl = 'http://m.kanww.com/';
    protected $name;
    protected $url;

    function __construct(){
        
    }

    // 获取dom
    protected function getDoc($code){
        // url
        $this->url = $this->baseUrl.$code.'.html';
        // html
        $html = $this->curl_get( $this->url );
        $doc = new Document($html);
        return $doc;
    }

    // 查询界面详情
    public function find($code='vod/25643'){
        $doc = $this->getDoc($code);
        // 获取主体区域
        return $this->introTxt( $doc->find('.main')[0] );   
    }
    
    // 获取 introTxt 区域
    protected function introTxt($res){
        $info = $res->find('.detailPosterIntro')[0];
        //$logo = $info->find('.posterPic')[0]->find('img');

        // 简介区域
        $introTxt = $info->find('.introTxt')[0];
        // 名称
        $title = $this->trim_space( $introTxt->find('h1')[0]->text() );
        
        // 更新状态
        $state = $introTxt->find('.sDes')[0]->text();
        $state = $this->get_pattern($state,'\d{1,}');
        
        // 简介区域
        $tabConList = $res->find('.tabConList')[0];
        
        // 选集列表
        $list = $tabConList->find('.tabCon')[0]->find('.ulNumList')[0]->find('a');
        
        $tempArr = [];
        // 循环采集
        foreach ($list as $k => $v) {
            // 获取每行数据
            $tempArr[] = [
                'name'  =>  $v->text(),
                'url'   =>  $this->baseUrl.preg_replace('#(^/)#','',$v->attr('href'))
            ];
        }
        
        //描述
        $desc = $tabConList->find('.tabCon')[1]->find('p');
        $year = $this->trim_space( $desc[1]->text() );
        $area = $this->trim_space( $desc[2]->text() );
        $type = $this->trim_space( $desc[3]->text() );
        $type = preg_replace('#(剧)#','',$type);
        $intro = preg_replace('#(展开>>)#','',$desc[4]->text());
        
        return [
            'title' =>  $title,
            'state' =>  $state,
            'year'  =>  $year,
            'area'  =>  $area,
            'type'  =>  $type,
            'intro'  =>  $intro,
            'list'  =>  $tempArr
        ]; 
    }
    
    // 获取列表
    public function getList($code='list/2'){
        
        $tempList = [];
        //取3页
        for ($i=1; $i < 3; $i++) { 
           $tempList[] = $this->getPage($i,$code);
        }
        return $tempList;
    }
    
    // 获取分页列表
    public function getPage($page=1,$code='list/2'){
        
        $page>=2 && $code.= '_'.$page;
        $doc = $this->getDoc($code);
        
        // 获取区域列表
        $res = $doc->find('#data_list')[0]->find('.con');
        $list = $this->getArgs($res);
        return $list;
    }
    
    
    // 获取列表当中的详情
    protected function getArgs($data){
        $tempArr = [];
        // 循环采集
        foreach ($data as $k => $v) {
            $href = $v->find('a')[0]->attr('href');
            $href = preg_replace('#(.html|^/)#','',$href);
                
            $info = $this->find($href); 
            //$info['logo']   = $this->baseUrl.$v->find('img')[0]->attr('src');
            // 获取每行数据
            $tempArr[] = $info;
            //usleep(500);
        }
        
        return $tempArr;
    }
    
    //发送get请求
    public function curl_get($url){
        $headers = [
            'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 SE 2.X MetaSr 1.0',
            //'user-agent:Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1'
        ];

        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch); 
        curl_close($ch);
        return $output;
    }
    
    //获取正则匹配结果
    protected function get_pattern($data,$pattern='\d{1}'){
        preg_match('#'.$pattern.'#',$data,$res);
        
        if( isset($res[0]) ){
            return $res[0];
        }else{
            return $res;
        }
    }    

    // 去除 空格 换行符
    protected function trim_space($arg){
        return preg_replace('#(\s|\r\n)#','',$arg);
    }

}

