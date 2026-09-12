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

        $adminListName = 'listconfig_' . $request->attributes->get('_route');

        $this->page = $request->query->getInt('page', 1);
        $this->limit = $request->query->getInt('limit', $this->getLimitOptions()[0]);

        $adminListSessionData = $request->getSession()->get($adminListName);
        if (!$query->has('limit') && null !== $adminListSessionData && isset($adminListSessionData['limit'])) {
            $this->limit = $adminListSessionData['limit'];
        }

        if ($request->query->has('limit') && !$request->query->has('page')) {
            $this->page = 1;
        }

        $this->orderBy = $this->sanitizeOrderBy($request->query->get('orderBy', ''));
        $this->orderDirection = $request->query->getAlpha('orderDirection');

        // there is a session and the filter param is not set
        if ($session->has($adminListName) && !$query->has('filter')) {
            if (!$query->has('page') && !$query->has('limit')) {
                $this->page = $adminListSessionData['page'];
            }

            if (!$query->has('orderBy')) {
                $this->orderBy = $this->sanitizeOrderBy($adminListSessionData['orderBy'] ?? '');
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
     * Only allow sorting on fields that were explicitly marked as sortable.
     * The value is used unescaped in the ORDER BY clause of the query, so any
     * value that is not a known sort field is discarded.
     *
     * Kept in the trait (instead of relying on AbstractAdminListConfigurator) so
     * the trait keeps working for configurators that only implement
     * AdminListConfiguratorInterface.
     */
    protected function sanitizeOrderBy($orderBy): string
    {
        if (!\is_string($orderBy) || $orderBy === '') {
            return '';
        }

        // Allow alphanumeric, _ & . in order by parameter!
        $orderBy = preg_replace('/[^a-zA-Z0-9_.]/', '', $orderBy);

        if (!\in_array($orderBy, $this->getSortFields(), true)) {
            return '';
        }

        return $orderBy;
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
