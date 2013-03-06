<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Alfred
 *
 * Alfred is a convience Service Layer between Codeigniter controllers and Datamapper ORM
 * It's purpose is to 
 *
 * @license 	MIT License
 * @author  	Steve Wanless
 * @link		  http://www.seeyouontheflipside.com
 * @link		  http://www.github.com/stevewanless
 * @version 	0.1
 */
 
class Alfred {

	function __get($sub) {

		// subblasses are prefixed
		$subclass = 'Alfred_'.$sub;
    
		// store the lowercase name
		$subclass_lc = strtolower($subclass);

		// check and see if there is a custom class file to include
		if ( !class_exists($subclass) ) {
      
			// check captialized or lowercase name
			foreach (array(ucfirst($subclass_lc), $subclass_lc) as $class) {
				$filepath = APPPATH.'libraries/alfred/'.$class.'.php';
				
				if ( file_exists($filepath) ) {
					include_once $filepath;
					break;
				}
				
			}
			
			// no custom file exists, return default base object at the bottom of this file
			if ( !class_exists($subclass) ) {			
    		$subclass = 'Alfred_base';		
			}			
			
		}
    
    // Pass in the child name for use with datamapper naming as if there is no custom class it doesn't know what it's named
		$obj = new $subclass($sub);
		$this->$sub = $obj;
		return $this->$sub;

	}

}
// Alfred Class

class Alfred_base {
  
  public function __construct($name) {
    $this->name = $name;
    
    // check to see if Model exists
    if ( !class_exists( ucfirst($this->name) ) ) {
      
      show_error("Model '".ucfirst($this->name)."' was not found. Please create the model in /application/models so Datamapper can find and use it.");
    
    } 
       
  }  
  
  
	// --------------------------------------------------------------------

	/**
	 * getNew
	 *
	 * The base new function
	 * Returns an new object
	 *
	 * @param	none
	 *
	 * returns object
	 */  	
	 
	public function getNew() {

    // create model
    $model = new $this->name();
    
    //return epmty object
    return $model;
    
	}  
  	
  	
	// --------------------------------------------------------------------

	/**
	 * get
	 *
	 * The base get function
	 * Loops through $params and builds Datamaper Model Object
	 *
	 * @param	multidimensional array of paramaters 
	 * @param	string of record to get
	 */  	
	 
	public function get($params = NULL) {

    // create model
    $model = new $this->name();
    
    // if $params is NULL, return epmty object, otherwise build query          
    if ( $params != NULL ) {
      
      if ( is_string($params) ) {
        
        $model->where('id', $params);
        
      } else {

        foreach($params as $key => $value) {
  
          if ( is_array($value) ) {
            
            foreach($value as $key2 => $value2) {          
              
              $model->$key($key2, $value2);                            
              
            }
            
          } else {
          
            $model->$key($value);

          }
          
        }
      
      } 
      
      /*
       * Depending on your application architecture, you might want to add custom code here
       * For example, if all of your tables have an active column, you could always force that to be set everytime get() is called
       * $model->where('active', '1');
       */
          
      $model->get();      
      
    } else {
    
      show_error('No $params in alfred->get()');        
    
    }

    // return object      
    return $model;
    
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Save
	 *
	 * The base save function
	 * 
	 * Note:
	 * If it find $array['id'] it will update the existing record
	 * If not a new record will be created
	 *
	 * @param array of values to set for object
	 */  		
	 
	public function save($var, $related = NULL ) {

    // create model
    $model = new $this->name();	     

    // if existing record, get record to update 
    if ( isset($var['id']) ) {
      $model->where('id', $var['id']);
      $model->get();
    }
    
    // set values
    foreach ($var as $key => $value) {
      $model->$key = $value;
    }
    
    // save object
    if ( is_object($related) ):
      $model->save($related);
    elseif( is_array($related) ):          
      // takes an array where the key is the name of the object and the value is a comma separated list of ids
      
      // store current name to reset later
      $old_name = $this->name;
      
      // loop through the related array so we can save lots of relationships at once
      foreach($related as $key => $value):
        
        if ($value):
        
          // make an array out of the ids                
          $ids = explode(',', $value);
          
          if ( count($ids) > 0 ):
            // set current name
            $this->name = $key;      
            
            // remove relationships no longer present in $ids      
            foreach ( $model->$key->all as $obj ) {
              if( in_array( $obj->id, $ids) ) {
                // only save new ones, not existing ones. 
                $ids = array_diff($ids, array($obj->id) );
              } else {
                // delete relationship
                // get object by id
                $obj = $this->get( trim($obj->id) );
                
                // save to main object
                $model->delete($obj);                
              }
                    
            }                        
            
            // save new relationships
            foreach($ids as $id):
              // get object by id
              $obj = $this->get( trim($id) );
              
              // save to main object
              $model->save($obj);
              
            endforeach;              
            
          endif;
          
        endif;

      endforeach;
      
      // reset name in case it's still going to be used
      $this->name = $old_name;

    endif;
    
    // make sure everything is saved
    $model->save();        
    
    // return object      
    return $model;
	 
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * populate
	 *
	 * The base populate function. Sets array values to object
	 *
	 * @param array of values to set for object
	 */  		
	 
	public function populate($var, $related = NULL) {
	 
    // create model
    $model = new $this->name();	     

    // if existing record, get record to update 
    if ( isset($var['id']) ) {
      $model->where('id', $var['id']);
      $model->get();
    }
    
    // set values
    foreach ($var as $key => $value) {
      $model->$key = $value;
    }
    
    // save object
    if ( is_object($related) ):
      $model->save($related);
    elseif( is_array($related) ):          
      // takes an array where the key is the name of the object and the value is a comma separated list of ids
      
      // store current name to reset later
      $old_name = $this->name;
      
      // loop through the related array so we can save lots of relationships at once
      foreach($related as $key => $value):
        
        // make an array out of the ids                
        $ids = explode(',', $value);
        
        // set current name
        $this->name = $key;
        
        foreach($ids as $id):
          // get object by id
          $obj = $this->get( trim($id) );
          
          // save to main object
          $model->save($obj);
          
        endforeach;              

      endforeach;
      
      // reset name in case it's still going to be used
      $this->name = $old_name;
      
    else:    
      $model->save();    
    endif;
    
    // return object      
    return $model;
	 
	}	
	
	
	// --------------------------------------------------------------------

	/**
	 * delete
	 *
	 * The base delete function
	 *
	 * @param	string id of record to delete
	 */  		
	 
	public function delete($id = NULL) {
	 
    // check to see if $id is given
    if ( $id == NULL ) {      
      show_error("ID not supplied to Alfred Delete.");    
    }	   
	 
    // create model
    $model = new $this->name();	     
    
    // get record
    $model->where('id', $id);
    $model->get();
    
    // delete record
    $model->delete();	 
    
    if ( $model->exists() ) {
      return true;
    } else {
      return false;
    }    
	 
	}



}
// Alfred_base

/* End of file alfred.php */
/* Location: ./system/libraries/alfred/alfred.php */