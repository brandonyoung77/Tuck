<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

include_once  'Chapter_Class.php';

if ((isset($_REQUEST['Current'])) && (!empty($_REQUEST['Current'])))
{
$Current = intval($_REQUEST['Current']);
}

if ((isset($_REQUEST['Story'])) && (!empty($_REQUEST['Story'])))
{
$Story = $_REQUEST['Story'];
}
else
{
	$Story = "SagaOfTuck";
}
header('Access-Control-Allow-Origin: *'); 
switch($_SERVER['REQUEST_METHOD'])
{
	case "GET":
		$Chapter = new Chapter($Story);
		if ((isset($_REQUEST['Current'])) && (!empty($_REQUEST['Current'])))
		{
			header('Content-type: application/json');
			$Chapter->GetFromDB($Current);
			echo $Chapter->ShowJSON();
		}
		else
		{
			header('Content-type: application/json');
			echo GetChapters($Story);
		}
		break;
	case "PUT":
		$Chapter = new Chapter($Story);
		if ((isset($_REQUEST['Current'])) && (!empty($_REQUEST['Current'])))
		{
			$Chapter->_id = $_REQUEST['Current'];
			$putdata = fopen("php://input", "r");
			$filelines = array();
			while (!feof($putdata))
			{
				$filelines[]  = stream_get_line($putdata,80,"\n");
			}
			$Chapter->ReadFromArray($filelines);
			$retval = $Chapter->SaveToDB();
			if ($retval->nUpserted > 0)
				send_response(201,"Created",$retval->_id);
			if ($retval->nModified > 0) 
				send_response(200,"OK",null);
			if ($retval->hasWriteError())
				send_response(500,"OK",$retval->writeConcernError);
		}
		break;
	case "POST":
		$Chapter = new Chapter($Story);
				
		$putdata = fopen("php://input", "r");
		$filelines = array();
		while (!feof($putdata))
		{
			$filelines[]  = stream_get_line($putdata,80,"\n");
		}
		$Chapter->ReadFromArray($filelines);
		$retval = $Chapter->SaveToDB();
		if ($retval->nUpserted > 0)
			send_response(201,"Created",$retval->_id);
		if ($retval->nModified > 0) 
			send_response(200,"OK",null);
		if ($retval->hasWriteError())
			send_response(400 ,"BAD REQUEST",$retval->writeError);
	
		break;
	case "DELETE":
		$Chapter = new Chapter($Story);
		if ((isset($_REQUEST['Current'])) && (!empty($_REQUEST['Current'])))
		{
			$Chapter->_id = $_REQUEST['Current'];			
			$retval = $Chapter->DeleteFromDB($Chapter->_id);
			if ($retval->nRemoved > 0)
				send_response(200,"OK",null);
			else
				send_response(204,"NO CONTENT",null);
			if ($retval->hasWriteError())
				send_response(400,"BAD REQUEST",$retval->writeError);
		}
		break;
}

function GetChapters($Story)
{
	$m = new MongoClient("mongodb://Xubuntu-VM:27017");
	$db = $m->Tuck;
	$chapters = $db->$Story;	
	$cursor = $chapters->find(array(),array("ChapterTitle"));
	$Chapterlist = Array();
	foreach($cursor as $title)
	{
		$Chapterlist[$title["_id"]] = $title["ChapterTitle"];
	}
	ksort ($Chapterlist);
	$TotalChapters = $chapters->count();	
	$json_return = json_encode(Array("TotalChapters" => $TotalChapters,"Chapterlist" => $Chapterlist));
	return $json_return;
}

function send_response($status,$status_message,$data)
{
	header("HTTP/1.1 $status $status_message");
	$response['status']=$status;
	$response['status_message']=$status_message;
	$response['data']=$data;
	$json_response= json_encode($response);
	echo $json_response;
	
}


?>