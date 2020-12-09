<?php

namespace Kunstmaan\AdminListBundle\Traits;

use Symfony\Component\HttpFoundation\Request;

/**
 * Trait ChangeableLimitTrait
 */
trait ChangeableLimitTrait
{
    private $limit;

    /**
     * Bind current request.
     */
    public function bindRequest(Request $request)
    {
        $query = $request->query;
        $session = $request->getSession();

        $adminListName = 'listconfig_' . $request->get('_route');

        $this->page = $request->query->getInt('page', 1);
        $this->limit = $request->query->getInt('limit', $this->getLimitOptions()[0]);

        $adminListSessionData = $request->getSession()->get($adminListName);
        if (!$query->has('limit') && null !== $adminListSessionData && isset($adminListSessionData['limit'])) {
            $this->limit = $adminListSessionData['limit'];
        }

        if ($request->query->has('limit') && !$request->query->has('page')) {
            $this->page = 1;
        }

        // Allow alphanumeric, _ & . in order by parameter!
        $this->orderBy = preg_replace('/[^[a-zA-Z0-9\_\.]]/', '', $request->query->get('orderBy', ''));
        $this->orderDirection = $request->query->getAlpha('orderDirection');

        // there is a session and the filter param is not set
        if ($session->has($adminListName) && !$query->has('filter')) {
            if (!$query->has('page') && !$query->has('limit')) {
                $this->page = $adminListSessionData['page'];
            }

            if (!$query->has('orderBy')) {
                $this->orderBy = $adminListSessionData['orderBy'];
            }

            if (!$query->has('orderDirection')) {
                $this->orderDirection = $adminListSessionData['orderDirection'];
            }
        }

        // save current parameters
        $session->set(
            $adminListName,
            [
                'page' => $this->page,
                'limit' => $this->limit,
                'orderBy' => $this->orderBy,
                'orderDirection' => $this->orderDirection,
            ]
        );

        // Remove limit from query param so it doesn't affect the session of the filter builder
        $request->query->remove('limit');
        $this->getFilterBuilder()->bindRequest($request);
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /** @return array */
    public function getLimitOptions()
    {
        return [
            10,
            20,
            50,
            100,
        ];
    }
}
