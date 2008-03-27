<?php
/**
 * Component.php
 *
 * This file defines the Component class, used by the Composite pattern. In
 * addition, the Component class provides a very useful implementation of the
 * __call method, credit for which goes to Matthew Byrne.
 */

class Component
{
  /**
   * Provides function overloads.
   * 
   * Provides getters and setters for variables defined in the $fields array.
   * Provides method redirects as defined in the $methods array.
   *
   * @param string $name The name of the function.
   * @param array $arguments The arguments to the function.
   */
  public function __call( string $name, array $arguments )
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
      $op = $regs[0];
      // Get the name of the field
      $field = $regs[1];
      
      // Convert the first letter of the field name to lowercase
      $field[0] = strtolower($field[0]);
      
      // If the variable to get/set is not a field, throw an exception
      if( !isset($this->fields[$field]))
        throw new Exception("There is no field matching the name '$field' to $op.");
      
      // If setting a field, check that it is the required type (if defined)
      if( $op == 'set' )
      {
        // If the field type is not defined, set the field
        if( !isset($this->fields[$field]['type']) )
          $this->$field = $arguments[0];
        else
        {
          // If the type doesn't match, throw an exception
          if( !is_a($arguments[0], $this->fields[$field]) )
            throw new Exception("Can't set field '$field', parameter of wrong type.");
          // Otherwise, set the field
          else
            $this->$field = $arguments[0];
        }
      }
      // Otherwise, return the field
      else
        return $this->$field;
    }
  }
  
  /**
   * Defines accessible variables in this component.
   * 
   * The fields variable is an array in the form array( name => array(
   * requirement => value ) ). E.g. array( 'name', 'gender', 'age' => array( 
   * 'type' => 'int' ) ).
   *
   * @var array The component fields.
   */
  protected $fields = array();
  /**
   * Defines redirected methods.
   * 
   * The $methods array is structured as follows:
   * 		$methods = array( method_name => redirect_name )
   * Which will result in $object->method_name calling $object_redirect_name.
   * Arguments are passed as-is.
   */
  protected $methods = array();
}

class Component_Validatable extends Component implements IValidatable
{
  abstract function isValid();
  abstract function getErrors();
}

?>