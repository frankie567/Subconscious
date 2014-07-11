<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Subconscious - Your subtitles in 5 seconds</title>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        
        <link href="css/subconscious.css" rel="stylesheet">
        <link href="css/font-awesome.min.css" rel="stylesheet">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
    
        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Subconscious</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="#">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>

        <div class="container">
            <div class="jumbotron jumbotron-sub">
		    	<h1>Your subtitles in 5 seconds.</h1>
		    	<p>Find the right subtitle for a movie or the last episode of your favorite serie is difficult. I know it. Now, with Subconscious, just copy & paste the title of your video and let the magic happen !</p>
		    	<form role="form" id="subconsciousForm" method="post" action="download-subtitles.php">
		    		<div class="form-group">
						<label for="videoTitle">Paste the full title of your video (episode or movie)</label>
						<input type="text" class="form-control" id="videoTitle" name="videoTitle" placeholder="Game.of.Thrones.S04E01.720p.HDTV.x264-KILLERS.mkv" required>
					</div>
					<div class="form-group">
						<label for="language">Select the language you want</label>
						<select class="form-control" id="language" name="language" required>
							<?php
								// Open the file containing the language and populates the options
								$languagesFile = file_get_contents("js/languages.js");
								$languages = json_decode($languagesFile, true);
								
								// Get the language of the user
								$userLanguage = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);

								foreach ($languages as $languageCode => $languageNames)
								{
									echo "<option value='".$languageCode."' ".($languageCode == $userLanguage ? "selected" : "").">".$languageNames["name"]."</option>";
								}
							?>
						</select>
					</div>
					<button type="submit" id="formSubmitButton" class="btn btn-primary btn-lg btn-block">Download my subtitles</button>
		    	</form>
			</div>
		</div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        
        <! -- Script to call the subtitle downloader in AJAX -->
        <script>
        	$(document).ready(function()
        	{
        		$("#subconsciousForm").submit(function(e)
        		{
        			e.preventDefault();
        			
        			// Loading state button
        			$("#formSubmitButton").attr("disabled","disabled");
        			$("#formSubmitButton").html("<i class='fa fa-refresh fa-spin'></i> Searching subtitles...");
        			
        			// Submit the form
        			var url = $(this).attr('action');
        			var posting = $.post(url, $(this).serialize());
        			posting.done(function(data)
        			{
        				console.log(data);
        				
        				// Normal state button
						$("#formSubmitButton").removeAttr("disabled");
						$("#formSubmitButton").html("Download my subtitles");
        			});
        		});
        	});
        </script>
    </body>
</html>