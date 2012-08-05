<?php
class ThatFormBuilder {
	
	// Stores all form inputs
	private $inputs = array(); 
	
	// Stores all form attributes
	private $form = array(); 
	
	// Make sure a submit button is output
	private $has_submit = false;
	
	// Constructor to set basic form attributes
	function __construct($action = '', $args = false) {
		
		$defaults = array(
			'action' => $action,
			'method' => 'post',
			'enctype' => 'application/x-www-form-urlencoded',
			'class' => array(),
			'id' => '',
			'markup' => 'html',
			'novalidate' => false,
			'add_nonce' => false,
			'add_honeypot' => true,
		);
		
		if ($args) $settings = array_merge($defaults, $args);
		else $settings = $defaults;
		
		foreach ($settings as $key => $val) :
			// Try setting with user-passed setting
			// If not, try the default with the same key name
			if (!$this->set_att($key, $val)) $this->set_att($key, $defaults[$key]);
		endforeach;
	
	}
	
	// Set attributes for the form and special fields
	function set_att($key, $val) {
	
		switch ($key) :
			
			case 'method':
				if (! in_array($val, array('post', 'get'))) return false;
				break;
			
			case 'enctype':
				if (! in_array($val, array('application/x-www-form-urlencoded', 'multipart/form-data'))) return false;
				break;
			
			case 'markup':
				if (! in_array($val, array('html', 'xhtml'))) return false;
				break;
			
			case 'class':
			case 'id':
				if (! $this->_check_valid_attr($val)) return false;
				break;
			
			case 'novalidate':
			case 'add_honeypot':
				if (! is_bool($val)) return false;
				break;
			
			case 'add_nonce':
				if (! is_string($val) && !is_bool($val)) return false;
				break;
			
			default: 
				return false;
			
		endswitch;
		
		$this->form[$key] = $val;
		
		return true;
		
	}
	
	// Add an input to the queue
	function add_input($label, $args = '', $slug = '') {
		
		if (empty($args)) $args = array();
		// Create slug
		if (empty($slug)) $slug = $this->_make_slug($label);
		
		$defaults = array(
			'type' => 'text',
			'name' => $slug,
			'id' => $slug,
			'label' => $label,
			'value' => '',
			'placeholder' => '',
			'class' => array(),
			'min' => '',
			'max' => '',
			'step' => '',
			'autofocus' => false,
			'checked' => false,
			'required' => false,
			'add_label' => true,
			'options' => array(),
			'wrap_tag' => 'div',
			'wrap_class' => array('form_field_wrap'),
			'wrap_id' => '',
			'wrap_style' => ''
		
		);
		
		// Combined defaults and arguments
		// Arguments override defaults
		$args = array_merge($defaults, $args);
		
		$this->inputs[$slug] = $args;
		
	}
	
	// Add multiple inputs to the queue
	function add_inputs($arr) {
		
		if (!is_array($arr)) return false;
		
		foreach ($arr as $field) :
			$this->add_input($field[0], isset($field[1]) ? $field[1] : '', isset($field[2]) ? $field[2] : '');
		endforeach;
		
	}
	
