let recentWatchedShowsCards = document.getElementsByClassName('recently-watched-show-card');
let arrowRight = document.getElementById('next-recently-watched-show');
let arrowLeft = document.getElementById('previous-recently-watched-show');
let userWatchingHistoryContainer = document.getElementById('user-watching-history-container');

function displayNext(visits)
{
    let nextShowDescendingId = parseInt(getLastDisplayedShowId()) + 1;

    let visitsIndexes = visits.length - 1;

    if (nextShowDescendingId > visitsIndexes) return;

    let nextVisit = visits[nextShowDescendingId];
    getFirstDisplayedShowDiv().remove();

    let divWithNextShow = createNewShowDiv(nextVisit, nextShowDescendingId);
    userWatchingHistoryContainer.insertAdjacentHTML('beforeend', divWithNextShow);

    recentWatchedShowsCards = document.getElementsByClassName('recently-watched-show-card');

    if (nextShowDescendingId == visitsIndexes) arrowRight.classList.add('disabled-color');
    arrowLeft.classList.remove('disabled-color');
}

function displayPrevious(visits)
{
    let previousShowDescendingId = parseInt(getFirstDisplayedShowId()) - 1;

    if (previousShowDescendingId < 0) return;

    let previousVisit = visits[previousShowDescendingId];
    getLastDisplayedShowDiv().remove();

    let divWithPreviousShow = createNewShowDiv(previousVisit, previousShowDescendingId);
    userWatchingHistoryContainer.insertAdjacentHTML('afterbegin', divWithPreviousShow);

    recentWatchedShowsCards = document.getElementsByClassName('recently-watched-show-card');

    if (previousShowDescendingId == 0) arrowLeft.classList.add('disabled-color');
    arrowRight.classList.remove('disabled-color');
}

function getFirstDisplayedShowDiv()
{
    return recentWatchedShowsCards[0];
}

function getLastDisplayedShowDiv()
{
    return recentWatchedShowsCards[recentWatchedShowsCards.length - 1];
}

function getFirstDisplayedShowId()
{
    return recentWatchedShowsCards[0].getAttribute('data-descendingId');
}

function getLastDisplayedShowId()
{
    return recentWatchedShowsCards[recentWatchedShowsCards.length - 1].getAttribute('data-descendingId');
}

function hideWatchingHistorySpinner()
{
    document.getElementById('watching-history-spinner').classList.add('hide');
}

function displayWatchingHistoryArrows()
{
    document.getElementById('watching-history-arrows').classList.remove('hide');
}

function createNewShowDiv(visit, previousShowDescendingId)
{
    return `<div class="col-xs-12 col-sm-6 col-md-3 recently-watched-show-card" data-descendingId="${previousShowDescendingId}">
                <div class="card mb-3">
                    <img class="recently-watched-show-picture" src="/assets/pictures/shows/${visit.series.picture}" alt="${visit.series.name}" show picture">
                    <div class="card-body">
                        <a class="text-decoration-none" href="/show/${visit.series.database_table_name}/${visit.episode_id}">
                            <h5 class="card-title">
                                ${visit.series.name}
                            </h5>
                        </a>
                    </div>
                </div>
            </div>`;
}

fetch('/api/user/watching/history/fetch')
    .then(response => response.json())
    .then(visits => {
        if (visits.length < 5) arrowRight.classList.add('disabled-color');
        arrowRight.addEventListener('click', () => displayNext(visits));
        arrowLeft.addEventListener('click', () => displayPrevious(visits));

        hideWatchingHistorySpinner();
        displayWatchingHistoryArrows();
    });

