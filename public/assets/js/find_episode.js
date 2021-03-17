document.getElementById('search-episode-form').addEventListener('submit', (e) => { e.preventDefault() });
document.getElementById('search-episode-input').addEventListener('keyup', () => findMatchingEpisodes());

var wereEpisodesAlreadyLoad = false;
var legnthOfPreviousSearchedTerm = 0;

function findMatchingEpisodes()
{  
    let searchedTerm = document.getElementById('search-episode-input').value;
    let resultsContainer = document.getElementById('search-results-container');

    searchedTerm = searchedTerm.replaceAll("/", ""); 

    if (searchedTerm.length >= 3 && wereEpisodesAlreadyLoad === false)
    {   
        wereEpisodesAlreadyLoad = true;

        hideInformationAboutNoResultsFound();

        showSearchResultsSpinner();

        setSearchEpisodeInputToDisable();

        fetch('/api/episodes/fetch/' + searchedTerm)
            .then((response) => {
                return response.json();
            })
            .then((episodes) => {
                hideSearchResultsSpinner();

                setSearchEpisodeInputToActive();
                focusOnSearchEpisodeInput();

                showFoundEpisodes(episodes, searchedTerm, resultsContainer);
            });
    }
    else if (searchedTerm.length >= 3 && wereEpisodesAlreadyLoad === true)
    {
        let arrayWithShowedEpisodesTitles = document.querySelectorAll('.showed-episode-title');

        /* Iterate through episodes which are currently displayed and hide those which don't match new pattern or show those which do match */
        Array.from(arrayWithShowedEpisodesTitles).forEach((title) => {
            let episodeId = title.getAttribute('data-episodeId');
            let episodeTable = title.getAttribute('data-tableName');

            if (searchedTerm.length > legnthOfPreviousSearchedTerm)
            {
                if (title.innerHTML.toLowerCase().indexOf(searchedTerm.toLowerCase()) === -1)
                {
                    /* Hide episode which doesn't match to pattern */
                    document.getElementsByClassName(episodeTable + episodeId)[0].classList.add('hide');
                }
            }
            else if (searchedTerm.length < legnthOfPreviousSearchedTerm)
            {
                if (title.innerHTML.toLowerCase().indexOf(searchedTerm.toLowerCase()) !== -1) {
                    /* Show episode which does match to pattern */
                    resultsContainer.parentElement.style.display = 'block';
                    hideInformationAboutNoResultsFound();

                    document.getElementsByClassName(episodeTable + episodeId)[0].classList.remove('hide');
                } 
            }
        });

        if (document.querySelectorAll('#search-results-container > div:not(.hide)').length == 0)
        {
            showInformationAboutNoResultsFound();
            resultsContainer.parentElement.style.display = 'none';
        }

        /* Update value of previous length, at the end of the function */
        legnthOfPreviousSearchedTerm = searchedTerm.length;
    }
    else if (searchedTerm.length < 3)
    {
        hideInformationAboutNoResultsFound();

        resultsContainer.parentElement.style.display = 'none';
        wereEpisodesAlreadyLoad = false;

        Array.from(resultsContainer.childNodes).forEach((child) => {
            child.remove();
        });
    }
}

function showSearchResultsSpinner()
{
    let spinner = document.getElementById('search-result-spinner');

    spinner.setAttribute('class', 'show pb-4');
}

function hideSearchResultsSpinner()
{
    let spinner = document.getElementById('search-result-spinner');

    spinner.setAttribute('class', 'hide');
}

function hideInformationAboutNoResultsFound()
{
    let noSearchResultsDiv = document.getElementById('no-search-results');

    noSearchResultsDiv.classList.add('hide');
    noSearchResultsDiv.textContent = "";
}

function showInformationAboutNoResultsFound()
{
    let noSearchResultsDiv = document.getElementById('no-search-results');

    noSearchResultsDiv.classList.remove('hide');
    noSearchResultsDiv.textContent = "Nie znaleziono żadnego odcinka";
}

function setSearchEpisodeInputToDisable()
{
    document.getElementById('search-episode-input').setAttribute('disabled', 'true');
}


function setSearchEpisodeInputToActive()
{
    document.getElementById('search-episode-input').removeAttribute('disabled');
}

function focusOnSearchEpisodeInput()
{
    let searchEpisodeInput = document.getElementById('search-episode-input');

    searchEpisodeInput.focus();
}

function showFoundEpisodes(episodes, searchedTerm, resultsContainer)
{
    Array.from(episodes).forEach((episode) => {
        const title = episode.title.toLowerCase();

        if (title.toLowerCase().indexOf(searchedTerm.trim().toLowerCase()) != -1)
        {
            resultsContainer.parentElement.style.display = 'block';
            resultsContainer.appendChild(createEpisodeWidget(episode));
        }
    });

    if (episodes.length === 0)
    {
        showInformationAboutNoResultsFound();
    }
}

function createEpisodeWidget(episode)
{
    let a = document.createElement('a');
    a.setAttribute('href', `/show/${episode.table_name}/${episode.season}/${episode.episode}`);
    a.classList.add('text-decoration-none');

    let wrapper = document.createElement('div');
    wrapper.setAttribute('class', `col-xs-12 col-md-2 mb-2 ${episode.table_name}${episode.id}`);

    let card = document.createElement('div');
    card.setAttribute('class', 'card');

    let cardBody = document.createElement('div');
    cardBody.setAttribute('class', 'card-body');

    let p = document.createElement('p');
    p.setAttribute('class', 'card-text showed-episode-title');
    p.setAttribute('data-episodeId', episode.id);
    p.setAttribute('data-tableName', episode.table_name);
    p.textContent = episode.name + ' - ' + episode.title;

    cardBody.appendChild(p);

    let img = document.createElement('img');
    img.setAttribute('class', 'card-img-top');
    img.setAttribute('width', '100%');
    img.setAttribute('height', '170px');
    img.setAttribute('src', '/assets/pictures/shows/' + episode.picture);
    img.setAttribute('alt', 'Picture presenting ' + episode.title);

    card.appendChild(img);
    card.appendChild(cardBody);

    a.appendChild(card);

    wrapper.appendChild(a);

    return wrapper;
}