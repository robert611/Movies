<?php 

namespace App\Service;

class ShowService 
{
    private array $episodes;

    public function __construct(array $episodes)
    {
        $this->episodes = $this->sortEpisodes($episodes);
    }

    public function getShowSeasonsNumbersWithSeasonFirstEpisodeId(): array
    {
        $seasons = array();

        foreach ($this->episodes as $episode)
        {
            if (!array_key_exists($episode['season'], $seasons))
            {
                // It always will be a first episode of given season
                $seasons[$episode['season']] = $episode['id'];
            }
        }

        return $seasons;
    }

    public function getSeasonEpisodes(int $season): array
    {
        $episodes = array();

        foreach ($this->episodes as $episode)
        {
            if ($episode['season'] == $season)
            {
                $episodes[] = $episode;
            }
        }

        return $episodes;
    }

    private function sortEpisodes($episodes)
    {
        usort($episodes, function ($a, $b) 
            {
                return $a['episode'] - $b['episode']; 
            }
        );

        return $episodes;
    }
}