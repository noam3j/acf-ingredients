(function($){
	
	var ACF_CONTAINER_SELECTOR = 'td.field-options-measurement-container';
	var ACF_DIV_SELECTOR = 'div.field-options-measurement-unit';

	var $measurementsContainer;
	var $measurementClone;
	var $measurements;
	var name;
	
	function initMeasurements($el) {
		$measurementsContainer = $el.find(ACF_CONTAINER_SELECTOR);
		$measurements = $measurementsContainer.find(ACF_DIV_SELECTOR);

		var $buttons = $measurements.find('button.remove');
		$buttons.click(function() {

			removeMeasurement($(this));
			return false;

		});

//				currentId = $ingredients.length;
		var $newMeasurement = $measurements.last();
		name = $newMeasurement.find('input[type=text]').attr('name');
		$newMeasurement.find('input[type=text]').attr('name', '');
		$newMeasurement.focusin(function() {
			
			newMeasurement($(this));
			
		});

		$measurementClone = $newMeasurement.clone(true, true);

		console.log($measurementClone);
	}

	function newMeasurement($element) {

		$element.removeClass('new');
		$element.find('input[type=text]').attr('name', name);
		$element.unbind('focusin');

		var $newMeasurement = $measurementClone.clone(true, true);

		$measurementsContainer.append($newMeasurement);

		console.log('new measurement');

	}

	function removeMeasurement($element) {
		console.log('hello world');
		var $div = $element.parents(ACF_DIV_SELECTOR);
		$div.remove();
	}
	
	function initialize_field( $el ) {
		console.log('hurray');
		initMeasurements($el);
		
		
	}
	
	if( typeof acf.add_action !== 'undefined' ) {
	
		/*
		*  ready append (ACF5)
		*
		*  These are 2 events which are fired during the page load
		*  ready = on page load similar to $(document).ready()
		*  append = on new DOM elements appended via repeater field
		*
		*  @type	event
		*  @date	20/07/13
		*
		*  @param	$el (jQuery selection) the jQuery element which contains the ACF fields
		*  @return	n/a
		*/
		
		acf.add_action('ready append', function( $el ){
			
			console.log('v5');
			
			// search $el for fields of type 'FIELD_NAME'
			acf.get_fields({ type : 'ingredients'}, $el).each(function(){
				
				initialize_field( $(this) );
				
			});
			
		});
		
		
	} else {
		
		
		/*
		*  acf/setup_fields (ACF4)
		*
		*  This event is triggered when ACF adds any new elements to the DOM. 
		*
		*  @type	function
		*  @since	1.0.0
		*  @date	01/01/12
		*
		*  @param	event		e: an event object. This can be ignored
		*  @param	Element		postbox: An element which contains the new HTML
		*
		*  @return	n/a
		*/
		
		$(document).on('acf/setup_fields', function(e, postbox){
			
			console.log('v4');
			
			$(postbox).find('tr.field_option_ingredients').each(function(){
				console.log('v4');
				initialize_field( $(this) );
			});
			
		});
	
	
	}


})(jQuery);
