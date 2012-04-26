<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mapper
 *
 * @author erwin
 */
abstract class Mapper {

   protected abstract function prepareEnvironment();

   public abstract function deleteMappings();

   public abstract function setMappingArray(AttributeMappingArray $array);
   
}

?>
