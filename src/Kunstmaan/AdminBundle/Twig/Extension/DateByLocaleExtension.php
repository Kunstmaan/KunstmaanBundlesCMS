<?php

namespace Kunstmaan\AdminBundle\Twig\Extension;

use Symfony\Component\Locale\Locale;

class DateByLocaleExtension extends \Twig_Extension {
    
    public function getFilters() {
        return array(
            'localeDate' => new \Twig_Filter_Function('\Kunstmaan\AdminBundle\Twig\Extension\DateByLocaleExtension::localeDateFilter')
        );
    }
    
    public function getName() {
        return 'TwigLocaleExtension';
    }
    
    public static function localeDateFilter($date, $locale="nl", $dateType = 'medium', $timeType = 'none') {
        $values = array(
            'none' => \IntlDateFormatter::NONE,
            'short' => \IntlDateFormatter::SHORT,
            'medium' => \IntlDateFormatter::MEDIUM,
            'long' => \IntlDateFormatter::LONG,
            'full' => \IntlDateFormatter::FULL,
         );
        
        $dateFormater = \IntlDateFormatter::create(
            $locale,
            $values[$dateType],
            $values[$timeType],
         	'Europe/Brussels'
        );
        
        return $dateFormater->format($date);
    }
}

