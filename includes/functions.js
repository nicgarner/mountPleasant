function copyToFake(x) {
	var y=document.getElementById("real").value;
	document.getElementById("fake").value=y;
}

function opacity(id, opacStart, opacEnd, millisec) {
	//speed for each frame
	var speed = Math.round(millisec / 100);
	var timer = 0;

	//determine the direction for the blending, if start and end are the same nothing happens
	if(opacStart > opacEnd) {
		for(i = opacStart; i >= opacEnd; i--) {
			setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
			timer++;
		}
	} else if(opacStart < opacEnd) {
		for(i = opacStart; i <= opacEnd; i++)
			{
			setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
			timer++;
		}
	}
	/* var t = setTimeout("document.getElementById('"+id+"').style.display='none'",millisec);*/
}

//change the opacity for different browsers
function changeOpac(opacity, id) {
	var object = document.getElementById(id).style; 
	object.opacity = (opacity / 100);
	object.MozOpacity = (opacity / 100);
	object.KhtmlOpacity = (opacity / 100);
	object.filter = "alpha(opacity=" + opacity + ")";
}

function shiftOpacity(id, millisec) {
	//if an element is invisible, make it visible, else make it invisible
	if(document.getElementById(id).style.opacity == 0) {
		opacity(id, 0, 100, millisec);
	} else {
		opacity(id, 100, 0, millisec);
	}
}

function blendimage(divid, imageid, imagefile, millisec) {
	var speed = Math.round(millisec / 100);
	var timer = 0;
	
	//set the current image as background
	document.getElementById(divid).style.backgroundImage = "url(" + document.getElementById(imageid).src + ")";
	
	//make image transparent
	changeOpac(0, imageid);
	
	//make new image
	document.getElementById(imageid).src = imagefile;

	//fade in image
	for(i = 0; i <= 100; i++) {
		setTimeout("changeOpac(" + i + ",'" + imageid + "')",(timer * speed));
		timer++;
	}
}

function currentOpac(id, opacEnd, millisec) {
	//standard opacity is 100
	var currentOpac = 100;
	
	//if the element has an opacity set, get it
	if(document.getElementById(id).style.opacity < 100) {
		currentOpac = document.getElementById(id).style.opacity * 100;
	}

	//call for the function that changes the opacity
	opacity(id, currentOpac, opacEnd, millisec)
}

function GetXmlHttpObject() {
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		return new XMLHttpRequest();
	}
	if (window.ActiveXObject) {
		// code for IE6, IE5
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
	return null;
}

function updateBlogSubscription(blog_id, user_id) {
	xmlhttp=GetXmlHttpObject();
	if (xmlhttp==null) {
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url="includes/ajax/updateBlogSubscription.php";
	url=url+"?blog_id="+blog_id;
	url=url+"&user_id="+user_id;
	url=url+"&sid="+Math.random();
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4) {
			document.getElementById("subscriptionStatus" + blog_id).innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);	
}

function startUpload(){
	document.getElementById('upload_process').style.visibility = 'visible';
	document.getElementById('upload_form').style.visibility = 'hidden';
	document.getElementById('upload_form_msg').style.visibility = 'hidden';
	return true;
}

