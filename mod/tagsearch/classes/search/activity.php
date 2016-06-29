<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Tag Search activities search area.
 *
 * @package    mod_tagsearch
 * @copyright  2016 Norbert Czirjak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_tagsearch\search;

defined('MOODLE_INTERNAL') || die();

class activity extends \core_search\area\base_activity {
    
    
    public function get_recordset_by_timestamp($modifiedfrom = 0) {
         
      
        return false;
    }

   
    public function get_document($record, $options = array()) {
      
     return false;
 
    }
    
    
    public function uses_file_indexing() {
        
        return false;
    }    
}
