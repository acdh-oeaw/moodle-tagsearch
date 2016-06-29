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
 * Tag Search course meta search area.
 *
 * @package    mod_tagsearch
 * @copyright  2016 Norbert Czirjak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_tagsearch\search;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/tagsearch/lib.php');


class coursemeta extends \core_search\area\base_mod {

   

    /**
     * Returns recordset containing required data for indexing course meta.
     *
     * @param int $modifiedfrom timestamp
     * @return moodle_recordset
     */
    public function get_recordset_by_timestamp($modifiedfrom = 0) {
        
        global $DB;
        $sql = 'SELECT 
                    cid.id, c.id as courseid, c.timemodified, c.fullname as coursename, 
                    cif.name as keyname, cid.data as keyvalue, cid.id as itemid, 
                    cif.datatype as datatype, cif.defaultdata as defaultdata, cif.param1 as param1
                FROM 
                    {custom_info_data} as cid 
                LEFT JOIN 
                    {custom_info_field} as cif on cid.fieldid = cif.id 
                LEFT JOIN 
                    {course} as c on c.id = cid.objectid 
                WHERE 
                    c.visible = 1 
                ';
        //ti.itemtype = "course" and
        //die(print_r($sql, true));
        return $DB->get_recordset_sql($sql);
    }

    /**
     * Returns the document associated with this meta id.
     *
     * @param stdClass $record Post info.
     * @param array    $options
     * @return \core_search\document
     */
    public function get_document($record, $options = array()) {
        
        
        $context = \context_course::instance($record->courseid);
      
        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($record->coursename, false));
        
        
        if($record->datatype == "menu")
        {
            $fields = explode("\n", $record->param1);            
            $menuValue = $fields[$record->keyvalue];
            $doc->set('content', content_to_text('Course METAKEY: '.$record->keyname.' VALUE: '.$menuValue , false));
        }
        elseif($record->datatype == "datetime")
        {
            $dateTimeVal = date("d-M-Y h:i:s", $record->keyvalue);            
            $doc->set('content', content_to_text('Course METAKEY: '.$record->keyname.' VALUE: '.$dateTimeVal , false));            
        }
        else
        {
            $doc->set('content', content_to_text('Course METAKEY: '.$record->keyname.' VALUE: '.$record->keyvalue , false));
        }
        
        $doc->set('contextid', $context->id);
        $doc->set('courseid', $record->courseid);	
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $record->timemodified);
 
        // Check if this document should be considered new.
        //if (isset($options['lastindexedtime']) && ($options['lastindexedtime'] < $record->created)) {
        	if (isset($options['lastindexedtime']) ) {
            // If the document was created after the last index time, it must be new.
            $doc->set_is_new(true);
        }

        return $doc;
    }

    /**
     * Returns true if this area uses file indexing.
     *
     * @return bool
     */
    public function uses_file_indexing() {
        
        return true;
    }

    

    /**
     * Whether the user can access the tagid.
     *
     * @throws \dml_missing_record_exception
     * @throws \dml_exception
     * @param int $id Tag id
     * @return bool
     */
    public function check_access($id) {
                
        return \core_search\manager::ACCESS_GRANTED;
    }

    /**
     * Link to the tag course 
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        
        
        $courseid = $doc->get('courseid');
        $tagid =  $doc->get('itemid');
        
        return new \moodle_url('/course/view.php', array('id' => $courseid));
    }

    /**
     * Link to the tag cloud.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        
        $contextmodule = \context::instance_by_id($doc->get('contextid'));
        
        return new \moodle_url('/tag/search.php');
        
    }

}
