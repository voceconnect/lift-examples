<?php

class TestLiftTextFilter extends aLiftFormFilter {

	protected $name;

	public function __construct( $field, $label, $name, $args = array( ) ) {
		$this->name = $name;
		parent::__construct( $field, $label, $args );
	}

	protected function getControlItems( $lift_query ) {
		return array( );
	}

	/**
	 * 
	 * @param string $filterHTML the unfiltered html to be overwritten
	 * @param Lift_Search_Form $lift_search_form
	 * @param type $args
	 * @return string the resulting control html
	 */
	public function getHTML( $filterHTML, $lift_search_form, $args ) {
		extract( $args );
		$value = isset( $_REQUEST[$this->name] ) ? $_REQUEST[$this->name] : '';
		$html = '<div>' . esc_html( $this->label ) . '</div>';
		$html .= sprintf( '<form action="%s" method="GET">', remove_query_arg( $this->name ) );
		$html .= sprintf( '<input type="text" name="%1$s" value="%2$s" />', esc_attr( $this->name ), esc_attr( $value ) );
		$html .= '<input type="submit" value="Apply" />';
		$html .= '</form>';
		return $before_field . $html . $after_field;
	}

}