<?php

class User extends Resource
{
  public function __construct( $data = null, $id = null, $dirty = true )
  {
    // Set up fields
    $this->addField('username')
         ->addField('password');
    
    // Call the parent constructor
    parent::__construct( $data, $id, $dirty );
    
    // Add the default role to the user, if it's set
    $default_role = Zend_Registry::get('general_config')->authorization->default_role;
    
    $this->addComponent( Mapper::loadModel( 'Role', array('name' => $default_role) ) );
    
    // Ensure that the user system is initialized
    self::check();
  }
  
  /**
   * Returns the logged in user, if there is one, or null.
   *
   * @return User|null
   */
  public static function getLoggedInUser()
  {
    // Ensure that the user system is initialized
    self::check();
    
    if( !self::$auth->hasIdentity() )
      throw new UserException("Nobody is logged in, can't get logged in user.");
    
    $array = self::$auth->getIdentity();
    return $array[0];
  }
  
  /**
   * Returns true if a user is logged in (in the current session).
   *
   * @return bool
   */
  public static function isLoggedIn()
  {
    // Ensure that the user system is initialized
    self::check();
    
    return self::$auth->hasIdentity();
  }
  
  /**
   * Attempts to log in the given user with the given password hash.
   * 
   * Returns a Zend_Auth_Result.
   *
   * @param string $username
   * @param string $password_hash
   */
  public static function logIn( $username, $password )
  {
    // Ensure that the user system is initialized
    self::check();
    
    if( self::isLoggedIn() )
      throw new UserException("Can't log in, user already logged in.", 0);
    
    // Set the username and password of the adapter
    self::$adapter->setIdentity($username)
                  ->setCredential($password);
    
    // Attempt to authenticate
    $result = self::$adapter->authenticate();
    
    if( $result->isValid() )
    {
      // Login succeeded
      $table = new ModelTable( array( 'model' => 'User' ) );
      $user = $table->loadWhere("username = '$username'");
      
      // Store the user in the Zend_Auth persistent storage, cause the lazy
      // fuckers who wrote DbTable don't do it
      self::$auth->getStorage()->write($user);
    }
    
    return $result;
  }
  
  public function isRoot()
  {
    return $this->concrete() && $this->id == 0;
  } 
  
  public static function logOut()
  {
    // Ensure that the user system is initialized
    self::check();
    
    // Clear the user information from Zend_Auth's persistent storage
    self::$auth->clearIdentity();
  }
  
  protected static function init()
  {
    // If there's no adapter yet, create one
    if( self::$adapter == null )
    {
      // Get the database adapter
      $db = Zend_Registry::get('database');
      
      // Get the authentication configuration
      $auth_config = Zend_Registry::get('general_config')->authentication;
      // Create an auth adapter
      self::$adapter = new Zend_Auth_Adapter_DbTable(
                                            $db,
                                            $auth_config->table_name,
                                            $auth_config->identity_column,
                                            $auth_config->credential_column,
                                            $auth_config->credential_treatment
                                        );
    }
    // If we don't have a reference to the Zend_Auth instance, get one
    if( self::$auth == null )
    {
      self::$auth = Zend_Auth::getInstance();
    }
    
    // The user system is now initialized
    self::$initialized = true;
  }
  
  protected static function check()
  {
    if( !self::$initialized )
      self::init();
  }
  
  protected static $adapter = null;
  protected static $auth = null;
  protected static $loggedInUser = null;
  protected static $initialized = false;
}