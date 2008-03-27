<?php
/**
 * CompositeModel.php
 *
 * This file provides the Composite Model class.
 * 
 * @author William Madden
 */

require_once('Model.php');

/**
 * The composite model class: a model which contains other models.
 * 
 * In addition to the standard model operations, CompositeModel provides:
 *    Adding/Getting/Deleting component models
 */
class CompositeModel extends Model
{
  const REL_ONETOONE = 'OO';
  const REL_ONETOMANY = 'OM';
  const REL_MANYTOONE = 'MO';
  const REL_MANYTOMANY = 'MM';
  
  /**
   * Adds a component to the composite model.
   * 
   * @param $component The component to add.
   * @param $index The index of the component (optional).
   * 
   * @return The index of the added component.
   */
  public function addComponent( Model $component )
  {
    // Check the component type is valid in this composite
    if( !$this->holdsComponent($component) )
      throw new CompositeModelException('CompositeModel of type ' . get_class($this) . ' cannot store models of type ' . get_class($component) . '.', 2);
    
    if( $index == null )
    {
      $index = count($this->components);
      $this->components[] = $component;
    }
    else
      $this->components[$index] = $component;
    
    return index; 
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
   * Deletes a component.
   * 
   * Takes as parameters a description of the component and the type of
   * component. If neither is provided, returns all components.
   *
   * @param string|array $id The component ID, or a map of properties to values.
   * @param string $type The type of component.
   * 
   * @return unknown
   */
  public function deleteComponent( $id = null, $type = null )
  {
    unset($this->components[$index]);
  }
  
  /**
   * Checks if the composite contains a component.
   *
   * Takes either the ID of the component or a map of properties to values which
   * the component posesses. Additionally, if type is specified only components
   * of that type will be considered.
   * 
   * @param string|array $id The component ID, or a map of properties to values.
   * @param string $type The type of component.
   * 
   * @return bool True if the composite contains a matching component, or false.
   */
  public function hasComponent( $id, $type = null )
  {
    // If we can find the component, return true
    return $this->findComponent( $id, $type ) !== false;
  }
  
  /**
   * As with hasComponent, but returns the component.
   *
   * @param string|array $id The component ID, or a map of properties to values.
   * @param string $type The type of component.
   * 
   * @return unknown
   */
  public function getComponent( $id, $type = null )
  {
    // Try to find the component
    $index = $this->findComponent( $id, $type );
    // If found return the component
    if( $index !== false )
      return $this->components[$index];
    
    // Otherwise return false
    return false;
  }
  
  /**
   * Used to get a set of components in the composite.
   * 
   * Takes as parameters a description of the component and the type of
   * component. If neither is provided, returns all components.
   * 
   * @param string|array $id The component ID, or a map of properties to values.
   * @param string $type The type of component.
   * 
   * @return array All matching components.
   */
  public function getComponents( $type = null, $id = null )
  {
    // If given nothing to match, return everything
    if( $id == null && $type == null )
      return $this->components;
    
    $result = array();
    
    foreach( $this->components as $component )
    {
      if( $this->matchComponent( $component, $id, $type ) )
        $result[] = $component;
    }
    
    return $result;
  }
  
  /**
   * Returns the array of structure information.
   */
  public function getStructure()
  {
    return $this->structure;
  }
  
  /**
   * Takes the name of a component class and the relationship and adds it to
   * the composite's structure.
   *
   * FLUENT
   */
  protected function addStructure( $component_type, $relationship )
  {
    $this->structure[$component_type] = $relationship;
    
    return $this;
  }
  
  protected function addStructures( $component_types )
  {
    $this->structure = array_merge( $this->structure, $component_types );
    
    return $this;
  }
  
  protected function deleteStructure( $component_type )
  {
    unset($this[$component_type]);
    
    return $this;
  }
  
  /**
   * Throws an exception if the component cannot belong in this composite, or if
   * the composite structure is un- or ill-defined.
   * 
   * TODO: ill-defined exceptions
   *
   * @param Model $component
   */
  protected function holdsComponent( $component )
  {
    // Check that the component is of a type present in $this->structure
    foreach( $this->structure as $key => $value )
    {
      $component_type = get_class($component);
      if( $component_type == $key || $component_type == $value )
        return true;
    }
    
    return false;
  }
  
  /**
   * Searches the composite for a component.
   *
   * Needs either the ID of the component or a map of properties to values which
   * the component posesses. Also may be given the type of component to search
   * for. Returns the index of the component if found, or null.
   * 
   * @param string|array $id The component ID, or a map of properties to values.
   * @param string $type The type of component.
   * 
   * @return mixed The index of the component or null.
   */
  protected function findComponent( $id, $type = null )
  {
    // Go through components and see if they match
    foreach( $this->components as $index => $component )
    {
      if( $this->matchComponent( $component, $id, $type ) )
        return $index;
    }
    
    // If not found, return false
    return false;
  }
  
  /**
   * Given a description of a component, checks to see if it matches.
   * 
   * Takes as parameters a component to check, either an ID number or a map
   * of properties to values, and the type of component to check or null.
   * 
   * If type is given, checks initially that $component is of $type. Then if $id
   * is an array, checks that each property in $id matches that in $component,
   * or that $component's ID matches $id.
   *
   * @param Model $component The component to check.
   * @param string|array $id Either an ID, or a map of properties to values.
   * @param null|string $type The type of component, or null.
   * 
   * @return bool Returns true if the component matches, otherwise false.
   */
  protected function matchComponent( Model $component, $id, $type = null )
  {
    // If the type is given, but does not match, fail
    // EDIT: changed the condition to use instanceof, which should take into
    // account inheritance.
    if( $type != null && !(get_class($component) instanceof $type) )
      return false;
    
    // If $id is an array, assume it's a map of properties to values
    if( is_array($id) )
    {
      // Iterate through the map, if any don't match, fail
      foreach( $id as $property => $value )
      {
        $getter = "get$property";
        if( $component->$getter() != $value )
        {
          return false;
        }
      }
    }
    // Otherwise, if the component's ID isn't $id, fail
    else if( $component->getId() != $id )
      return false;
    
    // Otherwise, it matches
    return true;
  }
  
  protected $components = array();
  /**
   * An array describing the structure of the composite.
   * 
   * Should follow the form:
   *    $structure = array(
   *        component type => relationship
   *     );
   * The relationship will default to one-to-many if not given. If given it
   * should be a relationship constant CompositeModel::REL_ONETOMANY, for
   * example.
   * 
   * You can use the values of the constants themselves, which is much nicer.
   * They are mnemonics, where O represents one and M represents many.
   * CompositeModel::REL_ONETOMANY can be replaced by OM.
   *
   * @var array
   */
  private $structure = array();
}