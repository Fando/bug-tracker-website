// returns empty string when valid or error msgs when invalid
function validateEmployeeNumber(employee_number) { 
	return employee_number.match(/^[0-9]{4}$/g) ? "" : 
	"Empoyee number should be 4 digits long."; 
}
function validateUsername(username) {
	return (username.match(/^[a-z0-9]{4,12}$/i)!=null) ? "" : 
	"Username should be be 4-12 characters and contain only letters and digits."; 
}
function validateEmail(email) {
	return email.match(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i) ? "" : 
	"Email address is invalid.";
}
function validatePassword(password) {
	return (password.length >= 4 && password.length <= 12 && (password.match(/[0-9]/g)!=null) ? "" :
	"Password should be 4-12 characters and contain at least 1 digit.");
}
function validatePasswordsMatch(pass1, pass2) {
	return pass1.match(pass2) ? "":
	"Passwords do not match";
}	

