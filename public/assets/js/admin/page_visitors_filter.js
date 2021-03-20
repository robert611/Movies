let trigger = document.getElementById('search-show-visits-button');

trigger ? trigger.addEventListener('click', (e) => {
    e.preventDefault();

    let searchedTerm = document.getElementById('search-form-input-show-name').value.trim();
    let dataCellsWithShowsNames = document.querySelectorAll('.show-name');
    let searchResultsDiv = document.getElementById('search-show-result');

    if (searchResultsDiv == undefined) {
        alert('Error. Div for found shows does not exist.');
        return;
    }

    searchResultsDiv.innerHTML = '';

    let matchingShowsRows = new Array();
    let matchingShowsNames = new Array();

    dataCellsWithShowsNames.forEach((dataCellWithShowName) => {
        if (dataCellWithShowName.textContent.indexOf(searchedTerm) != -1) {
            matchingShowsNames.indexOf(dataCellWithShowName.textContent) == -1 ? matchingShowsRows.push(dataCellWithShowName.parentNode): null;    
            matchingShowsNames.push(dataCellWithShowName.textContent);
        }
    });

    if (matchingShowsRows.length == 0 || searchedTerm == '') {
        searchResultsDiv.appendChild(createWarning('There is no such show which name includes: ' + searchedTerm));
        return;
    }

    searchResultsDiv.appendChild(createTable(matchingShowsRows)); 
}): null;

function createTable(showsTableRows)
{
    let table = document.createElement('table');
    table.className = 'table table-bordered';

    let tbody = document.createElement('tbody');

    showsTableRows.forEach((showTableRow) => {
        tbody.appendChild(showTableRow.cloneNode(true));
    });
    
    table.appendChild(tbody);

    return table;
}

function createWarning(message)
{
    let div = document.createElement('div');

    div.className = "alert alert-warning";
    div.textContent = message;

    return div;
}