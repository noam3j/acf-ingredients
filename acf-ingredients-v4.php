<?php

class acf_field_ingredients extends acf_field {
	
	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options
		
		
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'ingredients';
		$this->label = __('Ingredients');
		$this->category = __("Custom",'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			'measurement_units' => array("", "cups", "tbsp.", "tsp."),
			// add default here to merge into your field. 
			// This makes life easy when creating the field options as you don't need to use any if( isset('') ) logic. eg:
			//'preview_size' => 'thumbnail'
		);
		
		
		// do not delete!
    	parent::__construct();
    	
    	
    	// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);

	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like below) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
	{
		$field = array_merge($this->defaults, $field);
		
		// key is needed in the field names to correctly save the data
		$key = $field['name'];
	
		?>

		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label for=""><?php _e("Measurement Units",'acf'); ?></label>
				<p><?php _e("Enter each measurement.",'acf'); ?></p>
				<p><?php _e("You may want to leave one line blank for when you want to specify an amount (eg. 2 carrots)",'acf'); ?></p>
			</td>
			<td class='field-options-measurement-container'>
				<?php
					
					foreach ($field ['measurement_units'] as $k => $measurement_unit) {
						$this->add_measurement_unit($key, $measurement_unit);
					}
		
					$this->add_measurement_unit($key, '', true);
				?>
				
			</td>
		</tr>

		<?php
		
	}
	
	/*
	*  add_measurement_unit()
	*
	*  This function is called by the create_options function in this class to add a new measurment unit in the ingredients settings
	*
	*  $info	noam3jacobson@gmail.com
	*  @type	function
	*  @since	4.
	*  @date	29/09/15
	*/
	
	function add_measurement_unit($key, $measurement_unit, $new = false) 
	{
		
		$measurement_class = 'field-options-measurement-unit';
		
		if ($new) {
			$measurement_class .= ' new';
		}
		
		?>

		<div class='<?php echo $measurement_class; ?>'>
			<input type=text name='fields[<?php echo $key; ?>][measurement_units][]' value='<?php echo esc_attr($measurement_unit); ?>'>
			<button class='remove'>&#8722;</button>
		</div>

		<?php
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{	
		
		$num_ingredients = 0;
		
		if (isset($field ['value']['quantity']) && isset($field ['value']['measurements']) && isset($field ['value']['ingredient'])) {
		
			$quantities = $field ['value']['quantity'];
			$measurements = $field ['value']['measurements'];
			$ingredients = $field ['value']['ingredient'];
			
			//check that the lengths are all the same. if they aren't, display a warning message
			if (count($quantities) == count($measurements) && count($quantities) == count($ingredients)) {
				$num_ingredients = count($ingredients);
			} else {
				echo '<p class="acf-ingredient-error">* The old ingredients could not be loaded due to an error that occured when the ingredients were saved.</p>';
			}
			
		}
		?>

		<table class='acf-ingredients-table'>
			<thead>
				<tr class='acf-ingredient-headers'>
					<td class='quantity'>
						<label>Quantity</label>
					</td>
					<td class='measurements'>
						<label>measurements</label>
					</td>
					<td class='ingredient'>
						<label>Ingredient</label>
					</td>
					<td class='remove'>

					</td>
				</tr>
			</thead>
			<tbody>
				
		<?php 
		
			for ($i = 0; $i < $num_ingredients; $i++) {

				//add a new ingredient, but skip empty ingredients
				if ($ingredients [$i] != '') {
					$this->add_ingredient_row ($field, $quantities [$i], $measurements [$i], $ingredients [$i]);
				}
			}
			
			//add a blank new row for new entries
			$this->add_ingredient_row ($field, '', '', '');
		
		?>
				
			</tbody>
		</table>

		

		<?php
	}
	
	/*
	*  add_ingredient_row()
	*
	*  This function is called by the create_field function in this class to add a new ingredient to the table of ingredients
	*
	*  $info	noam3jacobson@gmail.com
	*  @type	function
	*  @since	4.
	*  @date	28/09/15
	*/
	
	function add_ingredient_row($field, $quantity, $measurement, $ingredient) 
	{
		
		$field_name = $field ['name'];

		$row_class = 'acf-ingredient-row';
		
		if ($ingredient == '')
			$row_class .= ' new';
		
		?>

		<tr class='<?php echo $row_class; ?>'>
			<td class='quantity'>
				<input type=text name='<?php echo $field_name; ?>[quantity][]' value='<?php echo esc_attr($quantity); ?>'>
			</td>
			<td class='measurements'>
				<select name='<?php echo $field_name ?>[measurements][]'>
					
				<?php
				
					foreach ($field ['measurement_units'] as $key => $measurement_unit) {
							
						echo $measurement_unit;
						echo $measurement;
				
						$selected = '';
						if ($measurement == $measurement_unit) {
										$selected = 'selected';
							echo 'measurement equal';
						} else {
							echo strcmp($measurement, $measurement_unit);
						}
						
						echo $selected;
						echo '<option ' . $selected . '>' . $measurement_unit . '</option>';

					}

				?>

				</select>
			</td>
			<td class='ingredient'>
				<input type=text name='<?php echo $field_name ?>[ingredient][]' value='<?php echo esc_attr($ingredient); ?>'>
			</td>
			<td class='remove'>
				<button class='remove'>&#8722;</button>
			</td>
		</tr>
		

		<?php
	}
	
	
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
		
		
		// register ACF scripts
		wp_register_script( 'acf-input-ingredients', $this->settings['dir'] . 'js/input.js', array('acf-input'), $this->settings['version'] );
		wp_register_style( 'acf-input-ingredients', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version'] ); 
		
		
		// scripts
		wp_enqueue_script(array(
			'acf-input-ingredients',	
		));

		// styles
		wp_enqueue_style(array(
			'acf-input-ingredients',	
		));
		
		
	}
	
	
	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your create_field() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_head()
	{
		// Note: This function can be removed if not used
	}
	
	
	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your create_field_options() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_enqueue_scripts()
	{
		// register ACF scripts
		wp_register_script( 'acf-input-ingredients-options', $this->settings['dir'] . 'js/input-options.js', array('acf-input'), $this->settings['version'] );
		wp_register_style( 'acf-input-ingredients-options', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version'] ); 
		
		
		// scripts
		wp_enqueue_script(array(
			'acf-input-ingredients-options',	
		));

		// styles
		wp_enqueue_style(array(
			'acf-input-ingredients-options',	
		));
		
	}

	
	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your create_field_options() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_head()
	{
		// Note: This function can be removed if not used
	}


	/*
	*  load_value()
	*
		*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value found in the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the value to be saved in the database
	*/
	
	function load_value( $value, $post_id, $field )
	{
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field )
	{
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value( $value, $post_id, $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/
		
		// perhaps use $field['preview_size'] to alter the $value?
		
		
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed back to the API functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/
		
		// perhaps use $field['preview_size'] to alter the $value?
		
		
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/
	
	function load_field( $field )
	{
		// Note: This function can be removed if not used
		return $field;
	}
	
	
	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = acf)
	*
	*  @return	$field - the modified field
	*/

	function update_field( $field, $post_id )
	{
		// Note: This function can be removed if not used
		return $field;
	}

	
}


// create field
new acf_field_ingredients();

?>
