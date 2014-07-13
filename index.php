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
        
        <link href="css/select2.css" rel="stylesheet">
        <link href="css/select2-bootstrap.css" rel="stylesheet">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body id="download">
    
        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#download">Subconscious</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="#download">Download</a></li>
                        <li><a href="#about">About</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>

        <div class="container">
            <!-- MAIN FORM -->
            <div class="jumbotron jumbotron-sub">
		    	<h1>Your subtitles in 5 seconds.</h1>
		    	<p>Find the right subtitle for a movie or the last episode of your favorite serie is difficult. I know it. Now, with Subconscious, just copy & paste the title of your video and let the magic happen !</p>
		    	<form role="form" id="subconsciousForm" method="post" action="download-subtitles.php">
					<div class="form-group">
						<label for="videosTitles">Paste the full titles of your videos (episode or movie). One per line.</label>
						<textarea class="form-control" rows="5" id="videosTitles" name="videosTitles" placeholder="Game.of.Thrones.S04E01.720p.HDTV.x264-KILLERS.mkv" required></textarea>
					</div>
					<div class="form-group">
						<label for="languages">Select the language(s) you want</label>
						<select class="form-control" id="languages" name="languages[]" multiple required>
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
		    	<p>
		    	    <div class="alert" role="alert" id="subconsciousMessage">...</div>
		    	</p>
			</div>
			
			<!-- ABOUT PART -->
			<div class="page-header" id="about">
			    <h1>About Subconscious</h1>
			</div>
			
			<p class="lead">A web interface for the marvelous Python library of Diaoul, <a href="https://github.com/Diaoul/subliminal" target="_blank">Subliminal</a>. Get your subtitles for your movies and episodes in 5 seconds in a clean web interface ! It is released under the MIT license, check the <a href="https://github.com/frankie567/Subconscious" target="_blank">GitHub repository</a> !</p>
			
			<p class="lead">It relies on the marvelous Python library of Diaoul, <a href="https://github.com/Diaoul/subliminal" target="_blank">Subliminal</a>. All the credit goes to him.</p>
			
			<p class="lead">It also makes use of <a href="http://getbootstrap.com" target="_blank">Bootstrap</a>, <a href="http://ivaynberg.github.io/select2" target="_blank">Select2</a> and <a href="https://github.com/PixelsCommander/Download-File-JS" target="_blank">DownloadJS</a> scripts.</p>
			
		</div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script src="js/select2.min.js"></script>
        
        <script src="js/download.js"></script>
        
        <! -- Script to call the subtitle downloader in AJAX -->
        <script>
        	$(document).ready(function()
        	{
        	    // Initializations
        	    $("#languages").select2();
        	    $("#subconsciousMessage").hide();
        	    
        	    // If languages stored in local storage, select them
        	    if (localStorage.getItem("languages"))
        	    {
        	        $("#languages").select2("val", localStorage.getItem("languages").split(","));
        	    }
        	    
        		$("#subconsciousForm").submit(function(e)
        		{
        			e.preventDefault();
        			
        			// Loading state button and hide message
        			$("#formSubmitButton").attr("disabled","disabled");
        			$("#formSubmitButton").html("<i class='fa fa-refresh fa-spin'></i> Searching subtitles...");
        			$("#subconsciousMessage").hide();
        			
        			// Save in local storage the preferred languages
        			var languages = [];
                    $('#languages option:selected').each(function(){ languages.push($(this).val()); });
        			localStorage.setItem("languages", languages);
        			
        			// Submit the form
        			var url = $(this).attr('action');
        			var posting = $.post(url, $(this).serialize());
        			posting.done(function(data)
        			{
        				// If no subtitle found, error message
        				if (data.nbOfSubtitles == 0)
        				{
                            $("#subconsciousMessage").removeClass("alert-success");
                            $("#subconsciousMessage").addClass("alert-danger");
                            $("#subconsciousMessage").html(data.error);
        				}
        				// Else show the number of subtitles found and start download
        				else
        				{
        				    $("#subconsciousMessage").removeClass("alert-danger");
                            $("#subconsciousMessage").addClass("alert-success");
                            $("#subconsciousMessage").html("I found "+data.nbOfSubtitles+" subtitle(s).");
                            
                            downloadFile(data.downloadPath);
        				}
        				
        				// Normal state button
						$("#formSubmitButton").removeAttr("disabled");
						$("#formSubmitButton").html("Download my subtitles");
						
						// Show message
						$("#subconsciousMessage").show();
        			});
        		});
        	});
        </script>
    </body>
</html>