#!/usr/bin/perl -w

# Tuck XML Converter
# Converts Text files in the Input folder to XML files, which can be converted to HTML with XSL and CSS.

$dir = "./Input";

opendir DIR, $dir or die "cannot open dir $dir: $!";
my @files= readdir DIR;
closedir DIR;


$FileCount = @files;


# Now loop through array and convert the files
foreach $file (@files)
 {
   if(!(($file eq ".") || ($file eq "..")))
    {
     ConvertFile($file,$FileCount-2);
    }
 }

#Read in a File and Write out an html file with Navigation
sub ConvertFile
{
	my $InputFileName = $_[0];
	my $FileCount = $_[1];
	$InputFileName =~ m/(\d+)/;
	my $CurrChapter = $1;
	
	#Build Output file name: Lowercase, except first letter, pad chapter number, end in html			
	$OutputFileName = ucfirst(lc($InputFileName));
	$padded = sprintf("%0*d", 3, $CurrChapter);  
	$OutputFileName =~ s/$CurrChapter/$padded/;
	$OutputFileName =~ s/txt$/xml/i;

	#Read in the Text File
	open INPUT, "./Input/$InputFileName" or die $!;
	my @lines;
	@lines = <INPUT>;
	close(INPUT);
	
	#Get Chapter Title
	my $ChapterTitle;
	$ChapterTitle = $lines[2];
	chomp($ChapterTitle);
	
	#Create Output File
	
	mkdir("./Output", 0777) unless (-d "./Output");  #create Output folder if it doesn't exist
	open OUTPUT, ">./Output/$OutputFileName" or die $!;
	
	print OUTPUT "<?xml version='1.0' encoding='UTF-8'?>\n";
	print OUTPUT "<?xml-stylesheet type='text/xsl' href='tuck.xsl'?>\n";

	print OUTPUT "<Page>\n";
		
	#Create Index
	print OUTPUT "<Index>\n";
	for($i=1;$i<=$FileCount;$i++)
	{
		my $linkName = $OutputFileName;		
		$paddedCurr = sprintf("%0*d", 3, $CurrChapter);
		$padded = sprintf("%0*d", 3, $i);  
		$linkName =~ s/$paddedCurr/$padded/;
		print OUTPUT "<Link address='$linkName'	desc='$i'/>\n";
	}
	print OUTPUT "</Index>\n";
	
	#Previous file for navigation
	if ($CurrChapter -1 > 0)
	{ 
		my $PrevLink = $OutputFileName;
		my $PrevIndex = $CurrChapter -1;
		$paddedCurr = sprintf("%0*d", 3, $CurrChapter);
		$padded = sprintf("%0*d", 3, $PrevIndex);
		$PrevLink =~ s/$paddedCurr/$padded/;
		print OUTPUT "<Previous address='$PrevLink'/>\n";
	}
	if ($CurrChapter < $FileCount)
	{
		my $NextLink = $OutputFileName;
		my $NextIndex = $CurrChapter + 1;
		$paddedCurr = sprintf("%0*d", 3, $CurrChapter);
		$padded = sprintf("%0*d", 3, $NextIndex);
		$NextLink =~ s/$paddedCurr/$padded/;
		print OUTPUT "<Next address='$NextLink'/>\n";
	}
	
	print OUTPUT "<CurrChapter>$CurrChapter</CurrChapter>\n";
	print OUTPUT "<Title>Chapter $CurrChapter - $ChapterTitle</Title>\n";
	
	print OUTPUT "<ChapterText>\n";
	print OUTPUT "<![CDATA[\n"; 
	for($count=0;$count<@lines;$count++)
	{
		print OUTPUT "$lines[$count]";	
	}
	print OUTPUT "\n]]>\n";
	print OUTPUT "</ChapterText>\n";
	print OUTPUT "</Page>\n";
	
	#Close Output File	
	close(OUTPUT);
	   
}

