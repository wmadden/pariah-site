<?php

/**
 * {filename}.php
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

/**
 * Extends Zend_Acl to work with my custom Resources, Roles, Rules and 
 * ResourceIds.
 * 
 * A ResourceAcl represents the access control list for a particular resource.
 */
class ResourceAcl extends Zend_Acl
{
  /**
   * Constructs a ResourceAcl given either a Resource (which must have all rules
   * already initialized), or a ResourceId.
   *
   * @param Resource|ResourceId $resource
   */
  public function __construct( $resource )
  {
    // If given a Resource,
    if( $resource instanceOf Resource )
    {
      // Resource must be concrete
      if( !$resource->concrete() )
      {
        throw new ResourceAclException(
                      "Given resource must be concrete to build an ACL.",
                      0
                  );
      }
      // Set the internal $resource variable
      $this->resource = $resource;
    }
    // If given a ResourceId,
    else if( $resource instanceof ResourceId )
    {
      // Load the associated Resource
      $this->loadResource($resource);
    }
    // Otherwise, we don't know what the fuck it is, so throw an exception
    else
    {
      throw new ResourceAclException(
          "Constructor requires valid Resource, or ResourceId.",
          1
      );
    }
    
    // Load parent ACL
    if( $this->resource->getParent() !== null )
    {
      
    }
    
    // Load rules for the resource
    $this->loadRules();
  }
  
  /**
   * Returns true if the given user is permitted to perform the given action on
   * the ACL's Resource.
   *
   * @param User $user
   * @param Action|string $action An Action object or the name of an action.
   * 
   * @return boolean
   */
  public function isAllowed( User $user, $action )
  {
    // If given an Action, convert it to a string
    if( $action instanceof Action )
      $action = $action->getName();
    // Test each of the user's roles
    foreach( $roles as $role )
    {
      if( parent::isAllowed($role, $this->resource, $action) )
        return true;
    }
    
    return false;
  }
  
  /**
   * Adds a Role to the ACL.
   *
   * @param Role $role The Role to add.
   * 
   * @return ResourceAcl Provides fluent interface.
   */
  public function addRole( Role $role )
  {
    // If we already have the role, don't bother
    if( $this->hasRole($role) )
      return $this;
    
    // If the role has a parent
    $parent = $role->getParent();
    if( $parent != null )
    {
      // Load the parent
      $parent = $this->loadRole( $role->getParent() );
      // Add the parent
      $this->addRole($parent);
    }
    
    // Add the role to the Zend ACL (that this subclass wraps)
    parent::addRole($role, $parent);
    
    return $this;
  }
  
  /**
   * Loads the resource's rules.
   */
  protected function loadRules()
  {
    // Get the resource's rules
    $rules = $this->resource->getComponents('Rule');
    
    // Get a rule mapper
    $rule_mapper = Mapper::getMapper('Rule');
    
    // Go through rules, loading and adding components to the ACL
    foreach( $rules as $rule )
    {
      // Load the rule's roles
      $roles = $rule_mapper->loadComponents($rule, 'Role');
      // Load the rule's actions
      $actions = $rule_mapper->loadComponents($rule, 'Action');
      
      // Add the roles to the ACL
      $this->addRoles($roles);
      
      // Permit/forbid the roles to perform the actions
      $this->permitRoles($roles, $actions, $rule->getPermit() == Rule::FORBID);
    }
  }
  
  /**
   * Takes a set of roles and a set of actions and either permits the roles to
   * perform the actions, or forbids it.
   *
   * @param array $roles An array of Roles.
   * @param array $actions An array of Actions.
   * @param boolean $forbid
   */
  protected function permitRoles( array $roles, array $actions, $forbid = false )
  {
    // Convert array of Actions to array of strings
    foreach( $actions as $key => $action )
    {
      $actions[$key] = $action->getName();
    }
    
    if( $forbid === true )
    {
      // Deny actions/role combinations
      foreach( $roles as $role )
      {
        $this->deny( $role, $this->resource, $actions );
      }
    }
    else
    {
    // Deny actions/role combinations
      foreach( $roles as $role )
      {
        $this->allow( $role, $this->resource, $actions );
      }
    }
  }
  
  /**
   * Loads a resource, given a resource id.
   * 
   * Stores the loaded resource in $this->resource.
   * Returns nothing.
   *
   * @param ResourceId $resource_id
   */
  protected function loadResource( ResourceId $resource_id )
  {
    // Get a mapper
    $mapper = Mapper::getMapper( $resource_id->getClass() );
    // Load the resource
    $this->resource = $mapper->loadModel( array(
        'id' => $resource_id->getId()
    ));
    // Load the components
    $mapper->loadComponents( $this->resource );
  }
  
  /**
   * Loads a role and returns it.
   * 
   * @param ResourceId $role_id The ResourceId of the Role to load.
   * 
   * @return Role
   */
  protected function loadRole( ResourceId $role_id )
  {
    // Load the role
    $role = Mapper::loadModel( array(
        $role_id->getClass(),
        $role_id->getId()
    );
    
    return $role;
  }
  
  protected $resource = null;
}
