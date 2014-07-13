<?php

// Subliminal binary path
$subliminalPath = "/usr/local/bin/";

// Request data
$videosTitles = $_POST["videosTitles"];
$languages = $_POST["languages"];

// Execute the Subliminal command
$videos = preg_split("/\r\n|\n|\r/", $videosTitles);
$videosList = "";
foreach ($videos as $video)
{
    $videosList .= escapeshellarg($video)." ";
}
exec($subliminalPath."subliminal -l ".implode(" ", $languages)." -d downloaded -- ".$videosList."2>&1", $out, $err);

// Parse the output to know what happened
$response = array();
$lastOutput = end($out);

// Bad input (not a movie/episode)
if (strpos($lastOutput, "ValueError") !== false)
{
    $response["nbOfSubtitles"] = 0;
    $response["error"] = "Your input doesn't seem to be a movie or an episode :(";
}
// No subtitles found
else if ($lastOutput == "No subtitles downloaded")
{
	$response["nbOfSubtitles"] = 0;
	$response["error"] = "Sorry, I didn't find the subtitles you want :(";
}
else
{
	$nbOfSubtitles = explode(" ", $lastOutput)[0];
	$response["nbOfSubtitles"] = $nbOfSubtitles;
	
	// Handle the subtitles
	$filenames = array();
	$currentSubtitle = count($out) - 2;
	$catchInfos = preg_match_all("/Saving <([^>]+)> to u'([^']+)'/", $out[$currentSubtitle], $infos);
	while ($catchInfos)
	{
        $provider = $infos[1][0];
        $filename = $infos[2][0];
    
        $filenames[] = $filename;
        
        $currentSubtitle--;
        $catchInfos = preg_match_all("/Saving <([^>]+)> to u'([^']+)'/", $out[$currentSubtitle], $infos);
	}
	
	// If only one, give directly the SRT file
	if ($nbOfSubtitles == 1)
	{
	    $response["downloadPath"] = $filenames[0];
	}
	// Else do a ZIP
	else
	{
	    // Initialize
	    $zip = new ZipArchive();
	    $zipFilename = "downloaded/".time().".zip";
	    if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE)
	    {
            exit("Unable to create zip");
        }
        // Add each subtitles
        foreach ($filenames as $filename)
        {
            $zip->addFile($filename, explode("/", $filename)[1]);
        }
        // Close and give the path
        $zip->close();
        $response["downloadPath"] = $zipFilename;
	}
}

header("Content-type: application/json");
echo json_encode($response);

?>