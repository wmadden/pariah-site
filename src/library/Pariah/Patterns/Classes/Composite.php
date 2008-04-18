<?php
/**
 * Composite.php
 *
 * This file provides the Composite pattern.
 */

require_once 'Component.php';
require_once 'ITraversable.php';

/**
 * The Composite pattern.
 */
class Composite extends Component
{
  /**
   * Adds a component to the composite.
   *
   * The 'component' is stored in the composite in an associative array,
   * identified by 'ID'.
   *
   * @param component The component to store (passed by reference).
   * @param mixed ID  A variable by which this component can be identified.
   */
  public function addComponent(Component $component, $ID )
  {
    $this_class = get_class($this);
    // Check that the component is of type componentType, or this class
    if( !($component instanceof $this->componentType || $component instanceof $this_class) )
      throw new Exception( "Cannot add component to composite: is not of type "
                         . "'{$this->componentType}'." );
    
    // Check if the ID is already in use
    if( isset($this->components[$ID]) )
      throw new Exception( "Cannot add component to composite: ID '$ID' already in use" );
    
    // Add the component to the set
    $this->components[$ID] = $component;
  }
  
  /**
   * Delete a component from the composite.
   *
   * @param mixed ID The ID of the component to be deleted, or null for all.
   *                 Defaults to null.
   */
  public function deleteComponent( $ID = null )
  {
    // Check if the ID is null, which means we need to delete all components
    if( $ID === null )
    {
      // Note: I'm trying to avoid destroying variables which might be used
      //       elsewhere. It would be a good idea to find out how PHP handles
      //       this.
    
      // Iterate through the set of components and unset all elements
      foreach( $components as $key => $value )
        unset($components[$key]);
    }
    
    // Unset the component identified by ID
    unset($this->components[$ID]);
  }
  
  /**
   * Gets a component identified by ID.
   *
   * @param mixed ID The ID of the component to return, or null for all.
   *                 Defaults to null.
   *
   * @return The component identified by 'ID' or null.
   */
  public function getComponent( $ID = null )
  {
    // If the ID is null, return the entire array
    if( $ID === null )
      return $this->components;
    
    // Check to make sure there is a component with that ID, and return it
    if( isset($this->components[$ID]) )
      return $this->components[$ID];
    
    // Otherwise return null
    return null;
  }
  
  /**
   * Checks if the composite contains a component with the given ID.
   *
   * @param mixed ID The ID of the component to look for, or null to check if
   *                 the composite has _any_ components.
   *
   * @return bool True if there is a component with the given ID, or false.
   */
  public function hasComponent( $ID = null )
  {
    // If the ID is null, check that the array is not empty
    if( $ID == null )
      return !empty($this->components);
    
    // Otherwise, check if we have the specified component
    return isset($this->components[$ID]);
  }
    
  /**
   * The magic method, __call, used to overload operations on components.
   */
  public function __call (string $name, array $arguments)
  {
    // If there are no available operations, try the parent __call
    if( empty($this->availableOperations) )
    {
      return parent::_call($name, $arguments);
    }
    
    // Get the available operations
    $available_operations = implode('|', $this->componentOperations);
    
    // Get the component type
    $component_type = $this->getComponentType();
    
    // Initialize the empty regs array, to store the results of the regex
    $regs = array();
    
    // If the regex matches, call the appropriate function
    if( ereg("($available_operations)(Component|$component_type)", $name, $regs) !== false )
      return call_user_func_array( array( $this, $regs[0] ), $arguments );
    // Otherwise, try the parent __call function
    else
      return parent::_call($name, $arguments);
  }
  
  /**
   * Available operations.
   */
  protected $componentOperations = array( 'has', 'get', 'add', 'delete' );
  
  /**
   * The type of component being stored. Must be a subclass of Component.
   */
  protected $componentType = 'Component';
  
  /**
   * The set of components
   */
  private $components = array();
}


/**
 * An extension of the Composite pattern, provides traversal of components.
 */
class TraversableComposite extends Composite implements ITraversable
{
  /**
   * Traverses the Composite and its components.
   *
   * Where possible, performs a depth-first traversal. All components of
   * traversable composites within the tree will be traversed, and their top-
   * level intraversable components.
   *
   * @param Functor function_callback The function to be called for each
   *                                  component.
   */
  public function traverse( Functor $function_callback )
  {
    if( $this->traversed == true )
      return false;
    
    // Call the function on this object
    $function_callback->call($this);
    
    $this->traversed = true;
    
    // Get all components in this composite
    $components = $this->getComponent();
    
    // Iterate through them, calling the callback function for each one
    foreach( $components as $component )
    {
      // Check if this component is traversable, if so, traverse it
      if( method_exists($component, 'traverse') )
        $component->traverse();
      // Otherwise, call the callback on this component
      else
        $function_callback($component);
    }
    
    return true;
  }
  
  /**
   * This function is used to reset the "traversed" property of the object,
   * and its children, which is used to avoid loops.
   */
  public function resetTraversal()
  {
    // Get all components in this composite
    $components = $this->getComponent();
    
    // Iterate through them, resetting them
    foreach( $components as $component )
    {
      // Check if this component is traversable, if so, traverse it
      if( method_exists($component, 'traverse') )
        $component->resetTraversal();
    }
    
    $traversed = false;
  }
  
  private $traversed = false;
}

?>