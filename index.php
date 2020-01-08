<?php
require_once("../../config.php");

$context = context_system::instance();

if (!has_capability('blocks/enrollment:viewpage', $context)) {
    print_error(get_string('notallowed', 'block_enrollment'));
}

$url = new moodle_url('/blocks/enrollment/index.php');

require_login();

$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_title(get_string('pluginname', 'block_enrollment'));
$PAGE->set_heading(get_string('pluginname', 'block_enrollment'));

$form = new block_enrollment\quickenrol_form();

if ($data = $form->get_data()) {
    if (!$enrol_manual = enrol_get_plugin('manual')) {
        throw new coding_exception(get_string('nomanenrol', 'block_enrollment'));
    }
    foreach ($data->courses as $course) {
        $instance = $DB->get_record('enrol', array('enrol' => 'manual', 'courseid' => $course));
        foreach ($data->userids as $userid) {
            $enrol_manual->enrol_user($instance, $userid, $data->roletoassign, $data->datestart, $data->dateend);
        }
    }
    redirect($url, get_string('userenrolled', 'block_enrollment'));
}
echo $OUTPUT->header();
echo html_writer::tag('p', get_string('description', 'block_enrollment'));

$form->display();

echo $OUTPUT->footer();
