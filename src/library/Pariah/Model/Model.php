<?php
/**
 * Model.php
 * 
 * This file provides the Model base class.
 */

/**
 * A Model represents an object in the domain.
 * 
 * This class represents a Model in the most general sense. It provides the
 * basic Model operations:
 *  construction - default, from data, from another model (copy)
 *  member access
 * 
 * The purpose of the Model class is to allow easy manipulation of an object in
 * memory. Loading and saving those objects is the job of the Mapper.
 * 
 * Loading and saving models is done elsewhere
 */
// TODO: implement Iterator interface
// TODO: implement more intelligent __call function, which can handle complex
//       redirects like get* and set*.
class Pariah_Model
{
  /**
   * Constructs a Model.
   *
   * If $data is a Model, copy constructor.
   * If $data is an array, builds model from array.
   * 
   * @param array $data A map of member names to values.
   */
  public function __construct( $data = null )
  {
    if( $data instanceof Model )
    {
      $this->_data = $data->_data;
      $this->fields = $data->fields;
      return;
    }
    
    if( is_array($data) )
      $this->_fromArray($data);
  }
  
  /**
   * Converts the model to an array.
   *
   * @return array
   */
  public function toArray()
  {
    return $this->_data;
  }
  
  protected function _fromArray( array $data )
  {
    $this->_data = $data;
  }
  
  /**
   * Provides function overloads.
   * 
   * Provides getters and setters for variables defined in the $fields array.
   * Provides method redirects as defined in the $methods array.
   *
   * @param string $name The name of the function.
   * @param array $arguments The arguments to the function.
   */
  public function __call( $name, array $arguments )
  {
    // Check if method name is registered in $methods
    if( isset($this->methods[$name]) )
    {
      // Call the redirect
      $redirect = $methods[$name];
      return call_user_func_array($redirect, $arguments);
    }
    
    // Otherwise, see if it's a getter/setter
    
    // Regs will store matched variables in the regex
    $regs = array();
    // Test the function name to see if it is a valid getter/setter
    if( ereg("(get|set)([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)", $name, $regs) !== false )
    {
      // Get the name of the operation
      $operation = $regs[1];
      // Get the name of the field
      $field = $regs[2];
      
      // Convert the first letter of the field name to lowercase
      $field[0] = strtolower($field[0]);
      
      // If the variable to get/set is not a field, throw an exception
      if( !$this->hasField($field) )
        throw new Pariah_Model_Exception("There is no field matching the name '$field' to $op.");
      
      if( $operation == 'set' )
      {
        $this->_set($field, $arguments[0]);
      }
      
      return $this->_get($field);
    }

    throw new Pariah_Model_Exception("Model has no method '$name'.", 4);
  }
  
  /**
   * Adds a field.
   */
  protected function addField( $field )
  {
    $this->fields[] = $field;
    return $this;
  }
  
  /**
   * Deletes a field.
   */
  protected function deleteField( $field )
  {
    unset($this->fields[$field]);
    return $this;
  }
  
  /**
   * Returns true if the model has the field.
   */
  public function hasField( $field )
  {
    return array_search($field, $this->fields) !== false;
  }
  
  /**
   * Adds a set of fields.
   */
  protected function addFields( array $fields )
  {
    $this->fields = array_merge($this->fields, $fields);
    return $this;
  }
  
  protected function _set( $field, $value )
  {
    $this->_data[$field] = $value;
    $this->dirty = true;
  }
  
  protected function _get( $field )
  {
    return $this->_data[$field];
  }
  
  /**
   * Defines accessible variables in this component.
   * 
   * The fields variable is an array of fields in the model.
   * Note that these fields represent permanent fields, which should be saved
   * to a database (and will be, by ModelTable and it's subclasses). For run-
   * time only fields, you must create your own accessors.
   *
   * @var array The component fields.
   */
  protected $_fields = array();
  /**
   * This variable holds the values of fields defined in the $fields array.
   *
   * NOTE: This variable is not intended to be accessed directly! You should,
   * where possible, use accessor methods to access the data in this array, so
   * that overridden accessors are called.
   * 
   * @var array
   */
  protected $_data = array();
  /**
   * Defines redirected methods.
   * 
   * A map of method names to redirected names, which will result in
   * $object->method_name calling $object_redirect_name.
   * 
   * Arguments are passed as-is.
   * 
   * @var array
   */
  protected $_methods = array();
}
