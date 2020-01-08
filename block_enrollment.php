<?php

require_once(dirname(__FILE__) . '/../../config.php');

class block_enrollment extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_enrollment');
    }

    function instance_allow_multiple() {
        return false;
    }

    public function applicable_formats() {
        return array('all' => true);
    }

    function get_content() {
        global $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        // shortcut -  only for admins
        if (!isloggedin() || isguestuser() || !is_siteadmin()) {
            return false;
        }
        $this->content = new stdClass();
        $this->content->footer = '';
        $this->content->text = '<a href="'.$CFG->wwwroot.'/blocks/enrollment/index.php">'.get_string('enrolusers', 'block_enrollment').'</a>';

        //Some extra functions that go alonge with enrolling a user, like adding a user and viewing existing users
        $this->content->text .= '<br><a href="'.$CFG->wwwroot.'/user/editadvanced.php?id=-1">'.get_string('addnewuser', 'block_enrollment').'</a>';
        $this->content->text .= '<br><a href="'.$CFG->wwwroot.'/admin/user.php">'.get_string('browseusers', 'block_enrollment').'</a>';
        //$this->content->text .= '<br><a href="'.$CFG->wwwroot.'/admin/user/user_bulk.php">'.get_string('bulkuseractions', 'block_enrollment').'</a>';

        return $this->content;
    }

}
