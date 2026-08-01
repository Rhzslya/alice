<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1>Welcome</h1>      
    <h3>CBA UKP - DPKP Module <span class="badge badge-danger ml-3"><?=$this->config->item('version')?></span></h3>
  </div>
</div>

<div class="container-fluid mt-4">
	<div class="row">
		<div class="col-12 mt-4 text-center">
			<a href="<?=base_url('report')?>" class="btn btn-primary p-4 mx-2">
				<h2 class="display-6">
					<i class="fa fa-clipboard"></i>&nbsp;Report (CBA)
				</h2>
			</a>
			<a href="<?=base_url('trb')?>" class="btn btn-warning p-4 mx-2">
				<h2 class="display-5">
					<i class="fa fa-clipboard"></i>&nbsp;TRB
				</h2>
			</a>
			<a href="<?=base_url('trb')?>" class="btn btn-info p-4 mx-2">
				<h2 class="display-5">
					<i class="fa fa-clipboard"></i>&nbsp;Comprehensive
				</h2>
			</a>
			<?php if ($this->session->userdata('log_user_category') == 1) : ?>
				<a href="<?=base_url('package')?>" class="btn btn-danger p-4 mx-2">
					<h2 class="display-5">
						<i class="fa fa-archive"></i>&nbsp;Package					
					</h2>
				</a>
			<?php endif; ?>
			<a href="<?=base_url('question')?>" class="btn btn-success menu-question p-4 mx-2">
				<h2 class="display-5">
					<i class="fa fa-question"></i>&nbsp;Question
				</h2>
			</a>
		</div>
	</div>

</div>