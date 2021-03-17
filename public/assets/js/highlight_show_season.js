function highlightShowSeason()
{
    let seasonNumber = document.getElementById('current-season').value;
    let seasonButton = document.getElementById('season' + seasonNumber);

    seasonButton.classList.remove('btn-outline-secondary');
    seasonButton.classList.add('btn-outline-primary');
}

highlightShowSeason();