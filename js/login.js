$(document).ready(function() {	
	$("#btn-submit").click(function(event) {
		event.preventDefault();

		var name =  $.trim($("#txt-username").val());
		var password =  $.trim($("#txt-password").val());
		var validationResults = validateLoginInfo(name, password);
		
		if (validationResults.status == "OK") {
			sendLoginRequest(name, password);
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
	function validateLoginInfo(name, password) {
		var strErrors = "";		
		strErrors += validateUsername(name) + "\n";
		strErrors += validatePassword(password) + "\n";
		strErrors = strErrors.trim();
		strErrors = strErrors.replace(/\n\n/g, '\n');
		return { status: (strErrors === "") ? "OK" : "ERROR", messages: [strErrors.split("\n")] };
	}	
	function sendLoginRequest(name, password) {
		$.post("includes/login_submit.php", {
					username: name,
					password: password,
				}, 
				function(jsonString) {										
					$("body").append(jsonString);
					console.log(jsonString);
					var response_label = $('#response-label');	
					var respObj = JSON.parse(jsonString);
					
					if (respObj.status == 'OK') {						
						if (respObj.redirect != "") {
							window.location.replace(respObj.redirect);
						}
						else {
							// show success msg
							response_label.removeClass('red').addClass('green');
							response_label.text("Login successful!");
						}
					}
					else if (respObj.status == 'ERROR') {
						$('body').append(jsonString);
						// show error msgs
						response_label.removeClass('green').addClass('red');
						response_label.html(respObj.messages.join("<br/>"));
					}
		});
	}
	/*
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
		$.post("clear_db.php", {}, 
				function(jsonString) {											
					console.log(jsonString);										
					$('body').append(jsonString + "<br\>");
				});
	});
	*/
});