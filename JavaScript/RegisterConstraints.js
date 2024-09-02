var daySelect = document.getElementById('day');
for (var i = 1; i <= 31; i++) {
    var option = document.createElement('option');
    option.value = i;
    option.text = i;
    daySelect.appendChild(option);
}

var yearSelect = document.getElementById('year');
for (var i = 2020; i >= 1950; i--) {
    var option = document.createElement('option');
    option.value = i;
    option.text = i;
    yearSelect.appendChild(option);
}
var password = document.getElementById('password');
var confirmPassword = document.getElementById('confirm-password');

function checkPassword() {
    var passwordValue = password.value;
    var confirmPasswordValue = confirmPassword.value;
    if (passwordValue === confirmPasswordValue && passwordValue !== '' && confirmPasswordValue !== '') {
        confirmPassword.classList.remove('wrong');
        confirmPassword.classList.add('correct-password');
    } else {
        confirmPassword.classList.remove('correct-password');
        confirmPassword.classList.add('wrong');
    }
}

password.addEventListener('input', checkPassword);
confirmPassword.addEventListener('input', checkPassword);

document.addEventListener('DOMContentLoaded', function() {
    var monthSelect = document.getElementById('month');
    var daySelect = document.getElementById('day');
    var yearSelect = document.getElementById('year');
    var invalidDateSpan = document.getElementById('Invalid-Date-Input');

    function checkDate() {
        var month = monthSelect.value;
        var day = daySelect.value;
        var year = yearSelect.value;

        var date = new Date(year, month - 1, day);

        if (date && (date.getMonth() + 1) == month && date.getDate() == Number(day)) {
            invalidDateSpan.textContent = '';
            monthSelect.classList.remove('wrong');
            daySelect.classList.remove('wrong');
            yearSelect.classList.remove('wrong');
        } else {
            invalidDateSpan.textContent = 'Invalid Date Format';
            monthSelect.classList.add('wrong');
            daySelect.classList.add('wrong');
            yearSelect.classList.add('wrong');
        }
    }

    // Add event listeners to the select elements
    monthSelect.addEventListener('change', checkDate);
    daySelect.addEventListener('change', checkDate);
    yearSelect.addEventListener('change', checkDate);
});
