<?php

namespace Kunstmaan\NodeSearchBundle\Event;


class Events
{

    /**
     * The onIndexNode event will be triggered when a node is being indexed. It will contain the page being indexed and the doc to add additional content and fields to
     */
    const INDEX_NODE = "kunstmaan_node_search.onIndexNode";

}