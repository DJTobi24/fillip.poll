$(function() {

	var path = "http://my-website.com/smooth_poll/"; /*  the url to the smooth_poll folder (don't forget about the trailing slash at the end)  */
												/*  example :  "http://www.my-website.com/smooth_poll/" */

	var a = $('<a>', { href:path } )[0];
	path = a.pathname;
    $('.pollWrapper').each(function(){
	    var ref = $(this).attr('id');
	    var loader = $('#l-'+ref);
	    var pollcontainer = $('#d-'+ref);
    
    	loader.fadeIn();
	    $.post(path+'poll.php',{auth:'y',r:ref},function(d){
	        pollcontainer.each(function(){
	        	$(this).html(d)
	        	animateResults(ref);
	    	});
		    
		    submitForm(ref);
	         
		    $(document).on('click','#viewresult-'+ref, function() {
		        viewResult(ref);
		        return false;
	   		 });
	   		 
	    	$(document).on('click','#vote-'+ref,function() {
	            vote(ref);
	            return false;
	        });

	        styleIt(ref);

	        $('#'+ref).on('mouseout',function(){$('#thxMsg-'+ref).hide(800);});
	        loader.fadeOut();
	    });
	    
	    $(this).on('click','#go-'+ref,function() {
	        loader.fadeIn();
	        var resu = $('#resu-'+ref).val();
	        var ssap = $('#ssap-'+ref).val();
	        $.post(path+'poll.php', { auth: 'y',u: resu, p: ssap,r:ref },function(d) {
	            pollcontainer.fadeOut(1000,function() {
	                $(this).html(d).fadeIn();
	                styleIt(ref);
	                loader.fadeOut();
	            });
	        })
	    });
	    
	    $(this).on('click','#logout-'+ref,function() {
	        loader.fadeIn();
	        $.post(path+'poll.php', {cmd: 'getMeOut', auth: 'y',r:ref},function(d) {
	            pollcontainer.fadeOut(1000,function(){
	                $(this).html(d).fadeIn();
	                styleIt(ref);
	                loader.fadeOut();
	            });
	        });
	    });
	    
		$(this).on('click','#lang-'+ref,function(){
			var elem=$(this).offset();
			$(this).next().css({'top':elem.top+22,'left':elem.left});
			$(this).next().toggle(200);
			return false;
		});
		
		$(this).on('click','#dd-'+ref+' .langz',function(){	
			var language=$(this).text();
			if(!confirm('You are going to change the poll language to'+language+', Are you sure ?'))
			{$('#dd-'+ref).toggle(400);return false;}
			pollcontainer.fadeOut(500);
			loader.fadeIn();
			$.post(path+'poll.php',{cmd:'changeLang',r:ref,l:language,auth:'y'},function(d){
				pollcontainer.html(d).fadeIn(function(){
		                 loader.fadeOut();
		            }
            	);
			})
			return false;
		})
		
	    $(this).on('click','#menu-'+ref,function() {
	        $('#'+ref+' .adBtn').show(500);
	        $('#'+ref+' .open').each(function() {
	            $(this).toggle(500);
	            $(this).toggleClass('open');
	        })
	        return false;
	    });
	    
	    
	    $(this).on('click','#newPoll-'+ref,function() {
	        $('#np-'+ref).toggle(500);
	        $('#np-'+ref).toggleClass('open');
	        $('#'+ref+' .adBtn').hide(500);
	        autocomplete();
	    });
	    
	    
	 	$(this).on('click','#addPollOpt-'+ref,function() {
	        var optNum = ($('#'+ref+' .newPoll').find('.pollOptions').length) + 1;
	        var newOpt = $('<tr><td></td><td><input type="text" class="txtInp pollOptions" name="pollOpt[]" />' + optNum + '</td></tr>');
	        var addOpt = $('#addPollOpt-'+ref);
	        newOpt.insertBefore(addOpt.parent().parent());
	        return false;
	    });
	    
	    
	     $(this).on('click','#rmvPollOpt-'+ref,function() {
	        var index = $('#'+ref+' .newPoll').find('.pollOptions').length;
	        if (index > 2) {
	            $(this).parent().parent().prev().prev().remove();
	        } else {
	            alert('At least two options should be kept');
	        };
	        return false;
	    });
	    
	    $(this).on('click','#publish-'+ref,function(){
	        var empty = 0;
	        $('#'+ref+' #pollTable input:not(input[name=pollVote])').each(function() {
	            if ($(this).val() == '') {
	                empty = 1;
	            };
	        });
	        
	        if (empty) {
	            alert('Empty field(s) have been detected please fill them in , and try again !');
	            return false;
	        }
	        
	        var area=$('#areaOption-'+ref).val();
	        if (area == '0') {
	        	alert('Please select a location to display the poll with in');
	        	return false;
	        };
	        var exclusive=$('#exclusivePoll').val();
	        var show='n';
	        var save = $(this).attr('name');
	        var qId = '';
	        if (save == 'save') {
	            qId = $(this).attr('class');
	        }
	        var opts = Array();
	        var votes = Array();
	        var multiple=$('#'+ref+' .newPoll').find('select[name=multiple]').val();
	        var question = $('#'+ref+' .newPoll').find('input[name=pollQues]').val();
	        var pollStart = $('#'+ref+' .newPoll').find('input[name=poll_start]').val();
	        var pollEnd = $('#'+ref+' .newPoll').find('input[name=poll_end]').val();
	        if (!save) {
	            var tableData = $('#'+ref+' #pollTable').html();
	        }
	        $('#'+ref+' .newPoll').find('.pollOptions').each(function(i) {
	            opts[i] = $(this).val();
	        });
	        $('#'+ref+' .newPoll').find('input[name=pollVote]').each(function(i){
	        	if($.trim($(this).val())=='Original'){
	        		votes[i] = $.trim($(this).val());	
	        	}else{
	        		votes[i] = parseInt($.trim($(this).val()));		
		        	if( isNaN(votes[i])){
		            	empty=1;
		        	}
	        	}
	        });
	        if(empty){
	        	alert('votes value should be a number !');
	        	return false;
	        }
	        
	        loader.fadeIn();
	        $.post(path+'poll.php', {cmd: 'publish',x: exclusive, ps:pollStart, pe:pollEnd, q: question, o: opts,v:votes,m:multiple, s: save,quesId: qId,r:ref,ar: area,auth:'y'},function(d) {
	            $('#'+ref+' #pollTable').fadeOut(500,function(){
	                $('#'+ref+' #pollTable').html(d).fadeIn(500,function() {
	                    if (!save) {
	                        setTimeout(function() {$('#pollTable').html(tableData);},3000)
	                    };
	                    loader.fadeOut();
	                });
	            })
	        });
	        return false;
	    });
	    
	    $(this).on('click','#editPoll-'+ref,function() {
	        $('#ep-'+ref).toggle(500);
	        $('#ep-'+ref).toggleClass('open');
	        $('#'+ref+' .adBtn').hide(500);
	    });
	    
	    $(document).on('click','#'+ref+' .qList',function() {
	        var id = $(this).attr('id');
	        if (id != null && id != undefined && id != '') {
	            loader.fadeIn();
	            $.post(path+'poll.php', {cmd: 'editPoll',qId: id, r:ref,auth: 'y'},function(d) {
	                $('#'+ref+' #d-'+ref).fadeOut(500,function() {
	                    $('#'+ref+' #d-'+ref).html(d).fadeIn(function() {
	                        $('#'+ref+' .newPoll').toggle(500);
	                        loader.fadeOut();
	                        autocomplete();
	                    });
	                })
	            });
	        }
	        return false;
	    });
	    
	    $(document).on('click','#delPoll-'+ref,function() {
	        $('#dp-'+ref).toggle(500);
	        $('#dp-'+ref).toggleClass('open');
	        $('#'+ref+' .adBtn').hide(500);
	    });
	    
	    
	     $(this).on('click','#newArea-'+ref,function() {
	        $('#na-'+ref).toggle(500);
	        $('#na-'+ref).toggleClass('open');
	        $('#'+ref+' .adBtn').hide(500);
	    });
	    
	    $(this).on('click','#addArea-'+ref,function() {
	    	var newArea = $('<tr class="rvabl"><td></td><td><input type="text" class="txtInp pollArea" name="pollArea[]" /></td></tr>');
	        var addArea = $('#addArea-'+ref);
	        newArea.insertBefore(addArea.parent().parent());
	        return false;
	    });
	    
	    
	     $(this).on('click','#rmvArea-'+ref,function() {
	        $(this).parent().parent().prev().prev('.rvabl').remove();
	        return false;
	    });
	    
	    
	    $(this).on('click','#pollAreaTable .areaRmv',function(e){
	    	e.preventDefault();
	    	var id=$(this).attr('id');
	    	$(this).parent().parent().remove();
	    	 $.post(path+'poll.php', {cmd: 'delArea', aid:id,auth:'y'});
	    	
	    });
	    
	    $(this).on('click','#saveArea-'+ref,function(){
	        var empty = 0;
	        var areas = Array();
	        var i=0;
	        $('#'+ref+' #pollAreaTable .pollArea').each(function() {
	            if ($(this).val() == '') {
	                empty = 1;
	            }else{
	            	 areas[i] = $(this).val();
	            	 i++;
	            };
	        });
	        if (empty) {
	            alert('Empty field(s) have been detected please fill them in , and try again !');
	            return false;
	        }
	        var tableData = $('#'+ref+' #pollAreaTable').html();
	         loader.fadeIn();
	        $.post(path+'poll.php', {cmd: 'saveArea', a: areas,auth:'y'},function(d) {
	            $('#'+ref+' #pollAreaTable').fadeOut(500,function(){
	                $('#'+ref+' #pollAreaTable').html(d).fadeIn(500,function() {
                    	setTimeout(function() {$('#pollAreaTable').html(tableData);},4000)
	                    loader.fadeOut();
	                });
	            })
	        });
	        return false;
	    });
	    

	    $(document).on('click','#'+ref+' .delList',function() {
	        if (!confirm('You are going to delete this poll,Are you sure ?')) {
	            return false;
	        }
	        var id = $(this).attr('id');
	        if (id != null && id != undefined && id != '') {
	            var ques = $(this).text();
	            loader.fadeIn();
	            $.post(path+'poll.php', {cmd: 'delPoll',q: ques,qId: id,r:ref,auth: 'y'},function(d) {
	                $('#'+ref+' #d-'+ref).fadeOut(500,function() {
	                    $('#'+ref+' #d-'+ref).html(d).fadeIn(function() {
	                        loader.fadeOut();
	                    });
	                })
	            });
	        }
	        return false;
	    });
	    
        $(this).on('click','#nimda_set-'+ref,function() {
	        $('#as-'+ref).toggle(500);
	        $('#as-'+ref).toggleClass('open');
	        $('#'+ref+' .adBtn').hide(500);
	    });
	    
	     $(this).on('click','#nimda_logDet-'+ref,function() {
	        $('#ld-'+ref).toggle(500);
	        $('#ld-'+ref).toggleClass('open');
	        $('#'+ref+' .adBtn').hide(500);
	    });
	    
	     $(this).on('click','#changeData-'+ref, function(){
	        var prev = $('input[name=prev_mode]:checked').val();
	        var exp = $('#exp-'+ref).val();
	        var area = $('#areaSet-'+ref).val();
	        if(exp==''){
	        	alert('Set how many minutes should the user wait till (s)he can vote again !');
	        	return false;
	        }
	        if(prev==undefined){
	        	alert('Please choose a preview mode !');
	        	return false;
	        }
	        loader.fadeIn();
	        $.post(path+'poll.php', { cmd: 'changeData', pr:prev,x:exp,a:area,r:ref,auth: 'y'}, function(d) {
	            $('#'+ref+' #d-'+ref).fadeOut(500,function() {
	                $('#'+ref+' #d-'+ref).html(d).fadeIn(function() {
	                    loader.fadeOut();
	                });
	            })
	        });
	        return false;
	    });
	    
	    $(this).on('click','#changeLogin-'+ref, function(){
	        var resu = $('#resu-'+ref).val();
	        var ssap = $('#ssap-'+ref).val();
	        loader.fadeIn();
	        $.post(path+'poll.php', { cmd: 'changeLogin', u: resu, p: ssap,r:ref,auth: 'y'}, function(d) {
	            $('#'+ref+' #d-'+ref).fadeOut(500,function() {
	                $('#'+ref+' #d-'+ref).html(d).fadeIn(function() {
	                    loader.fadeOut();
	                });
	            })
	        });
	        return false;
	    });
	    
	    $(this).on('click','#design-'+ref,function() {
	        $('#ds-'+ref).toggle(500);
	        $('#ds-'+ref).toggleClass('open');
	        $('#'+ref+' .adBtn').hide(500);
	    });
	    
	    
	    $(this).on('click','#colorSelector-'+ref,function() {
	        $(this).ColorPicker({
	            color: '#e6e6e6',
	            onShow: function(colpkr) {
	                $(colpkr).fadeIn(500);
	                return false;
	            },
	            onHide: function(colpkr) {
	                $(colpkr).fadeOut(500);
	                return false;
	            },
	            onChange: function(hsb, hex, rgb) {
	                $('#colorSelector-'+ref+' div').css('backgroundColor', '#' + hex);
	                $('#'+ref).css('backgroundColor', '#' + hex);
	            }
	        });
	    });
	    
	    
	    $(this).on('click','#colorSelector2-'+ref,function() {
	        $(this).ColorPicker({
	            color: '#e6e6e6',
	            onShow: function(colpkr) {
	                $(colpkr).fadeIn(500);
	                return false;
	            },
	            onHide: function(colpkr) {
	                $(colpkr).fadeOut(500);
	                return false;
	            },
	            onChange: function(hsb, hex, rgb) {
	                $('#colorSelector2-'+ref+' div').css('backgroundColor', '#' + hex);
	                $('#'+ref).css('color', '#' + hex);
	            }
	        });
	    });
	    
	    
	    $(this).on('click','#colorSelector3-'+ref,function() {
	        $(this).ColorPicker({
	            color: '#e6e6e6',
	            onShow: function(colpkr) {
	                $(colpkr).fadeIn(500);
	                return false;
	            },
	            onHide: function(colpkr) {
	                $(colpkr).fadeOut(500);
	                return false;
	            },
	            onChange: function(hsb, hex, rgb) {
	                $('#colorSelector3-'+ref+' div').css('backgroundColor', '#' + hex);
	            }
	        });
	    });
	    
	    
	    
	     $(this).on('click','#reset-'+ref,function() {
	     	var val=$('#areaToStyle-'+ref).val();
	     	if(val==undefined){
	     		alert('Please define what area you do want to style !');
	     		return false;
	     	}
	        if (!confirm('You are going to style the poll with the default design,are you sure ?')) {
	            return false;
	        }
	        loader.fadeIn();
	        $.post(path+'poll.php', { cmd: 'resetStyle',r:ref,v:val,auth: 'y'}, function(d) {
	            $('#'+ref+' #d-'+ref).fadeOut(500,function() {
	                $(this).html(d).fadeIn(function() {
	                    loader.fadeOut();
	                });
	            })
	        });
	        return false;
	    });
    
	    $(document).on('change','#width-'+ref,function(){
	     	var width = $('#width-'+ref).val();
	     	$('#'+ref).css('width',width);
	 	});
    
	    $(this).on('click','#saveStyle-'+ref,function(){
	    	var areaVal=$('#areaToStyle-'+ref).val();
	     	if(areaVal==undefined){
	     		alert('Please define what area you do want to style !');
	     		return false;
	     	}
	        var val = $('#'+ref+' input[name=buttonStyle]:checked').val();
	        var trans = $('#noPollBG-'+ref+':checked').val();
	        var bg = $('#colorSelector-'+ref+' div').attr('style');
	        var fg = $('#colorSelector2-'+ref+' div').attr('style');
	        var vg = $('#colorSelector3-'+ref+' div').attr('style');
	        var width = $('#width-'+ref).val();
	        if(trans!='y'){trans='n';}
	        if (val == undefined || val == '' || val == null) {
	            alert('Choose a radio button style !');
	        } else {
	            loader.fadeIn();
	            $.post(path+'poll.php', {cmd:'changeStyle',radStyle: val,t:trans,b:bg,f:fg,v:vg,w:width,r:ref,av:areaVal,auth:'y'},
	            function(d) {
	                $('#'+ref+' #d-'+ref).fadeOut(500,function() {
	                    $(this).html(d).fadeIn(function() {
	                        loader.fadeOut();
	                    });
	                })
	            });
	        }
	        return false;
	    });
	    
	    $(this).on('click','#noPollBG-'+ref,function(){
	    	if($(this).is(':checked')){
	    		$('.pollWrapper').css('background','transparent');
	    	}else{
	    		var bg = $('#colorSelector-'+ref+' div').attr('style');
	    		bg=bg.split(':')[1].split(';')[0];
	    		$('.pollWrapper').css('background',bg);
	    	}
	    });
	    
	    $(this).on('click','#stats-'+ref,function() {
	        $('#st-'+ref).toggle(500);
	        $('#st-'+ref).toggleClass('open');
	        $('#'+ref+' .adBtn').hide(500);
	    });
    
	    $(document).on('click','#'+ref+' .statList',function(){
	        var typo = $(this).attr('id');
	        if (typo != null && typo != undefined && typo != ''){
	            loader.fadeIn();
	            $.post(path+'poll.php', {cmd:'stats',type: typo,r:ref,auth:'y' }, function(d){
	                $('#d-'+ref).fadeOut(500, function() 
	                {
	                    $(this).html(d).fadeIn(function() {
	                        loader.fadeOut();
	                    });
	                })
	            });
	        }
	        return false;
	    })
	    
		jQuery.download = function(url, data){
			//url and data options required
			if( url && data ){ 
	        	loader.fadeIn();
				//data can be string of parameters or array/object
				data = typeof data == 'string' ? data : jQuery.param(data);
				//split params into form inputs
				var inputs = '';
				jQuery.each(data.split('&'), function(){ 
					var pair = this.split('=');
					inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
				});
				//send request
				jQuery('<form action="'+ url +'" method="post">'+inputs+'</form>').appendTo('body').submit().remove();
				loader.fadeOut();
				
			};
		};

    
	    $(this).on('click','#csv-'+ref,
	    function() {
	        $('#dcsv-'+ref).toggle(500);
	        $('#dcsv-'+ref).toggleClass('open');
	        $('#'+ref+' .adBtn').hide(500);
	    });
    
	    $(this).on('click','.csvList',function() {
	        var id = $(this).attr('id');
	        if (id != null && id != undefined && id != ''){
	        	$.download(path+'includes/export.php','t=csv&q='+id );
	        }
	        return false;
	    });
    
	    $(this).on('click','#xml-'+ref,function() {
	        $('#dxml-'+ref).toggle(500);
	        $('#dxml-'+ref).toggleClass('open');
	        $('#'+ref+' .adBtn').hide(500);
	    });
	    
	    $(this).on('click','.xmlList',function() {
	        var id = $(this).attr('id');
	        if (id != null && id != undefined && id != '')
	        {
	        	$.download(path+'includes/export.php','t=xml&q='+id );
	        }
	        return false;
	    });
    
	    $(this).on('click','#pdf-'+ref,function() {
	        $('#dpdf-'+ref).toggle(500);
	        $('#dpdf-'+ref).toggleClass('open');
	        $('#'+ref+' .adBtn').hide(500);
	    });
	});

	function submitForm(ref) {
		var loader = $('#l-'+ref);
	    var pollcontainer = $('#d-'+ref);
	    var pollForm=$('#'+ref+' .pollform');
	    pollForm.on('submit',function(){
	        var selected_items = $(this).find('input[name=option]:checked');
	        var qId = $(this).find('input[name=question_id]').val();
	        if (selected_items.length>0){
	        	var voted_on=new Array();
	        	for (var i = selected_items.length - 1; i >= 0; i--) {
	        		var id=$(selected_items[i]).attr('id').split('-')[1];
	        		voted_on.push(id);
	        	};
	            loader.fadeIn();
	            $.post(path+'poll.php',{ q:qId, o: voted_on, r:ref, auth: 'y'},function(d) {
	                $('#formcontainer-'+ref).fadeOut(100,function() {
	                    $(this).html(d);
	                    $(this).fadeIn(500);
	                    animateResults(ref);
	                    $('#'+ref).on('mouseout',function(){$('#thxMsg-'+ref).hide(800);});
	                    loader.fadeOut();
	                });
	            });
	        } else{
		            alert('Select an option !');
		        }
	        return false;
	    });
	};

	function viewResult(ref) {
			var loader =$('#l-'+ref);
		    var pollcontainer =$('#d-'+ref);
		    loader.fadeIn();
		    $.post(path+'poll.php', {result:'1',auth: 'y',r:ref},function(d) {
		        pollcontainer.fadeOut(1000,function() {
		            $(this).html(d).fadeIn();
		            animateResults(ref);
		            loader.fadeOut();
		        });
		    });
	}
	function vote(ref) {
		var loader =$('#l-'+ref);
	    var pollcontainer =$('#d-'+ref);
	    loader.fadeIn();
	    $.post(path+'poll.php', {auth: 'y',r:ref},function(d) {
	        pollcontainer.html(d);
	        styleIt(ref);
	        loader.fadeOut();
	    });
	}

});

