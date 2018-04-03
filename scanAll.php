<?php
require 'functions.php';


foreach($catArr as $k=>$v){

    $page = 1;

    while(true){
        //输出记录
        echo "scan:$k;page:$page\n";
        $url = listUrl($k,$v,$page);
        if(hasNext($url)){
            getList($url,$v);
        }else{
            echo "Finish:$k \n";
            break;
        }
        $page++;
    }

}

