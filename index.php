<?php

    include './vendor/autoload.php';
    include './kjh.php';
    include './aicai.php';
    include './kanww.php';

    // 开奖助手
    $kjh = new Kanww();

    // 获取单个
    $res = $kjh->find();
    dump($res);

//    https://ck.ee7e.com/gh/?v=jIansdqEpZCysqo000owrQO0O0OO0O0O_youku
    // 获取列表
//     $list = $kjh->getList();
//     dump( $list );

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