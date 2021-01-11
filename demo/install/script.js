$(function(){
$('#loadin').hide();
 $('#stp1').click(function(){
 	$('#loadin').show();
		$('#content_middle').load('step2.php',function(){	
 		$('#loadin').hide();
 	});
 });
 
  $('#stp2').live('click',function(){
  	if($('#warning').hasClass('warning')){return false;}
  	$('#loadin').show();
 	$('#gCtr').load('step3.php',function(){
 		$('#loadin').hide();
 	});
 });
 
   $('#stp3').live('click',function(){
   	$('#loadin').show();
   	$.post('process.php',$('#form3').serialize(),function(d){
   		$('#loadin').hide();
   		$('#gCtr').html('<span id="loadin">Loading <img src="image/loading.gif" /></span>');
   		$('#loadin').remove();
   		if(d=='Congratulation !,The poll has complete the data base installation.'){
   			$('#loadin').show();
   			$('#gCtr').load('step4.php',function(){
   				$('#loadin').hide();
   			});
   		}else{
   			$('#loadin').show();
   			$.post('step3.php',{'e':d},function(d){
   				$('#loadin').hide();
	   			$('#gCtr').append(d);
   			});
   		}
   	})
 });
 
    $('#modeB').live('click',function(){
   		$('.div4').show(500);
   	})
   	
   	 $('#modeA').live('click',function(){
   		$('.div4').hide(500);
   	})
});