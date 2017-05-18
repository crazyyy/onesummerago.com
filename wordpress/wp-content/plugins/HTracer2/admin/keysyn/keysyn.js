function hks_createRequestObject() {
  if (typeof XMLHttpRequest === 'undefined') {
    XMLHttpRequest = function() {
      try { return new ActiveXObject("Msxml2.XMLHTTP.6.0"); }
        catch(e) {}
      try { return new ActiveXObject("Msxml2.XMLHTTP.3.0"); }
        catch(e) {}
      try { return new ActiveXObject("Msxml2.XMLHTTP"); }
        catch(e) {}
      try { return new ActiveXObject("Microsoft.XMLHTTP"); }
        catch(e) {}
      throw new Error("This browser does not support XMLHttpRequest.");
    };
  }
  return new XMLHttpRequest();
}
var hks_textarea=false;
var hks_btn=false;
var hks_btn_text=false;

function hks_processReqChange()
{	
    if (req.readyState == 3) 
	{
		if(hks_btn)
		{
			hks_btn.value=hks_btn_text;
			hks_btn.disabled=false;
		}	
        if (req.status == 200) 
		{
			try{hks_textarea.innerHTML+="\n"+req.responseText;} catch(e1){ 
			try{hks_textarea.value+="\n"+req.responseText;} catch(e2){}} 
        }
    }
}
function hks_DoRequest(key,count,what,area,btn)
{
	if(!count)
		count=50;
	if(!what)
		what='sape';
	hks_textarea=area;
	hks_btn=btn;
	if(btn)
	{
		hks_btn_text=btn.value;
		btn.value='Ждите ...';
		btn.disabled=true;
	}	
	key=encodeURIComponent(key);
	req=hks_createRequestObject();
	if (req) {       
		var href = hks_href;
		href+='keysyn.php?key='+key+'&ajax=1&what='+what+'&count='+count;
		req.open("GET", href, true);
        req.onreadystatechange = hks_processReqChange;
        req.send(null);
    }
}