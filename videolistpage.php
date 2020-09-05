<!DOCTYPE html>
<html lang="en">
<?php 
include 'php/CheckPrivileges.php';
include("php/global_toolbar.php");
include 'php/Landing.php';
include("php/config.php");
session_start();
if (isset($_GET['videopageid'])) {
	$editID = $_GET['videopageid'];
	$sql = "SELECT `Title` FROM `MediaPage` WHERE `ID` = ".$editID;
	$result = mysqli_query($con, $sql);
	$sqlRow =mysqli_fetch_assoc($result);
}
?>
<head>
	  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	  
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="css/indexCss.css"
<!--<link rel="stylesheet" type="text/css" href="./css/videolist.css" --> 


  <title>MTSU Audio/Visual Services</title>

  

</head>

<body>
	<div class="MainBanner">
                    <div class="header">                       
													
                          <a href="index.php"><img src="images/avsLogo-white_redo.svg" alt="MTSU Audio Visual Services" /></a>
						 <!-- images/avsLogo-white_200h.svg" -->
						
            </div>
		</div>

  <!-- Page Content -->
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <div class="card">
		<h4 class="card-header"><?php echo $sqlRow['Title']; ?></h4>
          <div class="card-body">
            <h2 class="card-title"></h2>
			<?php if(isset($_SESSION["loggedin"]) && (($_SESSION['role']=='Site Admin')or($_SESSION['role']=='Content Manager'))) //check if user is an admin or content manager if they are display an edit button
			{  ?>
				<a href="videoCreationPage.php?videopageid=<?php echo $editID; ?>" style="display: <?php if(!isset($editID)) { echo 'none';} ?>" class="btn btn-primary">
					Edit
				</a>
			
			
				<?php
			}
			?>
			<div> 
					<p id="demo"> <!-- this is the timer element id -->
					</p>
				</div>
        
			
			 <?php
				
				
				$vidid=$_GET['videopageid']; //get the videoID passed in from the landing page
				
				if(isset($_SESSION["loggedin"]) && (($_SESSION['role']=='Site Admin')or($_SESSION['role']=='Content Manager'))) //check if admin or content manager
				{	
					
					$sqlAdmin = 'SELECT * FROM Media WHERE `MediaPage_Id` =' .$vidid; //select all videos for a project whether they are public or not
					$resAd = mysqli_query($con,$sqlAdmin);
					
					if(mysqli_num_rows($resAd)>0)
					{ 	
						 while($row=mysqli_fetch_assoc($resAd)) //display the result of the query
						{ ?>
						<div class="card">
						<div class="card-body"><?php echo "File: " . $row["Filename"] . "<br>" . "Title: " . $row["Title"]. "<br>" . "Size: " . $row["Size"]/1000000 ." MB"  . "<br>"  . "Width: " . $row["Width"] . "<br>" . "Height: " .  $row["Height"] . "<br>";?> 
						<a href="embededVideoPlayer.php?videoid=<?php echo $row['ID']; ?>&title=<?php echo $row['Title'];?>"  class="btn btn-primary">Approval View &rarr;</a>
					  
					  <a href="<?php echo("https://www.cs.mtsu.edu" . $row['Videourl'])?>"  class="btn btn-primary" download>
						Download 
					  </a>
					  
					<button type="button" class="btn btn-dark"  onclick="shortenlink()">Share Link</button>
					</div>
					</div>
					<?php
						}
					}
				}
		?>
		<?php
				if(isset($_SESSION["loggedin"]) && ($_SESSION['role']=='Client')) //check to see if the user is a client
				{	
					
					$sqlCli = 'SELECT * FROM Media WHERE is_public=1 AND`MediaPage_Id` =' .$vidid; //select only the public videos for that project
					$cliAd = mysqli_query($con,$sqlCli);
					
					if(mysqli_num_rows($cliAd)>0)
					{ 
						 while($row=mysqli_fetch_assoc($cliAd)) //display the results of the query
						{ ?>	
						<div class="card">
						<div class="card-body"><?php echo "File: " . $row["Filename"] ."<br>" . "Title: " . $row["Title"]. "<br>" . "Size: " . $row["Size"]/1000000 ."MB"  . "<br>"  . "Width: " . $row["Width"] . "<br>" . "Height: " .  $row["Height"] . "<br>";?> 
						<a href="embededVideoPlayer.php?videoid=<?php echo $row['ID']; ?>&title=<?php echo $row['Title'];?>"  class="btn btn-primary">Approval View &rarr;</a>
					  
					  <a  class="btn btn-primary" data-toggle="modal" data-target="#myModal">Download</a>
					 
					  
					<button type="button" class="btn btn-dark"  onclick="shortenlink()">Share Link</button>
					</div>
					</div>
					<?php
						}
					}
				}
		?>
		
		
		<?php
				
			
			
				//If the user is not logged in select the public media if there is no public media return them to the landing page
				$vidurl = 'SELECT `videourl` FROM `Media` WHERE `ID`= '.$vidid;
				$vidurlres = mysqli_query($con,$vidurl);
				if(mysqli_num_rows($vidurlres)>0)
				{
					while($row = mysqli_fetch_assoc($vidurlres))
						{
							//echo "url:". $row["videourl"];
						}
				}
				//getting the time to archive and using that value to tell the user how much longer they will be able to download the video
                $timeleft = "SELECT Time_to_Archive FROM MediaPage WHERE ID=".$vidid;
				$get_time = mysqli_query($con,$timeleft);
				while($row=mysqli_fetch_assoc($get_time))
				{
						$time_30 = $row['Time_to_Archive']; //base time to archive
						$time_60 = $row['Time_to_Archive'] +2592000; //time +60 days in unix time
						$time_90 = $row['Time_to_Archive'] +  5184000; //time +90 days in unix time
				}
				
				if(!isset($_SESSION["loggedin"]))
				{
					$sql = 'SELECT * FROM `Media` WHERE is_public = 1 AND `MediaPage_Id` =' .$vidid;
					//echo("In WRONG SELECT");
					$result = mysqli_query($con, $sql);
						if (mysqli_num_rows($result) > 0) {
							// output data of each row
								while($row = mysqli_fetch_assoc($result)) { ?>
									<div class="card">
										<div class="card-body"><?php echo "File: " . $row["Filename"] . "<br>" .  "Title: " . $row["Title"]. "<br>" . "Size: " . $row["Size"]/1000000 ." MB" . "<br>"  . "Width: " . $row["Width"] . "<br>" . "Height: " .  $row["Height"] . "<br>"; ?> 
											<a href="embededVideoPlayer.php?videoid=<?php echo $row['ID']; ?>&title=<?php echo $row['Title'];?>"  class="btn btn-primary">Approval View &rarr;</a>
					  
											<a class="btn btn-primary" data-toggle="<?php if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
																																					echo 'modal';
																																					} else {
																																					} ?>" data-target="#myModal">
																																						Download 
											</a>
											<button type="button" class="btn btn-dark"  onclick="shortenlink()">Share Link</button>
								</div> <!-- </card body> -->
							</div> <!-- </card>-->


			
					
				<?php
				}
				}
				else { //returning users to the landing page if there are no public videos associated with the project
					echo '<script type="text/javascript">
									window.location="https://www.cs.mtsu.edu/~se19_cem/index.php"
									</script>';
					
				
				}
				//close the connection to the database
				mysqli_close($con);
				}
			 
				
			?>
				
			
          
				


		
			
          </div>
          
        </div>
		</div>

       

      <!-- Sidebar Widgets Column -->
	  
      <div class="col-lg-4">
		<div class="card">
          <h4 class="card-header">Projects</h4>
		       <div class="Pubproj">
                         <div id="ProjHolder">
						<?php $projLoc = 0; //current page of projects
								$projLim = 5; //limit of project pages 
								getPublicProjects($projLoc,$projLim); ?>
					</div>
			 </div>
			 </div>
			 </div>
            
			
			
			  
            <!--</div>-->
          
        
		
		
        <!-- Side Widget 
        <div class="card my-4">
          <h5 class="card-header">Side Widget</h5>
          <div class="card-body">
            You can put anything you want inside of these side widgets. They are easy to use, and feature the new Bootstrap 4 card containers!
          </div>
        </div>
			-->
			
 <!-- This modal will save the first and last name  and mark is downloaded to 1 -->
 <div id="myModal" class="modal fade" role="dialog">
