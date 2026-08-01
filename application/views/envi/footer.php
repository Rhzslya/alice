<div class="modal" id="page-blocker">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-body text-center">
			<i class="fas fa-circle-notch fa-spin fa-4x text-white"></i>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.menu-question').click(function(){
			$('#page-blocker').modal('show');
		});
	});
</script>

</body>
</html>