<?php
class Chapter 
{
	public $_id;	
	public $ChapterTitle;
	public $ChapterText;
	private $TotalChapters;
	private $StoryName;
	
	public function __construct($Name)
	{		
		$this->StoryName = $Name;
	}
	
	public function ReadFromFile($Filename) 
	{
		$this->_id = intval(filter_var($Filename, FILTER_SANITIZE_NUMBER_INT));
		
		$FileLines = file($Filename);
		$this->ChapterText = implode("",$FileLines);
		for($count=1;$count<count($FileLines);$count++)
		{
			if (preg_match("/^\s*$/", $FileLines[$count]) == 0)
			{
				$this->ChapterTitle = $FileLines[$count];
				break;
			}  	
		}	
	}
	
	public function ReadFromArray($FileLines)
	{
		$this->ChapterText = implode("",$FileLines);
		for($count=1;$count<count($FileLines);$count++)
		{
			if (preg_match("/^\s*$/", $FileLines[$count]) == 0)
			{
				$this->ChapterTitle = $FileLines[$count];
				break;
			}  	
		}	
	}
	
	public function SaveToDB()
	{
		$StoryName = $this->StoryName;		
		$m = new MongoClient();
		$db = $m->Tuck;
		$chapters = $db->$StoryName;	
		return $chapters->save(array("_id" => $this->_id,"ChapterTitle" => $this->ChapterTitle,"ChapterText" => $this->ChapterText));
	}
	
	public function GetFromDB($i)
	{		
		$StoryName = $this->StoryName; 		
		$m = new MongoClient();
		$db = $m->Tuck;
		$chapters = $db->$StoryName;
		$a = $chapters->findOne( array("_id"=>$i));
		$this->_id = $a["_id"];
		$this->ChapterTitle = $a["ChapterTitle"];
		$this->ChapterText = $a["ChapterText"];
		$this->TotalChapters = $chapters->count();
	}
	
	public function DeleteFromDB($i)
	{
		$StoryName = $this->StoryName; 		
		$m = new MongoClient();
		$db = $m->Tuck;
		$chapters = $db->$StoryName;
		return $chapters->remove( array("_id"=>$i));
	}
	
	public function ShowXML()
	{
		$previous = $this->_id - 1; 
		$next = $this->_id + 1;
		$doc = new DOMDocument("1.0","UTF-8"); 
		$doc->formatOutput = true;
		$xslt = $doc->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="tuck.xsl"');
		$doc->appendChild($xslt);

		$Page = $doc->createElement("Page");
		$doc->appendChild($Page);
		$Index = $doc->createElement("Index");
		$Page->appendChild($Index);

		for($i=1;$i<=$this->TotalChapters;$i++)
		{
			$link = $doc->createElement("Link");
			$link->setAttribute("address","?Story=".$this->StoryName."&Current=" . $i);
			$link->setAttribute("desc",$i);
			$Index->appendChild($link);
		}

		if ($previous > 0){

		$PreviousNode = $doc->createElement("Previous");
		$PreviousNode->setAttribute("address","?Story=".$this->StoryName."&Current=" . $previous);
		$Page->appendChild($PreviousNode);
		}
		if ($next < $this->TotalChapters)
		{
		$NextNode = $doc->createElement("Next");
		$NextNode->setAttribute("address","?Story=".$this->StoryName."&Current=" . $next);
		$Page->appendChild($NextNode);
		}

		$CurrChapterNode = $doc->createElement("this",$this->ChapterTitle);
		$Page->appendChild($CurrChapterNode);

		$Title = $doc->createElement("Title","Chapter " . $this->_id . " - " . $this->ChapterTitle);
		$Page->appendChild($Title);

		$ChapterText = $doc->createElement("ChapterText");
		$Page->appendChild($ChapterText);
		$CData = $doc->createCDATASection("\n" .$this->ChapterText . "\n");
		$ChapterText->appendChild($CData);

		return $doc->saveXML();
	}
	
	public function ShowJSON()
	{
		$returnArray = array("Current"=> $this->_id,
		                "TotalChapters"=>$this->TotalChapters,
						"ChapterTitle"=>$this->ChapterTitle,
						"ChapterText"=>$this->ChapterText);
		return json_encode($returnArray);
	}
	
	
}
	








?>