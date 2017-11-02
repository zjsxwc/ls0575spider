<?php

include __DIR__ . '/vendor/autoload.php'; // 引入 composer 入口文件


function query(){

    $baseUrl = "http://www.0575ls.cn/newslist";
    $page = 1;
    $maxPage = -1;

    $result = [];
    while (true) {
        $url = $baseUrl."?g=1&page=".$page;
        $html = file_get_contents($url);
        $html=iconv('gbk', 'utf-8', $html);

        $dom = pQuery::parseStr($html);

        if (($page == 1) && ($maxPage < 0)) {
            /** @var \pQuery\IQuery[] $allBadgeList */
            $paginationLast = $dom->query('.pagination li:nth-last-child(2)');
            $maxPage = intval($paginationLast->text());
        }
        $page++;
        if ($page>$maxPage) {
            break;
        }

        /** @var \pQuery\IQuery[] $allBadgeList */
        $allBadgeList=$dom->query('article h3');

        foreach ($allBadgeList as $badge) {
            /** @var \pQuery\DomNode $badge */

            $note = ($badge->getNextSibling("p")->getNextSibling("p"));
            $noteString = $note->html();
            $noteStringSegs = explode("</span>", $noteString);
            if (isset($noteStringSegs[1])) {
                $date = $noteStringSegs[1];
            } else {
                $date = "unknown";
            }

            //var_dump($badge->html());
            $title = $badge->text();
            /** @var \pQuery\IQuery $eleA */
            $eleA = $badge->query("a")[1];

            $articleUrl = $baseUrl . $eleA->attr('href');

            preg_match("/(\d+)套.*均价(.*)元\/平米/", $title, $m);
            if (empty($m[1])||empty($m[2])) {
                continue;
            }

            $houseNum = intval($m[1]);
            $averagePrice = floatval($m[2]);

            printf("%s: %s %s".PHP_EOL, $date, $houseNum, $averagePrice);
            $oneResult = [
                "date" => $date,
                "houseNum" => $houseNum,
                "averagePrice" => $averagePrice,
                "articleUrl" => $articleUrl,
                "title" => $title,
                "note" => $noteString,
            ];
            $result[] = $oneResult;
        }
    }
    return $result;
}

$result = query();
file_put_contents("result.json", json_encode($result));
