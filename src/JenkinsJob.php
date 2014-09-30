<?php

namespace Cpliakas\PhpProjectStarter;

class JenkinsJob implements ConfigurableInterface, CreatableInterface
{
    use Configuration;

    /**
     * @var ProjectName
     */
    protected $projectName;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var boolean
     */
    protected $sslVerification;

    /**
     * @param ProjectName $projectName
     * @param Repository  $repository
     * @param string      $url
     */
    public function __construct(ProjectName $projectName, Repository $repository, $url)
    {
        $this->projectName = $projectName;
        $this->repository  = $repository;
        $this->url         = $url;
    }

    /**
     * @param boolean $verify
     *
     * @return JenkinsJob
     */
    public function sslVerification($verify = false)
    {
        $this->sslVerification = (bool) $verify;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $client = new Client($this->url);

        if (!$this->sslVerification) {
            $client->setSslVerification(false, false);
        }

        $configXml = file_get_contents(__DIR__ . '/../../../jenkins/config.xml');
        $job = str_replace('{{ project.name }}', $this->projectName->get(), $configXml);

        $headers = [
            'Content-Type' => 'text/xml'
        ];

        $options = [
            'query' => array('name' => $this->projectName->getName()),
        ];

        $client->post($this->url . '/createItem', $headers, $job, $options)->send();
        return true;
    }
}