<?php

require_once "ArquivosAws.php";


$bck = new ArquivosAws('xxx' , 'xxxx' , 'xxxx');

//$result = $bck->uploadFile(__DIR__.'/data.txt','data2.txt');
$result = $bck->uploadDir(__DIR__."/aws-sdk-php/docs/guide");
$val = $bck->listObjects();
var_dump($val);
foreach($val as $val){
	echo $val;
}

// echo "Expiration: ".$result['Expiration'] . "<BR>";
// echo "ServerSideEncription: ".$result['ServerSideEncryption'] . "<BR>";
// echo "Etag: ".$result['ETag'] . "<BR>";
// echo "VersionId: ".$result['VersionId'] . "<BR>";
// echo "id: ".$result['RequestId'] . "<BR>";

// // Get the URL the object can be downloaded from
// echo "url: ".$result['ObjectURL'] . "<BR>";
?>