function animateResults(ref) {
    $('#'+ref+' .bar').each(function() {
        var bar_width = $(this).css('width');
        $(this).css('width', '0').animate({
            width: bar_width
        },
        1000);
    });
}

function styleIt(ref) {
	replace(ref);
	// if ($.browser.msie==1 && parseInt($.browser.version, 10) < 8) {
	// 	return false;
	// }else{replace(ref);}
    var oldId='';
    $('#'+ref+' .radRep').each(function() {
        $(this).click(function() {
            var id = $(this).attr('data-repId');
            if(oldId!=id){
            	oldId=id;
	            $('#'+ref+' .radRep:not(this)').removeClass('checked');
	            var check = $('#' + id).attr('checked');
	            if (!check) {
	                $('#' + id).attr('checked', 'checked');
	            } else {
	                $('#' + id).removeAttr('checked');
	            };
	            $(this).toggleClass('checked');
            }
        });
         $(this).next().next().click(function() {
            $(this).prev().prev().click();
        })
    });
    
    $('#'+ref+' .chkRep').each(function() {
    	var label=$(this).next().next();
    	var realInp=$(this).next();
    	var fakeInp=$(this);
        fakeInp.click(function() {
        	var id = $(this).attr('data-repId');
        	var check = $('#' + id).attr('checked');
            if (!check) {
                $('#' + id).attr('checked', 'checked');
                $(this).toggleClass('checked');
            } else {
                $('#' + id).removeAttr('checked');
                $(this).toggleClass('checked');
            };
        });
        realInp.bind('change',function(){
			$(this).prev().toggleClass('checked');
		})
		/*if($.browser.msie){
			label.click(function(){
				fakeInp.click();
			});
		}*/
    });
}

