<?php

// Subliminal binary path
$subliminalPath = "/usr/local/bin/";

// Request data
$videoTitle = $_POST["videoTitle"];
$language = $_POST["language"];

// Execute the Subliminal command
exec($subliminalPath."subliminal -l ".$language." -d downloaded -- ".$videoTitle. " 2>&1", $out, $err);

// Parse the output to know if a subtitle was downloaded
$response = array();
$lastOutput = end($out);

if ($lastOutput == "No subtitles downloaded")
{
	$response["nbOfSubtitles"] = 0;
}
else
{
	$nbOfSubtitles = explode(" ", $lastOutput)[0];
	$response["nbOfSubtitles"] = $nbOfSubtitles;
	// Handle only one subtitle
	if ($nbOfSubtitles == 1)
	{
		// Get some informations : provider and filename
		$catchInfos = preg_match_all("/Saving <([^>]+)> to u'([^']+)'/", $out[count($out) - 2], $infos);
		if ($catchInfos)
		{
			$provider = $infos[1][0];
			$filename = $infos[2][0];
			
			$response["downloadPath"] = $filename;
		}
	}
}

header('Content-type: application/json');
echo json_encode($response);

?>