<?php

/**
 * {filename}.php
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

interface ComponentMapperInterface
{
  public function loadComponents( CompositeModel $composite, $criteria = array() );
  public function saveComponents( CompositeModel $composite, $criteria = array() );
  public function deleteComponents( CompositeModel $composite, $criteria = array() );
}
