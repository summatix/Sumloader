/**
 * Initializes the About page
 *
 * @author ShiverCube
 */
$.initAbout = function() {
	var aboutPage = $(".about_page");
	
	if (aboutPage.length == 1) {
		$.debug("Initializing the About page");
		
		var col1 = aboutPage.find(".col1");
		var col2 = aboutPage.find(".col2");
		var maxHeight = parseInt(col1.css("height"));
		col1.css("height", "auto");
		
		var elements = col1.find("*");
		col1.html("");
		
		var col1Full = false;
		elements.each(function() {
			var element = $(this);
			if (!col1Full) {
				col1.append(element);
				if (col1.outerHeight() > maxHeight) {
					col1Full = true;
					element.remove();
					col2.append(element);
				}
				
			} else {
				col2.append(this);
			}
		});
	}
};
