<?php
require 'functions.php';

foreach($catArr as $k=>$v){
    $url = listUrl($k,$v);
    getList($url,$v);
}

