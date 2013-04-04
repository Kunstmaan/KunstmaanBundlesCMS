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
        $doc = array("firstname" => "Roderik", "lastname" => "van der Veer", "company" => "Kunstmaan", "team" => "Smarties", "function" => "Technology Director", "tags" => array("een", "twee", "drie"));
        $doc = $this->sherlock->document()->index('testindex')->type('employee')->document($doc);
        $doc->execute();

        $doc = array("firstname" => "Kenny", "lastname" => "Debrauwer", "company" => "Kunstmaan", "team" => "Smarties", "function" => "Web Developer", "tags" => array("een", "drie"));
        $doc = $this->sherlock->document()->index('testindex')->type('employee')->document($doc);
        $doc->execute();

        $doc = array("firstname" => "Kurt", "lastname" => "Limbos", "company" => "Kunstmaan", "team" => "Studio", "function" => "Art Director", "tags" => array("twee"));
        $doc = $this->sherlock->document()->index('testindex')->type('employee')->document($doc);
        $doc->execute();
    }

    /**
     * @param string $querystring
     *
     * @return \Sherlock\responses\QueryResponse
     */
    public function searchIndex($querystring, $tag)
    {
        $request = $this->sherlock->search();

        $firstnameQuery = Sherlock::queryBuilder()->Match()->field("firstname")->query($querystring);
        $lastnameQuery = Sherlock::queryBuilder()->Match()->field("lastname")->query($querystring);
        $companyQuery = Sherlock::queryBuilder()->Match()->field("company")->query($querystring);
        $teamQuery = Sherlock::queryBuilder()->Match()->field("team")->query($querystring);
        $functionQuery = Sherlock::queryBuilder()->Match()->field("function")->query($querystring);

        $query = Sherlock::queryBuilder()->Bool()->should(array($firstnameQuery, $lastnameQuery, $companyQuery, $teamQuery, $functionQuery))->minimum_number_should_match(1);

        if($tag and $tag != ''){
            $tagQuery = Sherlock::queryBuilder()->Term()->field("tags")->term($tag);
            $query = Sherlock::queryBuilder()->Bool()->must(array($tagQuery, $query))->minimum_number_should_match(1);
        }

        echo $query->toJSON()."\r\n";

        $request->index("testindex")
                ->type("employee")
                ->query($query);
        echo $request->toJSON()."\r\n";

        $facet = Sherlock::facetBuilder()->Terms()->fields("tags")->facetname("tag");
        $request->facets($facet);

        $response = $request->execute();

        echo "Took: ".$response->took."\r\n";
        echo "Number of Hits: ".count($response)."\r\n";

        foreach($response as $hit)
        {
            echo $hit['score'].' : '.$hit['source']['firstname'].' '.$hit['source']['lastname'].' - '.$hit['source']['function'] . ' - ' . implode(' | ', $hit['source']['tags']) ."\r\n";
        }

        $responseData = $response->responseData;
        foreach($responseData['facets'] as $facet)
        {
            foreach($facet['terms'] as $term)
            {
                echo implode(' : ',$term) . "\r\n";
            }
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