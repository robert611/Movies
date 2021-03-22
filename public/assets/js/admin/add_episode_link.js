const addLinkInputRowButton = document.getElementById('add-link-input-row-button');

const addLinkInputRow = (e) => {
    e.preventDefault();

    let linkInputRows = document.querySelectorAll('#link-input-rows-container .row');
    inputRowNumber = parseInt(linkInputRows[linkInputRows.length - 1].getAttribute('data-number')) + 1;

    let linkInputRowsContainer = document.getElementById('link-input-rows-container');

    let lastAddedLinkInputRow = document.getElementById('last-added-link-input-row');

    if (linkInputRowsContainer  == undefined) throw new Error('Container(div) for link input rows is undefined, so there is no place to put another link form.');

    if (lastAddedLinkInputRow == undefined) throw new Error('Last link inputs row is undefined, so there is no row after which another can be added. It can be result of incorret work of delete link form script.');

    let formRow = createFormRow(inputRowNumber);

    lastAddedLinkInputRow.removeAttribute('id');

    linkInputRowsContainer.insertBefore(formRow, lastAddedLinkInputRow.nextSibling);

    updateLinkInputRowDeleteButtons();
}

const createFormRow = (rowNumber) => {
    let formRow = document.createElement('div');

    formRow.classList.add('row');
    formRow.classList.add('mt-2');

    formRow.setAttribute('id', 'last-added-link-input-row');
    formRow.setAttribute('data-number', rowNumber);

    /* Url Input Group */
    let urlInputGroup = document.createElement('div');
    let urlLabel = document.createElement('label');
    let urlInput = document.createElement('input');

    urlInputGroup.classList.add('form-group');
    urlInputGroup.classList.add('col-md-6');

    urlLabel.textContent = 'Url';

    urlInput.setAttribute('type', 'text');
    urlInput.setAttribute('name', 'links_urls[]');
    urlInput.classList.add('form-control');

    urlInputGroup.appendChild(urlLabel);
    urlInputGroup.appendChild(urlInput);

    /* Name Input Group */
    let nameInputGroup = document.createElement('div');
    let nameLabel = document.createElement('label');
    let nameInput = document.createElement('input');

    nameInputGroup.classList.add('form-group');
    nameInputGroup.classList.add('col-md-5');

    nameLabel.textContent = 'Name';

    nameInput.setAttribute('type', 'text');
    nameInput.setAttribute('name', 'links_urls_names[]');

    nameInput.classList.add('form-control');

    nameInputGroup.appendChild(nameLabel);
    nameInputGroup.appendChild(nameInput);

    /* Delete form Input Group */
    let deleteInputRowGroup = document.createElement('div');
    let deleteLabel = document.createElement('label');
    let deleteButton = document.createElement('a');
    let deleteIcon = document.createElement('i');

    deleteInputRowGroup.classList.add('form-group');
    deleteInputRowGroup.classList.add('col-md-1');

    deleteLabel.textContent = 'Delete';

    deleteButton.className = 'btn btn-danger form-control delete-link-input-row-button';
    deleteButton.setAttribute('data-number', rowNumber);
    
    deleteIcon.className = 'fas fa-trash-alt';
    deleteIcon.setAttribute('aria-hidden', 'true');

    deleteButton.appendChild(deleteIcon);
    deleteInputRowGroup.appendChild(deleteLabel);
    deleteInputRowGroup.appendChild(deleteButton);
 
    /* Append Input groups to row */
    formRow.appendChild(urlInputGroup);
    formRow.appendChild(nameInputGroup);
    formRow.appendChild(deleteInputRowGroup);

    return formRow;
}

addLinkInputRowButton ? addLinkInputRowButton.addEventListener('click', addLinkInputRow) : null;