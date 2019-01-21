<?php

    include './vendor/autoload.php';
    include './kjh.php';

    // 开奖助手
    // $kjh = new Kjh();

    // 获取单个
    // $res = $kjh->find('gpc-tjssc');
    // dump($res);

    // 获取列表
    // $list = $kjh->getList('gpc-chongqingssc');
    // dump( $list );
        
    // http://caipiao.163.com/order/cqssc/
    use DiDom\Document;
    $doc = new Document('https://kaijiang.aicai.com/cqssc/', true);
    $res = $doc->find('.lot_box')[0];
    
    // 开奖区域
    $kj_area = $res->find('.lotb_top')[0]->find('.lot_js')[0];

    $name = $kj_area->find('.fs14')[0]->text();

    dump( $name );
    // dump( $kj_area->text() );

    // 获取期号
    preg_match('#\d{2,}\d#',$kj_area->text(),$texts);
    $qh = $texts[0];

    dump( $qh );

    // 获取号码
    $ress = $kj_area->find('.lot_kjmub')[0]->find('.kj_ball')[0];
    $ball = trim($ress->text());

    dump( $ball );

    function dump($arg){
        echo '<div style="border:1px solid #ccc;background:#FAFAFA;padding:5px 20px;z-index:1000;margin:5px;"><pre>';
        
        if( is_array($arg) || is_object($arg)){
            print_r($arg);
        }else{
            var_dump($arg);
        }

        echo '</pre></div>';
    }

?>