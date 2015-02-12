<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

include_once  'Chapter_Class.php';

$filelist = array();

if ((isset($_REQUEST['Story'])) && (!empty($_REQUEST['Story'])))
{
$Story = $_REQUEST['Story'];
}
else
{
$Story="SagaOfTuck";
}

$Dir = "./" . $Story . "/";

if ($handle = opendir($Dir)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
			$ChapterA = new Chapter($Story);
			$ChapterA->ReadFromFile($Dir . $file);
			$ChapterA->SaveToDB();
			echo "saved " . $ChapterA->_id . ":" . $ChapterA->ChapterTitle ."\n";
        }
    }
    closedir($handle);

}


