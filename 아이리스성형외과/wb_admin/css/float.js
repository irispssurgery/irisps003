	self.onError=null;
	currentX = currentY = 0;  
	whichIt = null;           
	lastScrollX = 0; lastScrollY = 0;
	NS = (document.layers) ? 1 : 0;
	IE = (document.all) ? 1: 0;
	<!-- STALKER CODE -->
	function heartBeat() {
		if(IE) { 
			  diffY = document.body.scrollTop; 
 			  diffX = 0; 
		       }
	    if(NS) { diffY = self.pageYOffset; diffX = self.pageXOffset; }
		if(diffY != lastScrollY) {
	                percent = .1 * (diffY - lastScrollY);
	                if(percent > 0) percent = Math.ceil(percent);
	                else percent = Math.floor(percent);
			if(IE) document.all.floater.style.pixelTop += percent;
			if(NS) document.floater.top += percent; 
	                lastScrollY = lastScrollY + percent;
	    }
		if(diffX != lastScrollX) {
			percent = .1 * (diffX - lastScrollX);
			if(percent > 0) percent = Math.ceil(percent);
			else percent = Math.floor(percent);
			if(IE) document.all.floater.style.pixelLeft += percent;
			if(NS) document.floater.top += percent;
			lastScrollY = lastScrollY + percent;
		}	
	}	
	if(NS || IE) action = window.setInterval("heartBeat()",1);