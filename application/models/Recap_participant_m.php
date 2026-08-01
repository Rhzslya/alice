<?php
class Recap_participant_m extends MY_Model {
	public function __construct() {
		parent::__construct();

		$this->table_name = "`cba_ukp_recap_1_0`.`tech_participant`";
	}
}
?>