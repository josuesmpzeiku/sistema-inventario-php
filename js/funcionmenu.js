function setupNavToggle() {
            $("#tmMainNavToggle").on("click", function(){
                $(".sidebar").toggleClass("show");
            });
        }
        function setupNav() {  
            $(".tm-main-nav a").click(function(e){	     		
                $("#tmSideBar").removeClass("show");
            });	    
        }
        function setupFooter() {
  			
            var padding = 50;
            var footerPadding = 20;  			
            var footer = $(".footer-link");
            var footerHeight = footer.outerHeight(true);
            var totalPageHeight = footerHeight + footerPadding + padding;
            var windowHeight = $(window).height();		

                if(totalPageHeight > windowHeight){
                    $(".tm-content").css("margin-bottom", footerHeight + footerPadding + "px");
                        footer.css("bottom", footerHeight + "px");  			
                    }
                else {
                    $(".tm-content").css("margin-bottom", "0");
                        footer.css("bottom", "20px");  				
                    }  			
        }
       
      	$(window).on("load", function(){
            if(renderPage) {				
		$('body').addClass('loaded'); 			   			    
                    setupNavToggle(); 
                     setupFooter();
                     setupNav();
            }	      	
	});      

        var renderPage = true;
	if(navigator.userAgent.indexOf('MSIE')!==-1 || navigator.appVersion.indexOf('Trident/') > 0){
   		/* Microsoft Internet Explorer detected in. */
            alert("Please view this in a modern browser such as Chrome or Microsoft Edge.");
            renderPage = false;
        }
        
