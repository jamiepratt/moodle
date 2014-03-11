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
 * ${filedescription}
 *
 * @package    ${package}_{subpackage}
 * @copyright  2014 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\responses;


class analysis_for_question_all_tries extends analysis_for_question{
    /**
     * @param int      $variantno               variant number
     * @param \array[] $responsepartsforeachtry for question with multiple tries we expect an array with first index being try no
     *                                          then second index is subpartid and values are \question_classified_response
     */
    public function count_response_parts($variantno, $responsepartsforeachtry) {
        foreach ($responsepartsforeachtry as $try => $responseparts) {
            foreach ($responseparts as $subpartid => $responsepart) {
                $this->get_analysis_for_subpart($variantno, $subpartid)->count_response($responsepart, $try);
            }
        }
    }

    public function has_multiple_tries_data() {
        return true;
    }

    /**
     * What is the highest number of tries at this question?
     *
     * @return int try number
     */
    public function get_maximum_tries() {
        $max = 1;
        foreach ($this->get_variant_nos() as $variantno) {
            foreach ($this->get_subpart_ids($variantno) as $subpartid) {
                $max = max($max, $this->get_analysis_for_subpart($variantno, $subpartid)->get_maximum_tries());
            }
        }
        return $max;
    }

}
