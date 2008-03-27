<?php

/**
 * Represents a table storing models.
 * Operations on such a table are:
 *    * Load a model(s)
 *    * Save a model(s)
 *    * Delete a model(s)
 */
class ModelTable extends Zend_Db_Table_Abstract implements MapperInterface
{
  /**
   * Constructs a ModelTable.
   *
   * @param string $model_name
   * @param array $config
   */
  public function __construct( array $config = array() )
  {
    if( isset($config['model']) )
    {
      $this->_model = $config['model'];
      unset($config['model']);
    }
    
    parent::__construct($config);
  }

  /**
   * Loads a model, given some means to identify the model.
   * 
   * How $identifier is defined and handled is implementation specific.
   * 
   * @param array $criteria A map of properties to values for selecting a single model.
   * @throws MapperException If no single model can be found which matches the given criteria.
   * @return Model
   */
  public function loadModel( $criteria )
  {
    // Load the model specified by ID, if possible
    $rows = $this->find($id);
    
    // If we can't find a model, fail
    if( count($rows) == 0 )
    {
      throw new ModelTableException(
          'No model exists that matches the given criteria.',
          0
      );
    }
    
    // Get the first row
    $row = $rows->current();
    
    return $this->build( $row );
  }
  
  /**
   * Takes as criteria a map of model fields to values.
   * 
   * In addition to field values, the map may also contain the elements "_count",
   * "_order" and "_offset", which will be used in the SQL statement.
   *
   * @param array $criteria A map of properties to values for selecting models.
   * @return array An array of models matching the given criteria.
   */
  public function loadModels( $criteria )
  {
    $select = $this->select();
    
    // Get parameters from criteria array
    $order = $criteria['_order']; unset( $criteria['_order'] );
    $count = $criteria['_count']; unset( $criteria['_count'] );
    $offset = $criteria['_offset']; unset( $criteria['_offset'] );
    
    // Build WHERE clause
    $where = $this->buildWhere($criteria);
    
    // Set the Zend_Select structure's various properties
    $select->where($where);
    $select->order($order);
    $select->limit($count, $offset);
    
    // Query the database
    $rows = $this->fetchAll($select);
    if( count($rows) == 0 )
      return array();
    
    // Try to create models from the result
    $result = array();
    foreach( $rows as $row )
    {
      try
      {
        $model = $this->build($row);
        $result[] = $model;
      }
      catch( ModelTableException $e )
      {
        continue;
      }
    }
    
    // Return the result
    return $result;
  }
  
  /**
   * Builds a where clause from a map of field names to values.
   *
   * @param array $criteria
   * @return string
   */
  protected function buildWhere( $criteria )
  {// TODO: rewrite this function
    $where = '';
    $first = true;
    foreach( $criteria as $field => $value )
    {
      $where .= (!$first ? ' AND ' : '') . 
      $first = false;
    }
    
    return $where;
  }
  
  public function save( Model $model )
  {
    $this->checkModel($model);
    
    // If the model is clean, don't bother
    if( !$model->dirty() )
      return;
    
    // If the model is linked to a row already, update that row
    if( $model->concrete() )
    {
      return $this->update($model);
    }
    else
    {
      $this->insert($model);
    }
  }
  
  public function delete( Model $model )
  {
    $this->delete($model);
  }
  
  public function create( array $data )
  {
    // TODO: implement me!
    throw new ModelTableException("Method not implemented. DON'T CALL IT, ASSHOLE!", 9);
  }
  
  /**
   * Updates a model in the table.
   *
   * @param Model $model
   * @return unknown
   */
  protected function _update( Model $model )
  {
    return parent::update( $this->getTableData($model), 'id = ' . $model->getId() );
  }
  
  /**
   * Inserts a model into the table.
   *
   * @param Model $model
   * @return unknown
   */
  protected function _insert( Model $model )
  {
    return parent::insert( $this->getTableData($model) );
  }
  
  /**
   * Deletes the model from the table.
   *
   * @param Model $model
   * @return unknown
   */
  protected function _delete( Model $model )
  {
    return parent::delete('id = ' . $model->getId()); 
  }
  
  /**
   * Builds an object from a row.
   */
  protected function build( Zend_Db_Table_Row $row )
  {
    $this->checkModel();
    
    $row_data = $row->toArray();
    $field_data = array();
    
    $inflector = new Inflector();
    foreach ($row_data as $column => $value)
    {
      $field = $inflector->StringSeparate($column, array(
                                                      'LowerCase',
                                                      'CamelCase',
                                                      'TitleCase' => array(
                                                          'reverse' => true
                                                       )
                                                   ));
      $field_data[$field] = $value;
    }
    
    // Create the new model
    // TODO: magic value 'id'
    $result = new $this->_model( $field_data, $row_data['id'], false );
    
    return $result;
  }
  
  /**
   * This function is used to check that the given model is of the correct type.
   *
   * It will throw an exception if:
   *    a) The model type is unset
   *    b) The model type is not a valid class name
   *    c) The given model is not of the correct type
   * 
   * @param Model $model
   */
  protected function checkModel( Model $model = null )
  {
    // Check that there is a model type set
    if( $this->_model == null )
      throw new ModelTableException( 'Model type unset.', 3);
    // Check that the model type actually exists
    if( !class_exists($this->_model) )
      throw new ModelTableException( "Model type '{$this->_model}' is not a valid class.", 4);
    // Check that the model is an instance of $_model
    if( $model && !($model instanceof $this->_model) )
      throw new ModelTableException('ModelTable of type ' . getClass($this) . ' cannot store models of type ' . getClass($model) . '.', 2);
  }
  
  /**
   * Converts Model field names to their table field equivalents.
   *
   * @param Model $model
   * @return unknown
   */
  protected function getTableData( Model $model )
  {
    $data = array();
    $inflector = new Inflector();
    foreach( $model->toArray() as $field => $value )
    {
      $field = $inflector->CamelCase($field, array(
                                        'LowerCase',
                                        'StringSeparate',
                                        'TitleCase' => array(
                                            'reverse' => true
                                        )));
      $data['field'] = $value;
    }
    
    return $data;
  }
  
  protected function _setupTableName()
  {
    if( empty($this->_name) )
    {
      // Try to guess table name
      // If we don't know the model type we're storing, fail
      if( empty($this->_model) )
      {
        throw new ModelTableException('Model type not set.', 0);
      }
      // Inflect the name from camelcased to underscore separated lowercase,
      // and pluralise the result.
      $inflector = new Inflector();
      $this->_name = $inflector->CamelCase( $this->_model, array(
                                                'LowerCase',
                                                'StringSeparate',
                                                'Pluralise')
                                          );
    }
    
    parent::_setupTableName();
  }
  
  protected $_model = null;
  protected $_primary = 'id';
  protected $_cols = null;
}