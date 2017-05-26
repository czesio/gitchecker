<?php

namespace AppBundle\Repository;

use Github\Api\Repository;
use Github\Client;

class RepositoryHandler
{

    const GIT_REPO_KEY = 'repo';
    const GIT_RELEASE_KEY = 'release';
    const GIT_COMMIT_KEY = 'commit';

    /**
     * repositories list - array(author, name) as a single row
     *
     * @var array
     */
    private $repositories = array();


    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $comparsionData = array();

    /**
     * RepositoryHandler constructor.
     *
     * @param array $repositoriesList
     * @param string $gitApiToken
     */
    public function __construct(array $repositoriesList,  $gitApiToken = null)
    {
        $this->prepareRepositories($repositoriesList);
        $this->createClient($gitApiToken);
    }

    /**
     * Gitapi has limit request per hour for not authenticated request
     * for increasing this limit must get api token
     *
     * @param string $gitApiToken
     */
    private function createClient($gitApiToken = null) {
        $this->client = new Client();
        if (null !== $gitApiToken) {
            $this->client->authenticate($gitApiToken, null, Client::AUTH_URL_TOKEN);
        }
    }

    /**
     * Prepare repository author, name list from given strings
     *
     * @param array $repositoriesList
     */
    private function prepareRepositories(array $repositoriesList)
    {
        $counter = 0;
        foreach ($repositoriesList AS $k => $v) {
            $repositoriesDetailedList = explode('/', $v);
            if (count($repositoriesDetailedList) > 1) {
                $this->repositories[$counter][] = $repositoriesDetailedList[count($repositoriesDetailedList) - 2];
                $this->repositories[$counter][] = $repositoriesDetailedList[count($repositoriesDetailedList) - 1];
                ++$counter;
            }
        }
    }

    /**
     * Prepare data from git api
     *
     * @return array
     */
    public function getComparisionData() {
        $gitData = $this->getDataFromGitapi();
        $this->prepareComparingData($gitData);
        return $this->comparsionData;
    }

    /**
     * @return array
     */
    private function getDataFromGitapi()
    {
        $gitRepoData = array();
        if (count($this->repositories) > 0) {
            foreach ($this->repositories AS $repo) {
                try {
                    // particular repo info
                    $gitRepoData[self::GIT_REPO_KEY][] = $this->client->api('repo')->show($repo[0], $repo[1]);
                } catch (\Exception $e ) {
                    $gitRepoData[self::GIT_REPO_KEY][] = array();
                }
                try {
                    //The latest() method fetches only releases which are not marked "prerelease" or "draft".
                    $gitRepoData[self::GIT_RELEASE_KEY][] = $this->client->api('repo')->releases()->latest($repo[0], $repo[1]);
                } catch (\Exception $e ) {
                    $gitRepoData[self::GIT_RELEASE_KEY][] = array();
                }
                try {
                    // last commit to master info
                    $gitRepoData[self::GIT_COMMIT_KEY][] = $this->client->api('repo')->commits()->show($repo[0], $repo[1], 'master');
                } catch (\Exception $e) {
                    $gitRepoData[self::GIT_COMMIT_KEY][] = array();
                }
            }
        }
        return $gitRepoData;
    }


    /**
     * Prepare comparing data for particuluar request to api, based on data directly from git api
     *
     * @param array $gitRepoData
     */
    private function prepareComparingData(array $gitRepoData)
    {
        foreach ($gitRepoData AS $k => $v) {
            switch ($k) {
                case self::GIT_REPO_KEY:
                    $this->prepareComparingDataForRepo($v);
                    break;
                case self::GIT_RELEASE_KEY:
                    $this->prepareComparingDataForRelease($v);
                    break;
                case self::GIT_COMMIT_KEY:
                    $this->prepareComparingDataForCommit($v);
                    break;
            }
        }
    }

    /**
     * Prepare data from git api for repository
     *
     * @param array $dataList
     */
    private function prepareComparingDataForRepo(array $dataList)
    {
        foreach ($dataList AS $k1 => $v1) {
            $this->comparsionData['full_name'][] = (array_key_exists('full_name', $v1) ? $v1['full_name'] : '');
            $this->comparsionData['html_url'][] = (array_key_exists('html_url', $v1) ? $v1['html_url'] : '');
            $this->comparsionData['description'][] = (array_key_exists('description', $v1) ? $v1['description'] : '');
            $this->comparsionData['stargazers_count'][] = (array_key_exists('stargazers_count', $v1) ? $v1['stargazers_count'] : '');
            $this->comparsionData['forks_count'][] = (array_key_exists('forks_count', $v1) ? $v1['forks_count'] : '');
            $this->comparsionData['subscribers_count'][] = (array_key_exists('subscribers_count', $v1) ? $v1['subscribers_count'] : '');
        }
    }

    /**
     * Prepare data from git api for last release
     *
     * @param array $dataList
     */
    private function prepareComparingDataForRelease(array $dataList)
    {
        foreach ($dataList AS $k1 => $v1) {
            $this->comparsionData['release_tag_name'][] = (array_key_exists('tag_name', $v1) ? $v1['tag_name'] : '');
            $this->comparsionData['release_published_at'][] = (array_key_exists('published_at', $v1) ? $v1['published_at'] : '');
        }
    }

    /**
     * Prepare data from git api for last commit
     *
     * @param array $dataList
     */
    private function prepareComparingDataForCommit(array $dataList)
    {
        //var_dump($dataList); die();
        foreach ($dataList AS $k1 => $v1) {
            if (array_key_exists('commit', $v1)) {
                $this->comparsionData['commit_date'][] = (array_key_exists('committer', $v1['commit']) ? $v1['commit']['committer']['date'] : '');
                $this->comparsionData['commit_message'][] = (array_key_exists('message', $v1['commit']) ? $v1['commit']['message'] : '');
            } else {
                break;
            }
        }
    }
}