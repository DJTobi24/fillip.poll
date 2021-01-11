<?php 
include('config/header.php');
?>
<title>Voting System mit PHP und MySQL</title>
<style>
ul {
    list-style-type: none;    
	margin: 0;
    padding: 0;
}
ul li {
    margin-bottom:2px;
    padding:2px;
     
}
a:hover, a:active, a:focus, a:visited {
    text-decoration: none;
} 
</style>
<?php include('config/container.php');?>
<div class="container">
	<h2>Voting System mit PHP und MySQL</h2>		
	<?php
	include ('config/Poll.php');        
	$poll = new Poll();
	$pollData = $poll->getPoll();	
	if(isset($_POST['vote'])){
		$pollVoteData = array(
			'pollid' => $_POST['pollid'],
			'pollOptions' => $_POST['options']
		);
		$isVoted = $poll->updateVote($pollVoteData);
		if($isVoted){
			setcookie($_POST['pollid'], 1, time()+60*60*24*365);			
			$voteMessage = 'Your have voted successfully.';
		} else {
			$voteMessage = 'Your had already voted.';
		}
	}
	?>	
	<div class="poll-container">	
		<?php echo !empty($voteMessage)?'<div class="alert alert-danger"><strong>Warning!</strong> '.$voteMessage.'</div>':''; ?>		
		<form action="" method="post" name="pollFrm">	
			<?php 
			foreach($pollData as $poll){
				echo "<h3>".$poll['question']."</h3>"; 				
				$pollOptions = explode("||||", $poll['options']);
				echo "<ul>";
				for( $i = 0; $i < count($pollOptions); $i++ ) {
					echo '<li><input type="radio" name="options" value="'.$i.'" > '.$pollOptions[$i].'</li>';
				}
				echo "</ul>";
				echo '<input type="hidden" name="pollid" value="'.$poll['pollid'].'">';
				echo '<br><input type="submit" name="vote" class="btn btn-primary" value="Vote">';
				echo '<a href="results.php?pollID="'.$poll['pollid']."> View Results</a>";	
			} 
			?>			
		</form>		
	</div>		
</div>
<?php include('config/footer.php');?>