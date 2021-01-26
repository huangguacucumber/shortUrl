<?php
header('content-type:application/json');
$getURL = base64_decode($_GET['url']);
include "config.php";
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
mysqli_query($conn , "set names utf8");
$sql = 'SELECT yuan_url, 
        xian_url
        FROM short_url
        WHERE yuan_url="'.base64_encode($getURL).'"';
 
mysqli_select_db( $conn, 'short_url' );
$retval = mysqli_query( $conn, $sql );
while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC))
{
    $mysqldata = "{$row['yuan_url']}";
}
function checkstr($str){
    $needle ='@';//判断是否包含@这个字符
    $tmparray = explode($needle,$str);
    if(count($tmparray)>1){
    return true;
    } else{
    return false;
    }
}
if(strpos($getURL,'@') == false){
    if (filter_var($getURL, FILTER_VALIDATE_URL) == true){
        if ($mysqldata==""){
            class ShortUrl {
                //字符表(base64)
                public static $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_";
                public static function encode($url)
                {
                    $key = 'salt'; //加盐
                    $urlhash = md5($key . $url);
                    $len = strlen($urlhash);
                    for ($i = 0; $i < 4; $i++) {
                        $urlhash_piece = substr($urlhash, $i * $len / 4, $len / 4);
                        $hex = hexdec($urlhash_piece) & 0x3fffffff;
                        $short_url = "";
                        //生成6位短网址(如要修改请将"$j < 6;"中的"6"改为自己想要的位数)
                        for ($j = 0; $j < 6; $j++) {
                            $short_url .= self::$charset[$hex & 0x0000003d];
                            $hex = $hex >> 5;
                        }
                        $short_url_list[] = $short_url;
                    }
                    return $short_url_list;
                }
            }
            $url = $getURL;
            $yuan_url = base64_encode($getURL);
            $short = ShortUrl::encode($url);
            $sql = "INSERT INTO short_url ".
                "(yuan_url,xian_url) ".
                "VALUES ".
                "('$yuan_url','$short[0]')";
            mysqli_select_db( $conn, 'short_url' );
            $retval = mysqli_query( $conn, $sql );
            echo '{"status":"new","short_url":"https://'.$domain.'/'.$short[0].'","url":"'.$getURL.'"}';
        }else{
            $sql = 'SELECT yuan_url, 
                xian_url
                FROM short_url
                WHERE yuan_url="'.base64_encode($getURL).'"';
            mysqli_select_db( $conn, 'short_url' );
            $retval = mysqli_query( $conn, $sql );
            while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC)){
                $mysqldataxian = "{$row['xian_url']}";
            }
            echo '{"status":"old","short_url":"https://'.$domain.'/'.$mysqldataxian.'","url":"'.$getURL.'"}';
        }
    }
}
?>