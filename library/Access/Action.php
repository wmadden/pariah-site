<?php

/**
 * {filename}.php
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

class Action extends Resource
{
  public function __construct()
  {
    $this->addField('name');
  }
}