	// Parse the inputs and build the form HTML
	function build_form() {
	
		$output = '
		<form method="' . $this->form['method'] . '"';
		
		if (!empty($this->form['enctype'])) $output .= ' enctype="' . $this->form['enctype'] . '"';
		
		if (!empty($this->form['action'])) $output .= ' action="' . $this->form['action'] . '"';
		
		if (!empty($this->form['id'])) $output .= ' id="' . $this->form['id'] . '"';
		
		if (count($this->form['class']) > 0) $output .= $this->_output_classes($this->form['class']);
		
		if ($this->form['novalidate']) $output .= ' novalidate';
		
		$output .= '>';
		
		if ($this->form['add_honeypot']) 
			$this->add_input('Leave blank to submit', array(
				'name' => 'honeypot',
				'slug' => 'honeypot',
				'id' => 'form_honeypot',
				'wrap_tag' => 'div',
				'wrap_class' => array('form_field_wrap', 'hidden'),
				'wrap_id' => '',
				'wrap_style' => 'display: none'
			));
		
		if ($this->form['add_nonce'] && function_exists('wp_create_nonce')) 
			$this->add_input('WordPress nonce', array(
				'value' => wp_create_nonce($this->form['add_nonce']),
				'add_label' => false,
				'type' => 'hidden'
			));
		
		foreach ($this->inputs as $key => $val) :
			
			$min_max_range = $element = $end = $attr = $field = $label_html = '';
			
			// Set the field value to incoming
			$val['value'] = isset($_REQUEST[$val['name']]) && !empty($_REQUEST[$val['name']]) ? 
				$_REQUEST[$val['name']] : 
				$val['value'];
			
			switch ($val['type']) :
				
				case 'title':
					$element = '';
					$end = '
					<h3>' . $val['label'] . '</h3>';
					break;
				
				case 'textarea':
					$element = 'textarea';
					$end = '>' . $val['value'] . '</textarea>';
					break;
					
				case 'select':
					$element = 'select';
					$end = '>' . $this->_output_options_select($val['options']) . '
					</select>';
					break;
				
				case 'checkbox':
					if (count($val['options']) > 0) :
						$element = '';
						$end = $this->_output_options_checkbox($val['options'], $val['name']);
						$label_html = '<p class="checkbox_header">' . $val['label'] . '</p>';
						break;
					endif;
				
				case 'radio':
					if (count($val['options']) > 0) :
						$element = '';
						$end = $this->_output_options_radios($val['options'], $val['name']);
						$label_html = '<p class="checkbox_header">' . $val['label'] . '</p>';
						break;
					endif;
				
				case 'range':
				case 'number':
					$min_max_range .= !empty($val['min']) ? ' min="' . $val['min'] . '"' : '';
					$min_max_range .= !empty($val['max']) ? ' max="' . $val['max'] . '"' : '';
					$min_max_range .= !empty($val['step']) ? ' step="' . $val['step'] . '"' : '';
				
				case 'submit':
					$this->has_submit = true;
					break;
				
				default :
					$element = 'input';
					$end .= ' type="' . $val['type'] . '" value="' . $val['value'] . '"';
					$end .= $val['checked'] ? ' selected' : '';
					$end .= $this->field_close();
					break;
				
			endswitch;
			
			$id = !empty($val['id']) ? ' id="' . $val['id'] . '"' : '';
			$class = count($val['class']) ? ' class="' . $this->_output_classes($val['class']) . '"' : '';
			$attr = $val['autofocus'] ? ' autofocus' : '';
			$attr = $val['checked'] ? ' checked' : '';
			$attr = $val['required'] ? ' required' : '';
			
			// Build the label
			if (!empty($label_html)) :
				$field .= $label_html;
			elseif ($val['add_label'] && $val['type'] != 'hidden' && $val['type'] != 'submit') :
				$val['label'] .= $val['required'] ? ' <strong>*</strong>' : '';
				$field .= '
					<label for="' . $val['id'] . '">' . $val['label'] . '</label>';
			endif;
			
			if (!empty($element))
				$field .= '
					<' . $element . $id . ' name="' . $val['name'] . '"' . $min_max_range . $attr . $end;
			else 
				$field .= $end;
			
			// Parse and create wrap, if needed
			if ($val['type'] != 'hidden' && !empty($val['wrap_tag'])) :
			
				$wrap_before = '
				<' . $val['wrap_tag'];
				$wrap_before .= count($val['wrap_class']) > 0 ? $this->_output_classes($val['wrap_class']) : '';
				$wrap_before .= !empty($val['wrap_style']) ? ' style="' . $val['wrap_style'] . '"' : '';
				$wrap_before .= !empty($val['wrap_id']) ? ' id="' . $val['wrap_id'] . '"' : '';
				$wrap_before .= '>';
				
				$wrap_after = '
				</' . $val['wrap_tag'] . '>';
				
				$output .= $wrap_before . $field . $wrap_after;
			else : 
				$output .= $field;
			endif;
			
		endforeach;	
		
		if (! $this->has_submit) $output .= '
				<div class="form_field_wrap">
					<input type="submit" value="Submit" name="submit">
				</div>';
		
		$output .= '
		</form>';
		
		echo $output;
		
	}
	
	// :FIXIT: Add validation for classes and ids
	private function _check_valid_attr($string) {
		
		$result = true;
		
		// Check $name for correct characters
		// "^[a-zA-Z0-9_-]*$"
		
		return $result;
		
	}
	
	// Easy way to auto-close fields, if necessary
	function field_close() {
			
			return $this->form['markup'] === 'xhtml' ? ' />' : '>';
			
	}
	
	
	// Create a slug from a label name
	private function _make_slug($string) {
		
		$result = '';
		
		$result = str_replace('"', '', $string);
		$result = str_replace("'", '', $result);
		$result = str_replace('_', '-', $result);
		$result = preg_replace('~[\W\s]~', '-', $result);
		
		$result = strtolower($result);
		
		return $result;
		
	}
	
	// Parses and builds the classes in multiple places
	private function _output_classes($arr) {
		
		$output = '';
		
		if (count($arr) > 0) :
			$output .= ' class="';
			foreach ($arr as $class) :
				$output .= $class . ' ';
			endforeach;
			$output .= '"';
		endif;
		
		return $output;
		
	}
	
	// Builds the select input output
	private function _output_options_select($arr) {
		$output = '';
		foreach ($arr as $val => $opt) :
			$output .= '
						<option value="' . $val . '">' . $opt . '</option>';
		endforeach;
		return $output;
	}
	
	// Builds the radio button output
	private function _output_options_radios($arr, $name) {
		$output = '';
		foreach ($arr as $val => $opt) :
			$slug = $this->_make_slug($opt);
			$output .= '
						<input type="radio" name="' . $name . '[]" value="' . $val . '" id="' . $slug . '"';
			$output .= $this->form['markup'] === 'xhtml' ? ' />' : '>';
			$output .= '
						<label for="' . $slug . '">' . $opt . '</label>';
		endforeach;
		return $output;
	}
	
	// Builds the multiple checkbox output
	private function _output_options_checkbox($arr, $name) {
		$output = '';
		foreach ($arr as $val => $opt) :
			$slug = $this->_make_slug($opt);
			$output .= '
						<input type="checkbox" name="' . $name . '[]" value="' . $val . '" id="' . $slug . '"';
			$output .= $this->form['markup'] === 'xhtml' ? ' />' : '>';
			$output .= '
						<label for="' . $slug . '">' . $opt . '</label>';
		endforeach;
		return $output;
	}
	
}