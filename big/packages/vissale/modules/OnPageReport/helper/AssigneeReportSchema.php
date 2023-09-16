<?php
/**
 *
 */
class AssigneeReportSchema {
    public $cancelled_count;
    public $duplicate_count;
    public $total_count;

     /**
     * AssigneeReportSchema constructor.
     * @param $cancelled_count
     * @param $duplicate_count
     * @param $total_count
     * @param $new_count
     */public function __construct($cancelled_count, $duplicate_count, $total_count)
    {
        $this->cancelled_count = $cancelled_count;
        $this->duplicate_count = $duplicate_count;
        $this->total_count = $total_count;

    }
}