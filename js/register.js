$(document).ready(function() {	
	$("#btn-submit").click(function(event) {		
		event.preventDefault();
		
		var employee_number = $.trim($("#txt-employee-number").val());
		var name =  $.trim($("#txt-username").val());
		var email =  $.trim($("#txt-email").val());
		var password =  $.trim($("#txt-password").val());
		var retype_password =  $.trim($("#txt-retype-password").val());
		
		var validationResults = validateRegistrationInfo(employee_number, name, email, password, retype_password);
		
		if (validationResults.status == "OK") {
			sendRegisterRequest(employee_number, name, email, password, retype_password);
		} 
		else {			
			// show error messages
			var response_label = $('#response-label');		
			response_label.removeClass('green').addClass('red');
			response_label.html(validationResults.messages.join("<br/>"));
		}
	});
		
	// returns empty string if valid, otherwise returns an array of error messages
	// the function expects the following args: employee_number, name, email, password, retype_password
	// returns an object indicating status of validation and any errors that were encountered.
	function validateRegistrationInfo(employee_number, name, email, password, retype_password) {
		var strErrors = "";		
		strErrors += validateEmployeeNumber(employee_number) + "\n";
		strErrors += validateUsername(name) + "\n";
		strErrors += validateEmail(email) + "\n";
		strErrors += validatePassword(password) + "\n";
		strErrors += validatePasswordsMatch(password, retype_password);	
		strErrors = strErrors.trim();
		strErrors = strErrors.replace(/\n\n/g, '\n');
		return { status: (strErrors === "") ? "OK" : "ERROR", messages: [strErrors.split("\n")] };
	}
	
	function sendRegisterRequest(employee_number, name, email, password, retype_password) {
		$.post("includes/register_submit.php", {
					employee_number: employee_number,
					username: name,
					email: email,
					password: password,
					retype_password: retype_password
				}, 
				function(jsonString) {		
										
					console.log(jsonString);
					var response_label = $('#response-label');					
					$('body').append(jsonString);					
					
					
					var respObj = JSON.parse(jsonString);
					
					if (respObj.status == 'OK') {
						// show success msg
						response_label.removeClass('red').addClass('green');
						response_label.text("You have successfully registered!");
						//$("form")[0].reset();						
					}
					else if (respObj.status == 'ERROR') {
						// show error msgs
						response_label.removeClass('green').addClass('red');
						response_label.html(respObj.messages.join("<br/>"));
					}
		});
	}
	
	// test function to help register dummy user. 
	$("#btn-test").click(function() {
		var employee_number = 1234;
		var name = 'alex';
		var email = 'afando@gmail.com';
		var password = 1234;
		var retype_password = 1234;		
		$("#txt-employee-number").val(employee_number);
		$("#txt-username").val(name);
		$("#txt-email").val(email);
		$("#txt-password").val(password);
		$("#txt-retype-password").val(retype_password);		
		sendRegisterRequest(employee_number, name, email, password, retype_password);
	});
	
	// test function to delete all users from database
	$("#btn-delete-db").click(function() {		
		$.post("includes/clear_db.php", {}, 
				function(jsonString) {											
					console.log(jsonString);										
					$('body').append(jsonString + "<br\>");
				});
	});
	
});