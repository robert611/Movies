let searchVisitsFromGivenDateFormButton = document.getElementById('search-date-form-button');

searchVisitsFromGivenDateFormButton.addEventListener('click', (e) => {
    e.preventDefault();

    let date = document.getElementById('search-date-form-input').value;

    window.location.href = '/admin/visitors/page/filtered/' + date;
});