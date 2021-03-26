let shows = document.getElementsByClassName('show-to-compare-picture');

Array.from(shows).forEach((show) => {
    show.addEventListener('click', vote);
});

function chooseShowsToCompare()
{
    let showsContainer = document.getElementById('container-for-shows-to-compare');

    hideRankingContainerChildren();

    showRankingSpinner();

    fetch('/api/ranking/find/shows/to/compare')
        .then((response) => {
            return response.json();
        })
        .then((shows) => {

            hideRankingContainerChildren();    

            shows.map((show) => {
                showsContainer.appendChild(createShowToCompareWidget(show));
            });
        
            let showsToComparePictures = document.getElementsByClassName('show-to-compare-picture');

            Array.from(showsToComparePictures).forEach((picture) => {
                picture.addEventListener('click', vote);
            });
        }); 
}

function vote(event)
{
    event.preventDefault();

    showRankingSpinner();

    let pictures = document.querySelectorAll('.show-to-compare-picture img');
    let winnerDatabaseTableName = event.target.getAttribute('data-tableName');

    let data = new FormData();

    data.append('database_table_name', winnerDatabaseTableName);

    fetch('/ranking/vote/up', {
        method: 'POST',
        body: data
    });

    Array.from(pictures).map((picture) => {        
        if (picture.dataset.tablename !== winnerDatabaseTableName)
        {
            voteDown(picture);
        }
    });

    chooseShowsToCompare();
}

function voteDown(looser) {
    let databaseTableName = looser.getAttribute('data-tableName');
 
    let data = new FormData();
    
    data.append('database_table_name', databaseTableName);

    fetch('/ranking/vote/down', {
        method: 'POST',
        body: data
      });
}

function showRankingSpinner()
{
    let showsContainer = document.getElementById('container-for-shows-to-compare');

    let spinner = createRankingSpinnerWidget();

    showsContainer.appendChild(spinner);
}

function createShowToCompareWidget(show)
{
    let wrapper = document.createElement('article');
    wrapper.setAttribute('class', 'col-xs-12 col-sm-6 col-md-6 p-2');

    let a = document.createElement('a');
    a.setAttribute('href', '#');
    a.setAttribute('class', 'show-to-compare-picture');

    let img = document.createElement('img');
    img.setAttribute('src', '/assets/pictures/shows/' + show.picture);
    img.setAttribute('data-tableName', show.databaseTableName);

    let titleDiv = document.createElement('div');
    titleDiv.setAttribute('class', 'show-to-compare-title-div');

    titleDiv.textContent = show.name;

    a.appendChild(img);

    wrapper.appendChild(a);
    wrapper.appendChild(titleDiv);

    return wrapper;
}

function createRankingSpinnerWidget()
{
    let center = document.createElement('center');
    center.setAttribute('class', 'mx-auto');
    center.style.marginTop = '50px';
    center.style.marginBottom = '50px';

    let spinner = document.createElement('div');
    spinner.setAttribute('class', 'spinner-border text-secondary');

    let span = document.createElement('span');
    span.setAttribute('class', 'sr-only');

    span.textContent = 'Loading...';

    spinner.appendChild(span);
    center.appendChild(spinner);

    return center;
}

function hideRankingContainerChildren() {
    let shows = document.getElementById('container-for-shows-to-compare');

    Array.from(shows.children).forEach((child) => {
        child.remove();
    });
}

chooseShowsToCompare();