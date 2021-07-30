
jQuery(function($){
	"use strict";
	
	$("#savequote").click(function () {
		snackbar('snackbar','Saving the quote.');
		
		var $name = $('#quote-name').val();
		var $quote_id = $('#savequote').data("id");
		
		$.ajax({ 
			url: '?quote=save',
			data: {"name": $name,"quote_id": $quote_id},
			type: 'get',
			success: function(data){
				snackbar('successbar',data);
				$('#savequote').html('Saved');
				
				if($quote_id === 'create') {
					window.location='/quotes/';
				}	
				else {
					location.reload();
				}					
			}
		});
	});

	$(window).bind("beforeunload",function() {
		if($('#savequote').html() === 'Save') {return "You have an unsaved quote."; }
	});
	
	$("#quote-name").bind("keyup change", function() {
		$('#savequote').html('Save');
	});
	

	$(".removequote").click(function () {
		var $row = $($(this).data('row'));
		$.ajax({ 
			url: '?quote=remove',
			data: {"action": 'remove', "product": $(this).data('product_id'), "variation": $(this).data('variation_id'), "quote_id": $(this).data('quote_id')},
			type: 'get',
			success: function(){
				$row.hide();
				$('#savequote').html('Save');
			}  
		});				
	});
	
	jQuery('.quantityupdate').on('input', function() {
		var $subtotal = $($(this).data('subtotal'));		
		$.ajax({ 
			url: '?quote=update',
			data: {"action": 'update', "product": $(this).data('product_id'), "quantity": $(this).val()},
			type: 'get',
			success: function(data){
				$subtotal.html(data);
				$('#savequote').html('Save');
			}  
		});			
	});
	
	if ( $( "#singleproductdetails" ).length ) {
		$('html, body').animate({
			scrollTop: ($('#singleproductdetails').offset().top-80)
		}, 'fast');
	}

	function processJson(data) {
		//debugger;
		snackbar('successbar',data.message);
		
		var basket = data.basket;

		if(basket.length > 0) {
			$('#quoteTotal').html(basket);
		}
	}

	function snackbar(element,message) {
		// Get the snackbar DIV
		var x = document.getElementById(element);

		// Add the "show" class to DIV
		x.innerHTML=message;
		x.className = "show";

		// After 3 seconds, remove the show class from DIV
		setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
	} 
	
	function showRequest(formData, jqForm, options) {
		// formData is an array; here we use $.param to convert it to a string to display it 
		// but the form plugin does this for you automatically when it submits the data 
		jqForm='';
		options='';
		
		var arrayLength = formData.length;
		for (var i = 0; i < arrayLength; i++) {
			if(formData[i].name === 'submitbutton') {
				if(formData[i].value === 'Add To Quote') {
					snackbar('snackbar','Adding products to quote.');
					return true;					
				} else{
					snackbar('snackbar','Adding products to cart.');
					return true;										
				}				
			}
		}		
	}	
});