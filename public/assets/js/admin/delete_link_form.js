function updateLinkInputRowDeleteButtons()
{
    let deleteLinkFormButtons = document.getElementsByClassName('delete-link-input-row-button');

    Array.from(deleteLinkFormButtons).forEach(function (button) {
        button.addEventListener('click', deleteLinkInputRow);
    });
}

function deleteLinkInputRow(e)
{
    e.preventDefault();

    if (!confirm('Are you sure? This change cannot be restored.')) return;

    /* It has to be (this) because it refers to anchor element even if I will click directly on an icon element in an anchor element */
    let formNumber = this.getAttribute('data-number');
  
    let rows = document.querySelectorAll('#link-input-rows-container .row');
    
    let element;

    for (let row of Array.from(rows))
    {
        if (row.getAttribute('data-number') == formNumber)
        {
            element = row;
            break;
        }
    }

    /* If this is the last element, than it contains last-added-link-input-row id and this id has to be assign to new last element */
    if (element.getAttribute('id') == 'last-added-link-input-row')
    {
        let previousSibling = element.previousSibling;

        while (previousSibling.nodeName == "#text") {
            previousSibling = previousSibling.previousSibling;
        }

        previousSibling.setAttribute('id', 'last-added-link-input-row');
    }

    /* Create an effect of deleting element, after that change display to none, so element will not leave any space */
    element.style.transitionTimingFunction = 'ease-in';
    element.style.transition = '0.6s';
    element.style.transform = "scale(0.0)";

    setTimeout(function () {
        element.style.display = "none";
        element.remove();
    }, 610);
}

updateLinkInputRowDeleteButtons();