function stopUpload(success,name,title,series,speaker,minutes,seconds,filesize,year,month,day,time,comments){
	var result = '';
	if (success == 1){
		result = '<br/><p class="confirm"><b>'+name+'</b> was uploaded successfully. Please check and complete the details below, then click Save Changes to publish the recording.</p>';
		document.getElementById('upload_form_msg').innerHTML = result;
		document.getElementById('upload_form_msg').style.visibility = 'visible';
		document.getElementById('upload_form').style.display = 'none';
		document.getElementById('form').style.display = 'block';
		
		document.getElementById('rname').value = title;
		
		if(series!='') {
			for (var idx=0;idx<document.getElementById('category').options.length;idx++) {
				if (series==document.getElementById('category').options[idx].text) {
					document.getElementById('category').selectedIndex=idx;
					var seriesSet = 1;
				}
			}
			if (seriesSet!=1) {
				var dropdown = document.getElementById('category');
				var newSeries = document.createElement('option');
				newSeries.text = series;
				newSeries.value = 'newSeries';
				dropdown.add(newSeries,dropdown.options[1]);
				dropdown.selectedIndex=1;
				
				var newSeriesName = document.createElement('input');
				newSeriesName.value = series;
				newSeriesName.name = 'newSeriesName';
				newSeriesName.type = 'hidden';
				document.getElementById('category_area').appendChild(newSeriesName);
				var message = document.createTextNode(" Note: A new category will be created.")
				var para = document.createElement("p").appendChild(message);
//				para.class = 'formwarning';
				document.getElementById('category_area').appendChild(para);
				document.getElementById('category_area').setAttribute("class", "formwarning");
				var speakerShort = speaker.substring(0,speaker.indexOf(" "));
				if (speakerShort=="")
					speakerShort = speaker;
				var commentsMsg =  speakerShort + ' begins our new series of ';
			}
			else {
				var commentsMsg = speakerShort + ' continues our series of ';
			}
			if (time == '10:30')
				commentsMsg = commentsMsg + 'morning ';
			if (time == '18:30')
				commentsMsg = commentsMsg + 'evening ';
			commentsMsg = commentsMsg + 'messages called ' + series + '.';
			document.getElementById('comments').value=commentsMsg;
		}
		
		document.getElementById('speaker').value = speaker;
		document.getElementById('reading').value = comments;
		
		for (var idx=0;idx<document.getElementById('day').options.length;idx++) {
			if (day==document.getElementById('day').options[idx].value)
				document.getElementById('day').selectedIndex=idx;
		}
		for (var idx=0;idx<document.getElementById('month').options.length;idx++) {
			if (month==document.getElementById('month').options[idx].value)
				document.getElementById('month').selectedIndex=idx;
		}
		for (var idx=0;idx<document.getElementById('year').options.length;idx++) {
			if (year==document.getElementById('year').options[idx].value)
				document.getElementById('year').selectedIndex=idx;
		}
		for (var idx=0;idx<document.getElementById('time').options.length;idx++) {
			if (time==document.getElementById('time').options[idx].value)
				document.getElementById('time').selectedIndex=idx;
		}
		
		document.getElementById('minutes').value = minutes;
		document.getElementById('seconds').value = seconds;
		document.getElementById('size').value = filesize;
	}
	else {
		result = '<br/><p class="error"><b>There was an error during file upload.</b></p>';
		document.getElementById('upload_form_msg').innerHTML = result;
		document.getElementById('upload_form_msg').style.visibility = 'visible';
		document.getElementById('myfile').value = '';
		document.getElementById('upload_form').style.visibility = 'visible';
	}
	document.getElementById('upload_process').style.visibility = 'hidden';
	return true;   
}

function headlineOver(headlineoff,headlineon) {
	document.getElementById('headline'+headlineoff).style.display = 'none';
	document.getElementById('headline'+headlineon).style.display = 'block';
	window.showingad = headlineon;
}

function switchHeadlines(i,j,k,delay) {
//	alert('switchHeadlines has been called with j='+j+' and k='+k);
	headlineOver(j,k);
	j++; k++;
	if (k>i) k = 1;
	if (j>i) j = 1;
	showingad = j;
	t = setTimeout("switchHeadlines("+i+","+j+","+k+","+delay+")",delay);
//	alert(t);
}

function stopSwitchHeadlines() {
	if(typeof t!="undefined")
		clearTimeout(t);
//	alert('The ad that is showing is number '+showingad);
}

function resumeSwitchHeadlines(i,j,delay) {
	var k = j+1;
	if (k>i) k = 1;
	t = setTimeout("switchHeadlines("+i+","+j+","+k+","+delay+")",delay);
}

function showContent(content) {
//	alert(content);
	document.getElementById('latestmedia').style.display = 'none';
	document.getElementById('thisweek').style.display = 'none';
	document.getElementById('nextweek').style.display = 'none';
	document.getElementById('resources').style.display = 'none';
	
	document.getElementById('navlatestmedia').className = '';
	document.getElementById('navthisweek').className = '';
	document.getElementById('navnextweek').className = '';
	document.getElementById('navresources').className = '';
	
	document.getElementById(content).style.display = 'block';
	document.getElementById('nav'+content).className = 'on';
}