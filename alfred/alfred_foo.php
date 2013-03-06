<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Alfred_foo extends Alfred_base {

  /*
   * Override the default get/set/delete methods, but why?   
   */
     
  /*
  public function get() {
    
  }
  */
  
	// --------------------------------------------------------------------  
  
  /*
   * Write custom functions for specific things. 
   * If you find your self writing the same set $params for get, 
   * then you should write it a custom function here
   */
   
  public function getLatest($limit = 3) {

    $model = $this->get(array(
      'order_by' => array(
        'id' => 'DESC'      
      ),
      'limit' => $limit
    ));
    
    return $model;
    
  }

}

// Alfred_example

/* End of file alfred_example.php */
/* Location: ./system/libraries/alfred/alfred_example.php */