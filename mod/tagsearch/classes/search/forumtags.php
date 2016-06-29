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
 * Tag Search tags search area.
 *
 * @package    mod_tagsearch
 * @copyright  2016 Norbert Czirjak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace mod_tagsearch\search;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/tagsearch/lib.php');

class forumtags extends \core_search\area\base_mod {

  

    /**
     * Returns recordset containing required data for indexing tags.
     *
     * @param int $modifiedfrom timestamp
     * @return moodle_recordset
     */
    public function get_recordset_by_timestamp($modifiedfrom = 0) {
        global $DB;
        
         $sql = '
             SELECT DISTINCT(t.id), f.id as forumid, t.name as tagname, 
                            t.timemodified as timemodified, t.userid as userid, f.name as forumname, ti.*,
                            f.course as courseid             
               FROM {tag_instance} as ti 
               LEFT JOIN {tag} as t on t.id = ti.tagid
               LEFT JOIN {course_modules} as cm on cm.id = ti.itemid 
               LEFT JOIN {forum} as f on f.course = cm.course
               WHERE ti.itemtype = "course_modules" and cm.id is not null
               
             ';
        
        return $DB->get_recordset_sql($sql);
    }

    /**
     * Returns the document associated with this tag id.
     *
     * @param stdClass $record Post info.
     * @param array    $options
     * @return \core_search\document
     */
    public function get_document($record, $options = array()) {
         
        $context = \context_course::instance($record->courseid);
        
        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($record->forumname, false));
        $doc->set('content', content_to_text('FORUM TAG: '.$record->tagname, false));
        $doc->set('contextid', $context->id);
        $doc->set('courseid', $record->courseid);
        $doc->set('description1', "Forum id:");
        $doc->set('description2', $record->itemid);
        $doc->set('userid', $record->userid);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $record->timemodified);
      
        // Check if this document should be considered new.
        if (isset($options['lastindexedtime']) && ($options['lastindexedtime'] < $record->timemodified)) {
        //	if (isset($options['lastindexedtime']) ) {
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
     * Link to the tag forum 
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        
        
        $forumid = $doc->get('description2');
	
        return new \moodle_url('/mod/forum/view.php', array('id' => $forumid));
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
