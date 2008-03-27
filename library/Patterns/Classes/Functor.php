<?php
/**
 * Functor.php
 *
 * This file provides the Functor pattern class.
 *
 * @author William Madden
 */

require_once('ICallable.php');

/**
 * The Functor class.
 *
 * Problem:
 *	PHP function callbacks work by storing the name of the function in a
 *	variable. This means that when calling a callback, the function the name
 *	refers to must be in scope.
 *	
 *  In addition, function names are not sufficient for calling methods, which
 *	require a reference to the class.
 *
 *  EDIT: after some research, I found the call_user_func* functions and the
 *  callback pseudo-type, which provide the same functionality as this Functor
 *  class. This class still provides a nicer interface.
 *
 * Solution:
 *  Create a class to work around this fact.
 */
class Functor implements Callable
{
  /**
   * The constructor.
   *
   * The callback function follows the normal PHP rules for variable functions,
   * which are good and bad.
   *
   * A variable function can be:
   *    * a string containing the function name
   *    * or an array containing the class and method name
   *
   * See the {@link http://au2.php.net/manual/en/language.pseudo-types.php#language.types.callback documentation}
   * for more information.
   *
   * @param callback $callbackFunction The callback function. Defaults to null.
   *                                   Note that while you can instantiate an
   *                                   empty functor, you must set the callback
   *                                   function prior to calling call().
   */
  public function __construct( callback $callback_function = null )
  {
    $this->set($callback_function);
  }
  
  /**
   * Gets the callback function.
   * 
   * @return callback The callback function.
   */
  public function get()
  {
    return $this->callbackFunction;
  }
  
  /**
   * Sets the callback function.
   *
   * @param callback $callback_function The callback function.
   */
  public function set( callback $callback_function )
  {
    $this->callbackFunction = $callback_function;
  }
  
  /**
   * Provides a nicer interface to setting a method as the callback.
   *
   * @param object A reference to the object.
   * @param string method The name of the method.
   */
  public function setMethod( $object, string $method )
  {
    $this->callbackFunction = array( $object, $method );
  }
  
  /**
   * Calls the function.
   */
  public function call()
  {
    if( $this->callbackFunction == null )
      throw new Exception('Functor callback not initialized.');
    
    user_call_func_array( $this->callbackFunction, func_get_args() );
  }
  
  /**
   * The callback function wrapped by the functor.
   */
  protected $callbackFunction = null;
}

/**
 * Acts as a function which takes as an argument an object, but which instead
 * calls a preset method of that object, returning the result.
 */
class MethodCaller extends Functor
{
  public function call( $object )
  {
    $method = $this->method;
    if( $method == null )
      throw new Exception( 'Method caller method has not been set.' );
    
    $args = $this->args; 
      
    return call_user_func_array(array($object, $method), $args);
  }
  public $method = null;
  public $args = array();
}
?>