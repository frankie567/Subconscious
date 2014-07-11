<?php

// Request data
$videoTitle = $_POST["videoTitle"];
$language = $_POST["language"];

// Execute the Subliminal command
exec("/Applications/MAMP/Library/bin/subliminal -l ".$language." -d downloaded -- ".$videoTitle. " 2>&1", $out, $err);

// Parse the output to know if a subtitle was downloaded
$lastOutput = end($out);
if ($lastOutput == "No subtitles downloaded")
{
	echo "Not found";
}
else
{
	$nbOfSubtitles = explode(" ", $lastOutput)[0];
	// Handle only one subtitle
	if ($nbOfSubtitles == 1)
	{
		// Get some informations : provider and filename
		$catchInfos = preg_match_all("/Saving <([^>]+)> to u'([^']+)'/", $out[count($out) - 2], $infos);
		if ($catchInfos)
		{
			$provider = $infos[1][0];
			$filename = $infos[2][0];
			
			// Set headers to download the file
			header("Content-disposition: attachment; filename=".explode("/", $filename)[1]);
			header("Content-type: text/plain");
			readfile($filename);
			
			// Remove the file
			unlink($filename);
		}
	}
}

?>