<div class="modal-dialog">
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">×</button>
</div>
<h4 class="modal-title">Guest Download</h4>
<div class="modal-body">

        <form role="form" method="post" id="reused_form" >
     

        <div class="form-group">
            <label for="name">
               First Name:</label>
            <input type="text" class="form-control"
            id="fname" name="fname"   required maxlength="50">

        </div>
        <div class="form-group">
            <label for="lname">
                Last Name:</label>
            <input type="text" class="form-control"
            id="lname" name="lname" required maxlength="50">
        </div>
        <button type="submit" class="btn btn-lg btn-success btn-block" id="btnDownload">Verify →</button>
		
		
    </form>
    <div id="success_message" style="width:100%; height:100%; display:none; ">
        <h3>Thank You!</h3>
		<?php	
		
					$sql = "SELECT * FROM `Media` WHERE is_public = 1 AND `MediaPage_Id` = ".$vidid;//select only the public videos for that project
					$my_query = mysqli_query($con,$sql);
					
					if(mysqli_num_rows($my_query)>0)
					{ 
						 while($row=mysqli_fetch_assoc($my_query)) 
						{ 
								//echo $row['Videourl'];
								
								?>
								<a href="<?php echo("https://www.cs.mtsu.edu" . $row['Videourl'])?>"  class="btn btn-primary" download>Download</a>
								<?php
						}
				}
				
		mysqli_close($con)
		?>
	
    </div>
    <div id="error_message"
    style="width:100%; height:100%; display:none; ">
        <h3>Error</h3>
        Sorry there was an error sending your form.

    </div>
