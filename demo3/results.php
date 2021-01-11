<?php
include ('config/Poll.php');
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