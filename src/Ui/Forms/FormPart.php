<?php

namespace Osm\Ui\Forms;

use Osm\Data\Sheets\Search;

interface FormPart
{
    public function addFormPartToSearch(Search $search);

    /**
     * @param object $data
     */
    public function assignFormPartValue($data);
}