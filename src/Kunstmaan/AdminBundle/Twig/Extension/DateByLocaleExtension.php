<?php

namespace Kunstmaan\AdminBundle\Twig\Extension;

use IntlDateFormatter;

use Symfony\Component\Locale\Locale;

/**
 * DateByLocaleExtension
 */
class DateByLocaleExtension extends \Twig_Extension
{
    /**
     * Get Twig filters defined in this extension.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'localeDate' => new \Twig_Filter_Function('\Kunstmaan\AdminBundle\Twig\Extension\DateByLocaleExtension::localeDateFilter')
        );
    }

    /**
     * Get the Twig extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'TwigLocaleExtension';
    }

    /**
     * A date formatting filter for Twig, renders the date using the specified parameters.
     *
     * @param mixed  $date     Unix timestamp to format
     * @param string $locale   The locale
     * @param string $dateType The date type
     * @param string $timeType The time type
     *
     * @return string
     */
    public static function localeDateFilter($date, $locale="nl", $dateType = 'medium', $timeType = 'none')
    {
        $values = array(
            'none' => IntlDateFormatter::NONE,
            'short' => IntlDateFormatter::SHORT,
            'medium' => IntlDateFormatter::MEDIUM,
            'long' => IntlDateFormatter::LONG,
            'full' => IntlDateFormatter::FULL,
         );

        $dateFormatter = IntlDateFormatter::create(
            $locale,
            $values[$dateType],
            $values[$timeType], 'Europe/Brussels');

        return $dateFormatter->format($date);
    }
}
