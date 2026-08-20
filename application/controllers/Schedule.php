<?php
class Schedule extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->library('im_license');
		if (!$this->im_license->license_valid()) {
			redirect('license');
		}

		if (!$this->im_login->is_login('log_alice')) {
			redirect('login');
		}

		$this->load->library('encrypt');
	}

	/**
	 * Exports every period's exam/participant/score data for one calendar
	 * date under a UKP submission (tech_pengajuan_ukp), as one .cba backup
	 * file -- same encrypted format produced by Report::backup() and
	 * consumed by Report::upload()/do_update(). A UKP can run several
	 * periods (levels) in parallel on the same date, each with its own
	 * tech_day row, so this loops Backup_m::generate_query() once per
	 * matching day and concatenates the results before encoding.
	 */
	function export_daily($uc_ukp = NULL, $date = NULL) {
		if ($uc_ukp == NULL || $date == NULL) {
			show_404();
			return;
		}

		$this->load->model('day_m');
		$query = $this->day_m->get_days_by_ukp_date($uc_ukp, $date);

		if ($query->num_rows() == 0) {
			show_404();
			return;
		}

		$this->load->model('backup_m');

		$all_query = "";
		foreach ($query->result() as $res) {
			$gen = $this->backup_m->generate_query($res->uc);
			$all_query .= $gen['all_query']."\n\r";
		}

		$this->load->model('pengajuan_ukp_m');
		$q_ukp = $this->pengajuan_ukp_m->get_detail($uc_ukp);

		$namefile = 'export_'.$uc_ukp.'_'.$date;
		if ($q_ukp->num_rows() > 0) {
			$row_ukp = $q_ukp->row();
			$namefile = $row_ukp->pukp_label." - ".$row_ukp->upt_label." - [".date('m.d', strtotime($row_ukp->date_start))." - ".date('m.d', strtotime($row_ukp->date_finish))."] - ".date('d M', strtotime($date));
		}

		$this->load->library('zip');
		$this->load->helper('file');

		//CREATE FILE
		$dir = $namefile.".cba";
		$timestamp = strtotime($date);
		touch($dir, $timestamp);

		// Encrypt query
		$en_query = $this->encrypt->encode($all_query);

		//CREATE ZIP
		$this->zip->add_data($dir, $en_query);
		unlink($dir);
		$this->zip->download($namefile.'.zip');
	}
}
?>
