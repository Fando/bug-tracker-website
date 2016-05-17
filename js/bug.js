 $(document).ready(function() {	
	$("#btn-close").click(function(event) {
		event.preventDefault();

		var confirm = window.confirm("Are you sure you want to close this bug?");
		if (confirm) {
			var fixed = window.confirm("Was the bug fixed?");
			
			if (fixed) {
				$("#chk-fixed").prop('checked', fixed);
			}
			$("#chk-closed").prop('checked', true);	
			$("#frm-bug").trigger('submit');
		}
	});
});