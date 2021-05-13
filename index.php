<?php
//引用
require 'vendor/autoload.php';
//声明
use Medoo\Medoo;
//分类及对应ID
$catArr=array(
    'qingchun' => '1',
    'xiaohua' => '2',
    'chemo' => '3',
    'qipao' => '4',
    'mingxing' => '5',
    'xinggan' => '6'
);
$id = '';
$num = '';
$db = new medoo([
    'database_type' => 'sqlite',
    'database_file' => 'mm.db'
]);

//获取一张随机图片
function getRandImg(){
    global $db,$catArr,$id,$num;
    //连接数据库

    //判断参数
    $where=[];
    $t = $_GET["t"];
    if(array_key_exists($t,$catArr)){
        $where = ["cat" => $catArr[$t]];
    }

    //总数
    $count = $db->count('items',$where);

    //随机取一条
    $randRow = rand(0,$count);
    //echo $randRow;
    $where = array_merge($where,["LIMIT" => [$randRow, 1]]);
    $item = $db->select("items",['itemId','count'],$where);
    //print_r($items[0]);

    //随机的Id和张数
    $id = $item[0]['itemId'];
    $num = rand(1,$item[0]['count']);

    //存入Cookie
    setcookie('mmpic_id',$id);
    setcookie('mmpic_count',$item[0]['count']);
    setcookie('mmpic_num',$num);
    
    $url = "https://img1.hnllsy.com/pic/$id/$num.jpg";
    //echo $url;
    return $url;
}
$id = $_GET['id'];
$num = $_GET['num'];
if($id && $num){
    $url = "https://img1.hnllsy.com/pic/$id/$num.jpg";
    //存入Cookie
    setcookie('mmpic_id',$id);
    setcookie('mmpic_num',$num);
}else{
    $url = getRandImg();
}
//echo $url;
$baseUrl = "https://www.mm131.net/";
function referfile($url,$refer='') {
	$opt=array('http'=>array('header'=>"Referer:$refer"));
	$context=stream_context_create($opt);
	$fileco=file_get_contents($url,false,$context);
	return $fileco;
}
$file = referfile($url,$baseUrl);
if($_GET['down']=='1'){
header("content-disposition:attachment;filename=mm$id$num.jpg");
header('content-length:'. strlen($file));
}else{
header('Content-Type: image/jpeg');
}
echo $file;

//根据读取时间，判定是否要再读一次
function upDay(){
    global $db;
    $day = date("d");
    $upDay = $db->select("option",["op_value"],["op_name"=>"upDay"]);
    if($upDay){
        if($day == $upDay[0]['op_value']){
            return true;
        }else{
            $db->update('option',['op_value'=>$day],['op_name'=>'upDay']);
            return false;
        }
    }else{
        $db->insert('option',['op_name'=>'upDay','value'=>$day]);
        return false;
    }
}
if($_GET['noUpdate'] == '1'){}else{
    if(!upDay()){
        require 'scanNew.php';
    }
    
}
