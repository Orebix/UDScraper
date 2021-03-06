<?php
/*
@@This script will scrape the home page of urban dictionary
@@for terms it will then store the newest words in a 
@@database. Tt will then pull those words individually then 
@@find their corresponding definitions using a definition 
@@function scrapers and store them;
@@
@@This can also be altered to be used with other pages on UD
*/
try{
function addTerms($pageNumber)
{ 			
        require_once 'storeTerms.php';
	include	'getDef.php';
	require_once 'databaseConnect.php';
	$url="http://www.urbandictionary.com/?page=$pageNumber";
	$output=file_get_contents($url);
	$file="tempStorage.txt";
	file_put_contents($file,$output,FILE_APPEND);

	$contents= file_get_contents($file);

	//Gets and entire string  by searching for 
	//the quote below and sorting them into an array
	$pattern=preg_quote("term=","/");
	$pattern = "/^.*$pattern.*\$/m";
	preg_match_all($pattern,$contents,$matches,PREG_OFFSET_CAPTURE,4);
	//var_dump($matches);
	

	/*
	@@Reads through the array one line at a time
	@@then takes the line and strips it down until
	@@we are left with only the definition.
	*/
	$tWord="";
	$breaker="";
	$count=0;
	$finalTerm="";
	$def="";
	foreach($matches as $word)
	{
		
		for($i=0;$i<count($word);$i++)
		{
			$term=$word[$i][0];
			for($j=0;$j<strlen($term);$j++)
			{
				//echo $term[$j];
				
				if($term[$j]==">")
				{
					while($breaker != "<")
					{
						$tWord=$tWord.$term[$j+$count];	
						$breaker=$term[$j+$count];						
						$count++;
						
										
					}
				break;	
	
				}
			//echo $tWord;
			//cleans up the word for storage by replacing a space with a '+'
			//Also removes ><			
						
			
			
		}
		for($k=0;$k<strlen($tWord);$k++)
			{
				if($tWord[$k]!='<' && $tWord[$k]!='>' )
				{
					$finalTerm=$finalTerm.$tWord[$k];	
				}						
			}
			
			$finalTerm=preg_replace('/\s+/','+',$finalTerm);
			if($finalTerm != null)
			{	
				
				
				/*
				@@Checks the tables to see if the word is in the table
				@@if its not then the table will add the new term
				*/
				$database=$db;
				$termToCheck="select * from stores where term = '$finalTerm';";
				$checkDB=mysqli_query($database,$termToCheck);
				//$result = mysqli_free_result($fetchResult);
				$num=mysqli_num_rows($checkDB);
				
				if($num<1)
				{
					$def=storeDef($finalTerm);					
					//echo $finalTerm;				
					//echo $def;
					storeTerm($finalTerm,$def);			
					//echo $finalTerm;
					//echo"\r\n";
				}
			}		
			$tWord="";
		        $count=0;
		        $breaker="";
			//$def="";
			$finalTerm="";
			file_put_contents($file,"");
			}
	
	}
}
}catch(ErrorException $e){
	echo $e->getMessage();
}
?>

