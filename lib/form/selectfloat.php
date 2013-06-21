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
 * Same as select element but correctly cleans float values.
 *
 * @package   core_form
 * @copyright 2013 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once "$CFG->libdir/form/select.php";

/**
 * select type form element which correctly cleans float values. You can pass in an attribute precision
 * in order to set the precision for comparing floats which defaults to 1.0e-7.
 *
 * @package   core_form
 * @category  form
 * @copyright 2013 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_selectfloat extends MoodleQuickForm_select {

    /**
     * constructor
     *
     * @param string $elementName Select name attribute
     * @param mixed $elementLabel Label(s) for the select
     * @param mixed $options Data to be used to populate options
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     */
    public function MoodleQuickForm_selectfloat($elementName=null, $elementLabel=null, $options=null, $attributes=null) {
        parent::MoodleQuickForm_select($elementName, $elementLabel, $options, $attributes);
    }

    /**
     * @param $optionvalue mixed the option value we are comparing the set value to.
     * @param $setvalue mixed the value set for this element to compare to.
     * @return bool are $optionvalue and $setvalue the same?
     */
    protected function valuesSame($optionvalue, $setvalue) {
        return abs((float)$optionvalue - (float)$setvalue) < $this->getPrecision();
    }

    const DEFAULTPRECISION = 1.0e-7;

    protected function getPrecision() {
        if (is_null($this->getAttribute("precision"))) {
            return self::DEFAULTPRECISION;
        } else {
            return $this->getAttribute('precision');
        }
    }
}
