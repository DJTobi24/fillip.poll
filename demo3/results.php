<?php 
include('config/header.php');
?>
<title>phpzag.com : Demo Poll and Voting System with PHP, MySQL & jQuery</title>
<style>

</style>
<?php include('config/container.php');?>
<div class="container">
	<h2>Poll and Voting System with PHP, MySQL & jQuery</h2>	
	<?php
	include ('Poll.php');        
	$poll = new Poll();
	$pollData = $poll->getPoll();
	foreach($pollData as $poll) {
		echo "<h3>".$poll['question']."</h3>"; 		
		$pollOptions = explode("||||", $poll['options']);
		$votes = explode("||||", $poll['votes']);
		for( $i = 0; $i<count($pollOptions); $i++ ) {
			$votePercent = '0%';
			if($votes[$i] && $poll['voters']) {
				$votePercent = round(($votes[$i]/$poll['voters'])*100);
				$votePercent = !empty($votePercent)?$votePercent.'%':'0%';	
			}			
			echo '<h5>'.$pollOptions[$i].'</h5>';
			echo '<div class="progress">';
			echo '<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:'.$votePercent.'">'.$votePercent.'</div></div>';
		}
	} 
	?>	
	<a class="btn btn-default read-more" href="index.php">Back to Poll</a>	
</div>
<?php include('config/footer.php');?>