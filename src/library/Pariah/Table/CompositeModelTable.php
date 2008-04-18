<?php

/**
 * Represents a table containing composite models.
 * 
 * This class provides functionality for saving and loading composite models.
 */
class CompositeModelTable extends ModelTable
{
  /**
   * Loads components.
   *
   * Returns an array of all successfully loaded components. Throws 
   * CompositeModelTableExceptions on errors.
   * 
   * @param string $type The type of component to load. If null, all.
   * @param string $relationship 
   * @param array $filter
   * 
   * @throws CompositeModelTableException
   * 
   * @return array All loaded components.
   */
  public function loadComponents ( CompositeModel $composite, $type = null, $filter = array() )
  {
    // The array components that were successfully loaded
    $loaded = array();
    
    // If the composite is not concrete, the operation is insecure
    if( !$composite->concrete() )
      throw new CompositeModelTableException(
                  'Composite is not concrete. Can\'t load components.',
                  1
                );
    
    // Get the structure of the composite
    $structure = $composite->getStructure();
    
    // If $type is null, load all types
    if ( $type == null )
    {
      // Iterate through the structure array, loading types
      foreach ( $structure as $key => $value )
      {
        // Figure out which of the key and value is the component type.
        // (Because it's valid to just give component types, in which case the
        // key would be a number)
        $component_type = $value; // By default, assume the value is the type
        $relationship = CompositeModel::REL_ONETOMANY; // Default: one-to-many
        if( is_string($key) )
        {
          // The key is a string, so it must be the component type
          $component_type = $key;
          // hence the value is the relationship
          $relationship = $value;
        }
        
        // Load the component
        $this->loadComponentType(
            $composite,
            $component_type,
            $relationship,
            $filter
        );
      }
    }
    else
    {
      // The type is given.
      // Check that it's a part of the structure for the composite.
      if ( $this->validComponent( $component, $structure ) )
      {
        // Load it
        $this->loadComponentType(
            $composite,
            $component_type,
            $relationship,
            $filter
        );
      }
      else
      {
        // throw an exception
        throw new CompositeModelTableException(
            "Composite $composite cannot hold components of type '$component'.",
            0
        );
      }
    }
    
    return $loaded;
  }
  
  public function saveComponents ( $composite, $filter = array() )
  {
    throw new CompositeModelException("Method not implemented.");
  }
  
  public function deleteComponents ( $composite, $filter = array() )
  {
    throw new CompositeModelException("Method not implemented.");
  }
  
  protected function validComponent ( $component, $structure )
  {
    /*
     * The component is valid if it is in the structure array. Since the
     * component name will never be an integer, we can just compare it to the
     * key and value and if either match, assume it's valid.
     */
    foreach( $structure as $key => $value )
    {
      if( $key == $component || $value == $component )
        return true;
    }
    
    return false;
  }
  
  /**
   * Loads components of a certain type.
   *
   * @param CompositeModel $composite The composite into which to load the components.
   * @param string $type The type of components to load.
   * @param string $relationship The relationship between the composite and this type of component.
   * @param array $filter The filter for these components
   * 
   * @return array All loaded components
   */
  protected function loadComponentType ( CompositeModel &$composite, $type, $relationship = CompositeModel::REL_ONETOMANY, array $filter = array() )
  {
    // The array of all successfully loaded components
    $loaded = array();
    
    // Get an inflector (we'll need it soon)
    $inflector = new Inflector();
    
    // Load the components
    switch ( $relationship )
    {
      case CompositeModel::REL_ONETOONE:
      case CompositeModel::REL_MANYTOONE:
        // component_id in composite record
        // Get the composite record
        $rows = $this->find( $composite->getId() );
        $row = $rows->current();
        
        // Figure out the name of the ID field
        $idfield = $inflector->CamelCase( $type, array( 'LowerCase', 'StringSeparate' ) );
        $idfield .= '_id';
        
        // Get the component ID
        $id = $row->$idfield;
        
        // Get the component mapper
        $mapper = $this->getComponentMapper($type);
        
        // Load the component
        $component = $mapper->loadModel( array( 'id' => $id ) );
        
        // Add it to the composite
        $composite->addComponent($component);
        
        break;
      
      case CompositeModel::REL_ONETOMANY:
        // composite_id in component record
        // Get the composite's ID
        $id = $composite->getId();
        
        // Figure out the name of the ID field
        $idfield = $inflector->CamelCase( get_class($composite), array( 'LowerCase', 'StringSeparate' ) );
        $idfield .= '_id';
        
        // Get the component mapper
        $mapper = $this->getComponentMapper($type);
        
        // Load all components with the composite's ID
        $components = $mapper->loadModels( array( $idfield => $id ) );
        
        // Add them to the composite
        foreach( $components as $component )
          $composite->addComponent($component);
        
        break;
      
      case CompositeModel::REL_MANYTOMANY:
        // component_ids in composite record (CSV)
        // Get the composite record
        $row = $this->find($composite->getId());
        // Figure out the ID field
        $idfield = $inflector->CamelCase( $type, array( 'LowerCase', 'StringSeparate' ) );
        $idfield .= '_ids';
        // Get the component IDs
        $ids = $row->$idfield;
        
        // If there are no IDs, there are no IDs
        if( $ids == null )
          break;
        
        // Explode them
        $ids = explode(',', $ids);
        
        // Get the component mapper
        $mapper = $this->getComponentMapper($type);  
        
        // Load each component and add it to the composite
        foreach( $ids as $id )
        {
          $component = $mapper->loadModel( array( 'id' => $id ) );
          $composite->addComponent($component);
        }
        
        break;
    }
  }
  
  /**
   * Returns an instance of the mapper used to load components of the given 
   * type.
   * 
   * If the type of component has a preset configuration, including the type of
   * mapper, that will be used. Otherwise it will be assumed that a table mapper
   * is to be used, it will try and guess it.
   *
   * @param string $component_type
   * @return Mapper A mapper.
   */
  // TODO: revise this function (Will 09/03/08)
  protected function getComponentMapper ( $component_type )
  {
    // Try to get a mapper
    $mapper = null;
    try
    {
      $mapper = Mapper::getMapper($component_type);
    }
    // If it fails, guess at a table
    catch ( MapperException $e )
    {
      $table = self::guessComponentTable($component_type);
      $mapper = new $table( array('model' => $component_type) );
    }
    
    // Return the mapper we loaded
    return $mapper;
  }
  
  /**
   * Attempt to guess the table type given a component type
   */
  protected static function guessComponentTable ( $component_type )
  {
    // See if there's a table class of type 'component' + 'table'
    if( class_exists($component_type . 'Table') )
    {
      return $component_type . 'Table';
    }
    // Otherwise if it's a composite model, use CompositeModelTable
    else if( $component_type instanceof CompositeModel )
    {
      return 'CompositeModelTable';
    }
    // Otherwise return ModelTable
    return 'ModelTable';
  }
}