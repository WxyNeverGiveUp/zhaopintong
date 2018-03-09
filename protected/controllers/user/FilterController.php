<?php
class FilterController extends Controller {
  
    public function filters() {
        return array(
            array('application.controllers.filters.SessionCheckFilter')
        );
    }
}
?>