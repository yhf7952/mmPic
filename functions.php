<?php
require 'vendor/autoload.php';
ini_set('max_execution_time', 300);

use QL\QueryList;
use Medoo\Medoo;

#设置开始
$baseUrl = 'http://www.mm131.com/';
$catArr=array(
    'qingchun' => '1',
    'xiaohua' => '2',
    'chemo' => '3',
    'qipao' => '4',
    'mingxing' => '5',
    'xinggan' => '6'
);

//根据URL获取html
function readHtml($url){
    $ql = QueryList::get($url,[],[
        'headers' => [
            'Referer' => $baseUrl
        ]
    ])->encoding("UTF-8");
    return $ql->getHtml();
}

//判断是否有下一页
function hasNext($url){
    $html = readHtml($url);
    //echo $html;
    $data = QueryList::html($html)->find('.page-en')->texts()->all();
    return array_search('下一页', $data);
}

$db = new medoo([
    'database_type' => 'sqlite',
    'database_file' => 'mm.db'
]);

//获取列表，和总条数,并存入数据库
function getList($url,$cat = ''){
    //echo $url;
    $html = readHtml($url);
    $data = QueryList::html($html)->find('dd>a:lt(19)')->attrs('href');
    //print_r ($data);

    foreach($data as $item){
        //echo $item;
        //获取文章ID
        $itemId = substr($item,strrpos($item,'/')+1);
        $itemId = substr($itemId,0,strrpos($itemId,'.'));
        //echo $itemId;

        //获取文章总条数
        $itemHtml = readHtml($item);
        $itemData = QueryList::html($itemHtml)->find('.page-ch:first')->text();
        $itemCount = str_replace('共','',$itemData);
        $itemCount = str_replace('页','',$itemCount);
        //echo $itemCount;

        //都有值
        if($itemId && $itemCount){
            global $db;
            $dbResult=$db->select("items","id",["itemId" => $itemId]);

            //print_r($dbResult);
    
            //插入数据库
            if(!$dbResult){
                $db->insert("items",[
                    'cat' => $cat,
                    "itemId" => $itemId,
                    "count" => $itemCount
                ]);
            }
        }
        
    }
}


function listUrl($catName,$catId,$page=1){
    global $baseUrl;
    $url = $baseUrl.$catName;
    if($page > 1){
        $url = $url . '/list_'.$catId.'_'.$page.'.html';
    }
    return $url;
}