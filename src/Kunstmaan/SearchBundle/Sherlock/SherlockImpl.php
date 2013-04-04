<?php

namespace Kunstmaan\SearchBundle\Sherlock;


use Sherlock\Sherlock;

class SherlockImpl {

    private $sherlock;

    public function __construct($hostname, $port)
    {
        $this->sherlock = new Sherlock;
        $this->sherlock->addNode($hostname, $port);
    }

    public function setupIndex()
    {
        $index = $this->sherlock->index('testindex');

        //Add two mappings, one a string and one a date
        $index->mappings(
            Sherlock::mappingBuilder('employee')->String()->field('firstname'),
            Sherlock::mappingBuilder('employee')->String()->field('lastname'),
            Sherlock::mappingBuilder('employee')->String()->field('function')
        );

        $response = $index->create();

        return $response->ok;
    }

    public function populateIndex()
    {
        $doc = array("firstname" => "Roderik", "lastname" => "van der Veer", "function" => "Technology Director");
        $doc = $this->sherlock->document()->index('testindex')->type('employee')->document($doc);
        $doc->execute();

        $doc = array("firstname" => "Kenny", "lastname" => "Debrauwer", "function" => "Web Developer");
        $doc = $this->sherlock->document()->index('testindex')->type('employee')->document($doc);
        $doc->execute();

        $doc = array("firstname" => "Kurt", "lastname" => "Limbos", "function" => "Art Director");
        $doc = $this->sherlock->document()->index('testindex')->type('employee')->document($doc);
        $doc->execute();
    }

    /**
     * @param string $querystring
     *
     * @return \Sherlock\responses\QueryResponse
     */
    public function searchIndex($querystring)
    {
        $request = $this->sherlock->search();

        $firstnameQuery = Sherlock::queryBuilder()->Match()->field("firstname")->query($querystring);
        $lastnameQuery = Sherlock::queryBuilder()->Match()->field("lastname")->query($querystring);
        $functionQuery = Sherlock::queryBuilder()->Match()->field("function")->query($querystring);

        $query = Sherlock::queryBuilder()->Bool()->should(array($firstnameQuery, $lastnameQuery, $functionQuery))->minimum_number_should_match(1);

        echo $query->toJSON()."\r\n";

        $request->index("testindex")
                ->type("employee")
                ->query($query);
        echo $request->toJSON()."\r\n";

        $response = $request->execute();

        echo "Took: ".$response->took."\r\n";
        echo "Number of Hits: ".count($response)."\r\n";

        foreach($response as $hit)
        {
            echo $hit['score'].' : '.$hit['source']['firstname'].' '.$hit['source']['lastname'].' - '.$hit['source']['function']."\r\n";
        }

        return $response;
    }

    public function deleteIndex()
    {
        $index = $this->sherlock->index('testindex');
        $response = $index->delete();

        return $response;
    }
}