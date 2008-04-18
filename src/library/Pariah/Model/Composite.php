<?php
/**
 * Composite.php
 *
 * This file provides the Composite Model class.
 * 
 * @author William Madden
 */

/**
 * The composite model class: a model which contains other models.
 * 
 * In addition to the standard model operations, CompositeModel provides
 * functions for managing a set of component Models.
 * 
 * Following the same pattern as the Model class, the Composite model allows
 * subclasses to specify the types of components they can hold. This is
 * achieved through use of the addStructure and delStructure methods.
 * 
 * Components are added to the Composite using the addComponent(s) method.
 * Components can be retrieved using the getComponents method, which takes as
 * parameter the type of components you want.
 * 
 * More advanced accessors were considered (such as hasComponent and
 * getComponent), but were not implemented on the basis that such functionality
 * was implementation specific, and should not be provided in the base class.
 */
class Pariah_Model_Composite extends Pariah_Model
{
  /**
   * Adds a component to the Composite.
   * 
   * @param $component The component to add.
   */
  public function addComponent( Model $component )
  {
    // Check the component type is valid in this composite
    if( !$this->holdsComponent($component) )
    {
      throw new Pariah_Model_Exception(
      		'Composite model of type ' . get_class($this) .
      		' cannot store models of type ' . get_class($component) . '.',
            2 );
    }
    
    if( !isset($this->structure[ get_class($component) ]) )
    {
      $this->structure[ get_class($component) ] = array();
    }
    
    $this->structure[get_class($component)][] = $component;
  }
  
  /**
   * Adds a set of components to the composite.
   * 
   * @param array $components The components to add.
   */
  public function addComponents( array $components )
  {
    foreach( $components as $component )
      $this->addComponent($component);
  }
  
  /**
   * Returns all components of the given type stored in the composite.
   *
   * @param unknown $type
   * @return array
   */
  public function getComponents( $type )
  {
    $type = (string)$type;
    
    if( isset($this->structure[$type]) )
    {
      return $this->structure[$type];
    }
    
    return null;
  }
  
  /**
   * Allows the composite to hold components of type $component_type.
   *
   * @return Fluent interface.
   */
  protected function addStructure( $component_type )
  {
    $component_type = (string)$component_type;
    
    if( !in_array($component_type, $this->structure) )
      $this->structure[] = $relationship;
    
    return $this;
  }
  
  /**
   * Allows the composite to hold components of all types in $component_types.
   *
   * @return Fluent interface.
   */
  protected function addStructures( $component_types )
  {
    foreach( $component_types as $component_type )
      $this->addStructure($component_type);
    
    return $this;
  }
  
  /**
   * If the composite can hold components of type $component_type, this will
   * disallow it from doing so, otherwise there is no effect.
   *
   * @param unknown_type $component_type
   * @return Fluent interface.
   */
  protected function delStructure( $component_type )
  {
    // TODO: make sure this works if $this[$component_type] is not set
    unset( $this[$component_type] );
    
    return $this;
  }
  
  /**
   * Checks to see if the Composite can hold the given component.
   * 
   * The Composite can hold the component if the component's type appears in
   * the $_structure array.
   *
   * @param Model $component
   */
  public function holdsComponent( Model $component )
  {
    foreach( $this->structure as $structure )
    {
      if( $component instanceof $structure )
        return true;
    }
    
    return false;
  }
  
  /**
   * An array defining allowable components.
   * 
   * Component types are values, keys are undefined.
   *
   * @var array
   */
  protected $_structure = array();
  /**
   * An array of components in the Composite.
   * 
   * Keys are component types, values are arrays of components.
   *
   * @var array
   */
  protected $_components = array();
}