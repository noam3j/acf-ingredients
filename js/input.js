(function($){
	
	var ACF_TABLE_SELECTOR = 'table.acf-ingredients-table';
	var ACF_TR_SELECTOR = 'tr.acf-ingredient-row';

	var $ingredientsTable;
	var $ingredientClone;
	var $ingredients;
	
	function initIngredients($el) {
		$ingredientsTable = $el.find(ACF_TABLE_SELECTOR);
		$ingredients = $ingredientsTable.find(ACF_TR_SELECTOR);

		var $buttons = $ingredients.find('button.remove');
		$buttons.click(function() {

			removeIngredient($(this));
			return false;

		});

//				currentId = $ingredients.length;
		var $newIngredient = $ingredients.last();
		$newIngredient.focusin(function() {
			
			newIngredient($(this));
			
		});

		$ingredientClone = $newIngredient.clone(true, true);

		console.log($ingredientClone);
	}

	function newIngredient($element) {

		$element.removeClass('new');
		$element.unbind('focusin');

		var $newIngredient = $ingredientClone.clone(true, true);

		$ingredientsTable.append($newIngredient);

		console.log('new ingredient');

	}

	function removeIngredient($element) {
		console.log('hello world');
		var $tr = $element.parents(ACF_TR_SELECTOR);

		console.log($ingredientsTable);
		console.log($tr);

		$tr.remove();
	}
	
	function initialize_field( $el ) {
		
		initIngredients($el);
		
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
			
			$(postbox).find('.field[data-field_type="ingredients"]').each(function(){
				
				initialize_field( $(this) );
				
			});
			
		});
	
	
	}


})(jQuery);