function replace(ref) {
    $('#'+ref+' input[name=option]').each(function() {
        $(this).css('display', 'none');
        var classo='';
        var type=$(this).attr('type');
        switch(type){
        	case'radio':
        		classo='radRep';
        	break;
        	case'checkbox':
        		classo='chkRep';
        	break;
        }
        var id = $(this).attr('id');
        $('<div data-repId=' + id + ' class="'+classo+'"></div>').insertBefore(this);
    })
}

function autocomplete () {
	var countries=new Array("<img src='images/flags/xx.gif'/> Anybody","<img src='images/flags/ad.gif'/> Andorra","<img src='images/flags/ae.gif'/> United Arab Emirates","<img src='images/flags/af.gif'/> Afghanistan","<img src='images/flags/ag.gif'/> Antigua and Barbuda","<img src='images/flags/ai.gif'/> Anguilla","<img src='images/flags/al.gif'/> Albania","<img src='images/flags/am.gif'/> Armenia","<img src='images/flags/an.gif'/> Netherlands Antilles","<img src='images/flags/ao.gif'/> Angola","<img src='images/flags/ar.gif'/> Argentina","<img src='images/flags/as.gif'/> American Samoa","<img src='images/flags/at.gif'/> Austria","<img src='images/flags/au.gif'/> Australia","<img src='images/flags/aw.gif'/> Aruba","<img src='images/flags/ax.gif'/> Aland Islands","<img src='images/flags/az.gif'/> Azerbaijan","<img src='images/flags/ba.gif'/> Bosnia and Herzegovina","<img src='images/flags/bb.gif'/> Barbados","<img src='images/flags/bd.gif'/> Bangladesh","<img src='images/flags/be.gif'/> Belgium","<img src='images/flags/bf.gif'/> Burkina Faso","<img src='images/flags/bg.gif'/> Bulgaria","<img src='images/flags/bh.gif'/> Bahrain","<img src='images/flags/bi.gif'/> Burundi","<img src='images/flags/bj.gif'/> Benin","<img src='images/flags/bm.gif'/> Bermuda","<img src='images/flags/bn.gif'/> Brunei Darussalam","<img src='images/flags/bo.gif'/> Bolivia","<img src='images/flags/br.gif'/> Brazil","<img src='images/flags/bs.gif'/> Bahamas","<img src='images/flags/bt.gif'/> Bhutan","<img src='images/flags/bv.gif'/> Bouvet Island","<img src='images/flags/bw.gif'/> Botswana","<img src='images/flags/by.gif'/> Belarus","<img src='images/flags/bz.gif'/> Belize","<img src='images/flags/ca.gif'/> Canada","<img src='images/flags/cc.gif'/> Cocos (Keeling) Islands","<img src='images/flags/cd.gif'/> Democratic Republic of the Congo","<img src='images/flags/cf.gif'/> Central African Republic","<img src='images/flags/cg.gif'/> Congo","<img src='images/flags/ch.gif'/> Switzerland","<img src='images/flags/ci.gif'/> Cote D'Ivoire (Ivory Coast)","<img src='images/flags/ck.gif'/> Cook Islands","<img src='images/flags/cl.gif'/> Chile","<img src='images/flags/cm.gif'/> Cameroon","<img src='images/flags/cn.gif'/> China","<img src='images/flags/co.gif'/> Colombia","<img src='images/flags/cr.gif'/> Costa Rica","<img src='images/flags/cs.gif'/> Serbia and Montenegro","<img src='images/flags/cu.gif'/> Cuba","<img src='images/flags/cv.gif'/> Cape Verde","<img src='images/flags/cx.gif'/> Christmas Island","<img src='images/flags/cy.gif'/> Cyprus","<img src='images/flags/cz.gif'/> Czech Republic","<img src='images/flags/de.gif'/> Germany","<img src='images/flags/dj.gif'/> Djibouti","<img src='images/flags/dk.gif'/> Denmark","<img src='images/flags/dm.gif'/> Dominica","<img src='images/flags/do.gif'/> Dominican Republic","<img src='images/flags/dz.gif'/> Algeria","<img src='images/flags/ec.gif'/> Ecuador","<img src='images/flags/ee.gif'/> Estonia","<img src='images/flags/eg.gif'/> Egypt","<img src='images/flags/eh.gif'/> Western Sahara","<img src='images/flags/er.gif'/> Eritrea","<img src='images/flags/es.gif'/> Spain","<img src='images/flags/et.gif'/> Ethiopia","<img src='images/flags/fi.gif'/> Finland","<img src='images/flags/fj.gif'/> Fiji","<img src='images/flags/fk.gif'/> Falkland Islands (Malvinas)","<img src='images/flags/fm.gif'/> Federated States of Micronesia","<img src='images/flags/fo.gif'/> Faroe Islands","<img src='images/flags/fr.gif'/> France","<img src='images/flags/ga.gif'/> Gabon","<img src='images/flags/gd.gif'/> Grenada","<img src='images/flags/ge.gif'/> Georgia","<img src='images/flags/gf.gif'/> French Guiana","<img src='images/flags/gh.gif'/> Ghana","<img src='images/flags/gi.gif'/> Gibraltar","<img src='images/flags/gl.gif'/> Greenland","<img src='images/flags/gm.gif'/> Gambia","<img src='images/flags/gn.gif'/> Guinea","<img src='images/flags/gp.gif'/> Guadeloupe","<img src='images/flags/gq.gif'/> Equatorial Guinea","<img src='images/flags/gr.gif'/> Greece","<img src='images/flags/gs.gif'/> S. Georgia and S. Sandwich Islands","<img src='images/flags/gt.gif'/> Guatemala","<img src='images/flags/gu.gif'/> Guam","<img src='images/flags/gw.gif'/> Guinea-Bissau","<img src='images/flags/gy.gif'/> Guyana","<img src='images/flags/hk.gif'/> Hong Kong","<img src='images/flags/hm.gif'/> Heard Island and McDonald Islands","<img src='images/flags/hn.gif'/> Honduras","<img src='images/flags/hr.gif'/> Croatia (Hrvatska)","<img src='images/flags/ht.gif'/> Haiti","<img src='images/flags/hu.gif'/> Hungary","<img src='images/flags/id.gif'/> Indonesia","<img src='images/flags/ie.gif'/> Ireland","<img src='images/flags/il.gif'/> Israel","<img src='images/flags/in.gif'/> India","<img src='images/flags/io.gif'/> British Indian Ocean Territory","<img src='images/flags/iq.gif'/> Iraq","<img src='images/flags/ir.gif'/> Iran","<img src='images/flags/is.gif'/> Iceland","<img src='images/flags/it.gif'/> Italy","<img src='images/flags/jm.gif'/> Jamaica","<img src='images/flags/jo.gif'/> Jordan","<img src='images/flags/jp.gif'/> Japan","<img src='images/flags/ke.gif'/> Kenya","<img src='images/flags/kg.gif'/> Kyrgyzstan","<img src='images/flags/kh.gif'/> Cambodia","<img src='images/flags/ki.gif'/> Kiribati","<img src='images/flags/km.gif'/> Comoros","<img src='images/flags/kn.gif'/> Saint Kitts and Nevis","<img src='images/flags/kp.gif'/> Korea (North)","<img src='images/flags/kr.gif'/> Korea (South)","<img src='images/flags/kw.gif'/> Kuwait","<img src='images/flags/ky.gif'/> Cayman Islands","<img src='images/flags/kz.gif'/> Kazakhstan","<img src='images/flags/la.gif'/> Laos","<img src='images/flags/lb.gif'/> Lebanon","<img src='images/flags/lc.gif'/> Saint Lucia","<img src='images/flags/li.gif'/> Liechtenstein","<img src='images/flags/lk.gif'/> Sri Lanka","<img src='images/flags/lr.gif'/> Liberia","<img src='images/flags/ls.gif'/> Lesotho","<img src='images/flags/lt.gif'/> Lithuania","<img src='images/flags/lu.gif'/> Luxembourg","<img src='images/flags/lv.gif'/> Latvia","<img src='images/flags/ly.gif'/> Libya","<img src='images/flags/ma.gif'/> Morocco","<img src='images/flags/mc.gif'/> Monaco","<img src='images/flags/md.gif'/> Moldova","<img src='images/flags/mg.gif'/> Madagascar","<img src='images/flags/mh.gif'/> Marshall Islands","<img src='images/flags/mk.gif'/> Macedonia","<img src='images/flags/ml.gif'/> Mali","<img src='images/flags/mm.gif'/> Myanmar","<img src='images/flags/mn.gif'/> Mongolia","<img src='images/flags/mo.gif'/> Macao","<img src='images/flags/mp.gif'/> Northern Mariana Islands","<img src='images/flags/mq.gif'/> Martinique","<img src='images/flags/mr.gif'/> Mauritania","<img src='images/flags/ms.gif'/> Montserrat","<img src='images/flags/mt.gif'/> Malta","<img src='images/flags/mu.gif'/> Mauritius","<img src='images/flags/mv.gif'/> Maldives","<img src='images/flags/mw.gif'/> Malawi","<img src='images/flags/mx.gif'/> Mexico","<img src='images/flags/my.gif'/> Malaysia","<img src='images/flags/mz.gif'/> Mozambique","<img src='images/flags/na.gif'/> Namibia","<img src='images/flags/nc.gif'/> New Caledonia","<img src='images/flags/ne.gif'/> Niger","<img src='images/flags/nf.gif'/> Norfolk Island","<img src='images/flags/ng.gif'/> Nigeria","<img src='images/flags/ni.gif'/> Nicaragua","<img src='images/flags/nl.gif'/> Netherlands","<img src='images/flags/no.gif'/> Norway","<img src='images/flags/np.gif'/> Nepal","<img src='images/flags/nr.gif'/> Nauru","<img src='images/flags/nu.gif'/> Niue","<img src='images/flags/nz.gif'/> New Zealand (Aotearoa)","<img src='images/flags/om.gif'/> Oman","<img src='images/flags/pa.gif'/> Panama","<img src='images/flags/pe.gif'/> Peru","<img src='images/flags/pf.gif'/> French Polynesia","<img src='images/flags/pg.gif'/> Papua New Guinea","<img src='images/flags/ph.gif'/> Philippines","<img src='images/flags/pk.gif'/> Pakistan","<img src='images/flags/pl.gif'/> Poland","<img src='images/flags/pm.gif'/> Saint Pierre and Miquelon","<img src='images/flags/pn.gif'/> Pitcairn","<img src='images/flags/pr.gif'/> Puerto Rico","<img src='images/flags/ps.gif'/> Palestinian Territory","<img src='images/flags/pt.gif'/> Portugal","<img src='images/flags/pw.gif'/> Palau","<img src='images/flags/py.gif'/> Paraguay","<img src='images/flags/qa.gif'/> Qatar","<img src='images/flags/re.gif'/> Reunion","<img src='images/flags/ro.gif'/> Romania","<img src='images/flags/ru.gif'/> Russian Federation","<img src='images/flags/rw.gif'/> Rwanda","<img src='images/flags/sa.gif'/> Saudi Arabia","<img src='images/flags/sb.gif'/> Solomon Islands","<img src='images/flags/sc.gif'/> Seychelles","<img src='images/flags/sd.gif'/> Sudan","<img src='images/flags/se.gif'/> Sweden","<img src='images/flags/sg.gif'/> Singapore","<img src='images/flags/sh.gif'/> Saint Helena","<img src='images/flags/si.gif'/> Slovenia","<img src='images/flags/sj.gif'/> Svalbard and Jan Mayen","<img src='images/flags/sk.gif'/> Slovakia","<img src='images/flags/sl.gif'/> Sierra Leone","<img src='images/flags/sm.gif'/> San Marino","<img src='images/flags/sn.gif'/> Senegal","<img src='images/flags/so.gif'/> Somalia","<img src='images/flags/sr.gif'/> Suriname","<img src='images/flags/st.gif'/> Sao Tome and Principe","<img src='images/flags/sv.gif'/> El Salvador","<img src='images/flags/sy.gif'/> Syria","<img src='images/flags/sz.gif'/> Swaziland","<img src='images/flags/tc.gif'/> Turks and Caicos Islands","<img src='images/flags/td.gif'/> Chad","<img src='images/flags/tf.gif'/> French Southern Territories","<img src='images/flags/tg.gif'/> Togo","<img src='images/flags/th.gif'/> Thailand","<img src='images/flags/tj.gif'/> Tajikistan","<img src='images/flags/tk.gif'/> Tokelau","<img src='images/flags/tl.gif'/> Timor-Leste","<img src='images/flags/tm.gif'/> Turkmenistan","<img src='images/flags/tn.gif'/> Tunisia","<img src='images/flags/to.gif'/> Tonga","<img src='images/flags/tr.gif'/> Turkey","<img src='images/flags/tt.gif'/> Trinidad and Tobago","<img src='images/flags/tv.gif'/> Tuvalu","<img src='images/flags/tw.gif'/> Taiwan","<img src='images/flags/tz.gif'/> Tanzania","<img src='images/flags/ua.gif'/> Ukraine","<img src='images/flags/ug.gif'/> Uganda","<img src='images/flags/uk.gif'/> United Kingdom","<img src='images/flags/um.gif'/> United States Minor Outlying Islands","<img src='images/flags/us.gif'/> United States","<img src='images/flags/uy.gif'/> Uruguay","<img src='images/flags/uz.gif'/> Uzbekistan","<img src='images/flags/va.gif'/> Vatican City State (Holy See)","<img src='images/flags/vc.gif'/> Saint Vincent and the Grenadines","<img src='images/flags/ve.gif'/> Venezuela","<img src='images/flags/vg.gif'/> Virgin Islands (British)","<img src='images/flags/vi.gif'/> Virgin Islands (U.S.)","<img src='images/flags/vn.gif'/> Viet Nam","<img src='images/flags/vu.gif'/> Vanuatu","<img src='images/flags/wf.gif'/> Wallis and Futuna","<img src='images/flags/ws.gif'/> Samoa","<img src='images/flags/ye.gif'/> Yemen","<img src='images/flags/yt.gif'/> Mayotte","<img src='images/flags/za.gif'/> South Africa","<img src='images/flags/zm.gif'/> Zambia","<img src='images/flags/zw.gif'/> Zimbabwe");
	var a = $('#exclusivePoll').autocomplete({ 
	minChars:2, 
	delimiter: /(,|;)\s*/, // regex or character
	maxHeight:350,
	width:200,
	zIndex: 9999,
	deferRequestBy: 0, //miliseconds
	params: { country:'Yes' }, //aditional parameters
	noCache: false, //default is false, set to true to disable caching
	onSelect:function(){
		var country= $('#exclusivePoll').val();
		//$('#exclusivePoll').val(country.split('>')[1]);
		var nVal=country.split('flags/')[1].split('.gif')[0];
		$('#exclusivePoll').val(nVal);
	},
	lookup: countries //local lookup values 
	});
}
