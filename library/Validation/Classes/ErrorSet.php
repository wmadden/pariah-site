<?php
/**
 * ErrorSet.php
 */

require_once('Composite.php');


class Error extends Component
{
  public function __construct( $id, $error = null )
  {
    $this->error = $error;
    $this->id = $id;
  }
  
  protected $fields = array( 'id',
                             'error'
                           );
}

/**
 * The Error Set class.
 */
class ErrorSet extends TraversibleComposite implements IArrayable
{
  private $componentType = 'Error';
}

?>