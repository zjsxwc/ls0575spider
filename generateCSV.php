<?php
/**
 * Created by IntelliJ IDEA.
 * User: wangchao
 * Date: 11/3/17
 * Time: 10:47 AM
 */

$resultString = file_get_contents("result.serialize");
$result = unserialize($resultString);


$csvString = "";

$csvString .= "发布日期" . ",";
$csvString .= "成交套数" . ",";
$csvString .= "平均价格" . ",";
$csvString .= "标题" . ",";
$csvString .= "网页地址" . "\n";

foreach ($result as $oneResult) {

    $csvString .= $oneResult["date"] . ",";
    $csvString .= $oneResult["houseNum"] . ",";
    $csvString .= $oneResult["averagePrice"] . ",";

    $title = str_replace(",","",$oneResult["title"]);
    $title = str_replace("\n","",$title);
    $csvString .= $title . ",";
    $csvString .= $oneResult["articleUrl"] . "\n";

}

file_put_contents("result.csv", $csvString);




