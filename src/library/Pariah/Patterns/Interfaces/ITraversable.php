<?php
/**
 * ITraversable.php
 *
 * Provides the Traversable interface.
 */

require_once('Functor.php');

/**
 * The Traversable interface.
 */
interface ITraversable
{
  /**
   * The traverse function.
   */
  public function traverse( Functor $callback_function );
}

?>