</div>

</div>

 </div>
</div>
    <!-- /.row -->
		</div>

  
  <!-- /.container -->
	</div>
  <!-- Footer -->
  <?php include 'php/footer.php';?>
  


  <!-- Bootstrap core JavaScript -->
<script src="https://code.jquery.com/jquery-3.2.1.js"</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  <!-- Display the countdown timer in an element -->
  <script>

// Set the date we're counting down to
var initialTime = <?php echo($time_30);?>;
//alert(initialTime);
var myDate = ((new Date).getTime())/1000;
//alert(myDate);

//Place here the total of seconds you receive on your PHP code. ie: var initialTime = <? echo $remaining; ?>;

var seconds = initialTime - myDate-(86400*3);
function timer() {
    var days        = Math.floor(seconds/24/60/60);
    var hoursLeft   = Math.floor((seconds) - (days*86400));
    var hours       = Math.floor(hoursLeft/3600);
    var minutesLeft = Math.floor((hoursLeft) - (hours*3600));
    var minutes     = Math.floor(minutesLeft/60);
    var remainingSeconds = Math.round(seconds % 60);
    if (remainingSeconds < 10) {
        remainingSeconds = "0" + remainingSeconds; 
    }
   // document.getElementById('demo').innerHTML = days + " days " + hours + " hours remaining to download the video";
    if (seconds == 0) {
        clearInterval(countdownTimer);
        document.getElementById('demo').innerHTML = "Completed";
    } else {
        seconds--;
    }
	if(hours=0)
	{
		document.getElementById('demo').innerHTML = minutes + " minutes remaining to download the video"; //displays the timer at the specified element 'demo'

	}
	if(days==0)
	{
		document.getElementById('demo').innerHTML = hours + " hours remaining to download the video";
	}
	if(days>0)
	{
		document.getElementById('demo').innerHTML = days + " days remaining to download the video";
	}
}
var countdownTimer = setInterval('timer()', 100);

</script>



 <script>
 function shortenlink(){
let linkRequest = {
  destination: window.location.href,
  domain: { fullName: "rebrand.ly" }
  //, slashtag: "A_NEW_SLASHTAG"
  //, title: "Rebrandly YouTube channel"
}

let requestHeaders = {
  "Content-Type": "application/json",
  "apikey": "", //need to sign up for a rebrandly account and get an api key from them
  "workspace": ""
}

$.ajax({
  url: "https://api.rebrandly.com/v1/links",
  type: "post",
  data: JSON.stringify(linkRequest),
  headers: requestHeaders,
  dataType: "json",
  success: (link) => {
	 // alert("Success");
    console.log(`Long URL was ${link.destination}, short URL is ${link.shortUrl}`);
	var myLink = JSON.stringify(link.shortUrl);
	var formatted = myLink.replace(/\"/g,""); //removes the "" from a shortened link
	//alert(formatted);
	//var test = JSON.stringify(link.shortUrl);
	
	prompt('Please copy the URL:',formatted);
	/*$(document).ready(function(){
		$('[data-toggle="popover"]').popover({
			html:true;*/
	}

});
}

 </script>
 
 
 
<script>
$(function()
{ $('#hidDownload').hide();
   

	$('#reused_form').submit(function(e)
      {
        e.preventDefault();
		
        $form = $(this);
        //show some response on the button
        $('button[type="submit"]', $form).each(function()
        {
            $btn = $(this);
            $btn.prop('type','button' );
            $btn.prop('orig_label',$btn.text());
            $btn.text('Sending ...');
        });
		<?php $thisID = $_GET['videopageid']; ?>

                    $.ajax({
				url: 'php/handler.php?videopageid=<?php echo($thisID);?>',
                type: "post",
                data: $form.serialize(),
                success: function(response){
					 $('form#reused_form').hide();
					$('#success_message').show();
					$('#error_message').hide();
				},
				error: function(jqXHR,textStatus,errorThrown){
				
				alert('Error',errorThrown);
				}//after_form_submitted(),,
               
			
            });

      });
});
</script>



</body>

</html>
