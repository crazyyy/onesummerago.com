jQuery(document).ready( function($) {

	$( ".slides" ).sortable();
	
	var mid = id;
	
	$(".slider-wrap .deleted").live("click", function() {
		if(confirm("Do you really want delete this slider?"))
			$(this).parents(".slider-wrap").remove();
		
		return false;
	});
	
	$(".slide-single .delete-single").live("click", function() {
		if(confirm("Do you really want delete this slide item?"))
			$(this).parents(".slide-single").remove();
			
		return false;
	});
	
	$(".add-slider a").click(function() {
		
		var clone = $("#slider-clone .slider-wrap").clone();
		
		clone.find(".parent").attr("name", "sliders["+mid+"]");
		clone.find(".slider-id").attr("name", "sliders["+mid+"][0]");
		clone.find(".slider-id").attr("value", mid);
		clone.find(".type").attr("name", "sliders["+mid+"][1]");
		clone.find(".name").attr("name", "sliders["+mid+"][2]");
		
		$(".all-sliders").append(clone);
		
		mid++;

		
		return false;
	});
	
	$(".add_new_slide a").live("click", function() {
		var parentType = $(this).parents(".slider-wrap").find("select").val();
		
		var clone = $(".slide-"+parentType+" .slide-single").clone();
		
		var cid = $(this).parent().siblings(".slider").find(".slider-id").val();
		
		
		clone.find(".field-1").attr("name", "sliders["+cid+"][3]["+idinner+"][0]");
		clone.find(".field-2").attr("name", "sliders["+cid+"][3]["+idinner+"][1]");
		clone.find(".field-3").attr("name", "sliders["+cid+"][3]["+idinner+"][2]");
		clone.find(".field-4").attr("name", "sliders["+cid+"][3]["+idinner+"][3]");
		clone.find(".field-5").attr("name", "sliders["+cid+"][3]["+idinner+"][4]");
		
		$(this).parent().siblings(".slides").append(clone);
		
		idinner++;
		
		return false;
		
	});
	
	$(".slider .edit").live("click", function() {
		$(this).parents(".slider").siblings(".slides, .add_new_slide").slideToggle("fast");
		return false;
	});
	
});