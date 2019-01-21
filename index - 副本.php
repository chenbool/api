<?php

    include './vendor/autoload.php';
    use DiDom\Document;

    $doc = new Document('https://kjh.55128.cn/k/kjls/gpc-tjssc.html', true);
    
    $res = $doc->find('.kaij-data')[0];

    //期号
    $expect = trim_space( $res->find('.kaij-qs')[0]->text() );
    $ball = trim_space( $res->find('.kaij-cartoon')[0]->text() );
    
    dump($expect);
    dump($ball);


        
    function dump($arg){
        echo '<div style="border:1px solid #ccc;background:#FAFAFA;padding:5px 20px;z-index:1000;margin:5px;"><pre>';
        var_dump($arg);
        echo '</pre></div>';
    }

    // 去除空格
    function trim_space($arg){
        return preg_replace('#(\s|\r\n)#',"",$arg);;
    }

?>