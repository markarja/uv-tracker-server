function download() {
	 var free = document.getElementById("free").checked;
	 if(navigator.userAgent.match(/Windows Phone/i)) {
		 window.location = "https://www.windowsphone.com/en-us/store/app/uv-radiation-now/55caa454-3666-42a2-855f-8f777df59f71";
	 } else if(navigator.userAgent.match(/Android/i)) {
		 if(free) {
			 window.location = "https://play.google.com/store/apps/details?id=com.markuskarjalainen.uvradiationnowfree";
		 } else {
			 window.location = "https://play.google.com/store/apps/details?id=com.markuskarjalainen.uvtracker";
		 }
	 } else if(navigator.userAgent.match(/Kindle Fire/)) {
		 window.location = "https://www.amazon.com/Markus-Karjalainen-UV-radiation-now/dp/B010RHMYFY/ref=sr_1_1?ie=UTF8&qid=1435811671&sr=8-1&keywords=uv+radiation+now";
	 } else {
		 if(free) {
			 window.location = "https://itunes.apple.com/app/uv-radiation-now-free/id1173659659?mt=8";
		 } else {
			 window.location = "https://itunes.apple.com/app/uv-radiation-now/id882320475?mt=8";
		 }
	 } 
}

function get(name) {
   if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
      return decodeURIComponent(name[1]);
}

function forward() {
  if(get("redirect") == 1) {
    download();
  }
}