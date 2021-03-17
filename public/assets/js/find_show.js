const searchShowForm = document.getElementById('search-show-form');

searchShowForm.addEventListener('submit', (e) => { 
    e.preventDefault() 
});

function findMatchingShows(shows)
{  
    let searchedTerm = document.getElementById('search-show-input').value;
    let resultsContainer = document.getElementById('search-results-container');

    resultsContainer.innerHTML = '';

    if (searchedTerm.length >= 1)
    {    
        Array.from(shows).forEach((show) => {
            const name = show.name.toLowerCase();

            if (name.toLowerCase().indexOf(searchedTerm.trim().toLowerCase()) != -1)
            {
                resultsContainer.parentElement.style.display = 'block';
                resultsContainer.appendChild(createShowWidget(show));
            }
        });
    }
  
    if (resultsContainer.children.length == 0) {
        resultsContainer.parentElement.style.display = 'none';
    }
}

function createShowWidget(show)
{
    let a = document.createElement('a');
    a.setAttribute('href', `/show/${show.databaseTableName}/1/1`);
    a.classList.add('text-decoration-none');

    let wrapper = document.createElement('div');
    wrapper.setAttribute('class', 'col-xs-12 col-md-2 mb-2');

    let card = document.createElement('div');
    card.setAttribute('class', 'card');

    let cardBody = document.createElement('div');
    cardBody.setAttribute('class', 'card-body');

    let p = document.createElement('p');
    p.setAttribute('class', 'card-text');
    p.textContent = show.name;

    cardBody.appendChild(p);

    let img = document.createElement('img');
    img.setAttribute('class', 'card-img-top');
    img.setAttribute('width', '100%');
    img.setAttribute('height', '170px');
    img.setAttribute('src', '/assets/pictures/shows/' + show.picture);
    img.setAttribute('alt', 'Picture presenting ' + show.name);

    card.appendChild(img);
    card.appendChild(cardBody);

    a.appendChild(card);

    wrapper.appendChild(a);

    return wrapper;
}

fetch('/api/shows/fetch')
    .then(response => response.json())
    .then(json => JSON.parse(json))
    .then(data => document.getElementById('search-show-input').addEventListener('keyup', () => findMatchingShows(data)));