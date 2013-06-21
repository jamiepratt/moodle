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
 * Unit tests for the shortanswer question type class.
 *
 * @package    qtype
 * @subpackage shortanswer
 * @copyright  2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/shortanswer/questiontype.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the shortanswer question type class.
 *
 * @copyright  2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_shortanswer_test extends advanced_testcase {
    public static $includecoverage = array(
        'question/type/questiontypebase.php',
        'question/type/shortanswer/questiontype.php',
    );

    protected $qtype;

    protected function setUp() {
        $this->qtype = new qtype_shortanswer();
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    protected function get_test_question_data() {
        return test_question_maker::get_question_data('shortanswer');
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'shortanswer');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $q = test_question_maker::get_question_data('shortanswer');
        $q->options->answers[15]->fraction = 0.1;
        $this->assertEquals(0.1, $this->qtype->get_random_guess_score($q));
    }

    public function test_get_possible_responses() {
        $q = test_question_maker::get_question_data('shortanswer');

        $this->assertEquals(array(
            $q->id => array(
                13 => new question_possible_response('frog', 1),
                14 => new question_possible_response('toad', 0.8),
                15 => new question_possible_response('*', 0),
                null => question_possible_response::no_response()
            ),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_get_possible_responses_no_star() {
        $q = test_question_maker::get_question_data('shortanswer', 'frogonly');

        $this->assertEquals(array(
            $q->id => array(
                13 => new question_possible_response('frog', 1),
                0 => new question_possible_response(get_string('didnotmatchanyanswer', 'question'), 0),
                null => question_possible_response::no_response()
            ),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_question_saving_frogtoad() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $questiondata = test_question_maker::get_question_data('shortanswer');
        $formdata = test_question_maker::get_question_form_data('shortanswer');

        list($cat, $systemcontext, $form) = $this->get_question_editing_form($questiondata);

        $form->set_data(clone($questiondata));
        $formdata->category = "{$cat->id},{$systemcontext->id}";
        $form->mock_submit((array)$formdata);
        $fromform = $form->get_data();

        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestionsdata = question_load_questions(array($returnedfromsave->id));
        $actualquestiondata = end($actualquestionsdata);

        $this->assertEquals($this->normalize_question_data($questiondata), $this->normalize_question_data($actualquestiondata));
    }

    /**
     * Used before comparison. Remove properties we don't want to compare, ones we don't want to predict or test.
     * @param object  $questiondataasobject
     * @return array  the question data with some elements we don't want to predict removed. Array comparison gives a nice diff
     *                      in my IDE if there is a failure.
     */
    protected function normalize_question_data($questiondataasobject) {
        // Remove some properties we don't want to compare.
        foreach (array('version', 'timemodified', 'timecreated') as $propertytounset) {
            unset($questiondataasobject->{$propertytounset});
        }
        $questiondataasobject->options->answers = $this->normalize_answer_array($questiondataasobject->options->answers);
        $questiondata = (array)$questiondataasobject;
        ksort($questiondata);
        return $questiondata;
    }

    protected function normalize_answer_array($answers, $decimals = 7) {
        $normalized = array();
        foreach ($answers as $answer) {
            $normalized[$answer->answer] = new stdClass();
            foreach ($answer as $property => $value) {
                if (!in_array($property, array('answer', 'id', 'question'))) {
                    if (is_float($value)) {
                        $value = number_format($value, $decimals, '.', '');
                    }
                    $normalized[$answer->answer]->{$property} = $value;
                }
            }
        }
        ksort($normalized);
        return $normalized;
    }

    /**
     * Set up a form to create a question in a new category in the system context.
     * @param $questiondata
     * @return array
     */
    protected function get_question_editing_form($questiondata) {
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(array());
        $systemcontext = context_system::instance();
        $contexts = array($systemcontext);
        $questiondata->contextid = $systemcontext->id;
        $questiondata->category = $cat->id;
        $questiondataforformconstructor = clone($questiondata);
        $questiondataforformconstructor->category = $cat->id;
        $questiondataforformconstructor->formoptions = new stdClass();
        $questiondataforformconstructor->formoptions->canmove = true;
        $questiondataforformconstructor->formoptions->cansaveasnew = true;
        $questiondataforformconstructor->formoptions->movecontext = false;
        $questiondataforformconstructor->formoptions->canedit = true;
        $questiondataforformconstructor->formoptions->repeatelements = true;
        $form = $this->qtype->create_editing_form('question.php', $questiondataforformconstructor, $cat, $contexts, true);
        return array($cat, $systemcontext, $form);
    }
}
