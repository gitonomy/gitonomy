<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Bundle\FrontendBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Gitonomy\Bundle\CoreBundle\Entity\User;

/**
 * Twig extension for date/time formatting.
 */
class DateTimeExtension extends \Twig_Extension
{
    /**
     * Associative array of formatters
     *
     * @var array An array mapping type to formatter
     */
    protected $formatters = array();

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inherited}
     */
    public function getFilters()
    {
        return array(
            'date_long'      => new \Twig_Filter_Method($this, 'getDateLong'),
            'date_short'     => new \Twig_Filter_Method($this, 'getDateShort'),
            'datetime_long'  => new \Twig_Filter_Method($this, 'getDateTimeLong'),
            'datetime_short' => new \Twig_Filter_Method($this, 'getDateTimeShort'),
            'time_short'     => new \Twig_Filter_Method($this, 'getTimeShort'),
            'dayname_short'  => new \Twig_Filter_Method($this, 'getDaynameShort'),
            'dayname_long'   => new \Twig_Filter_Method($this, 'getDaynameLong'),
            'date_relative'  => new \Twig_Filter_Method($this, 'getDateRelative'),
        );
    }

    public function getDateRelative(\DateTime $dateTime)
    {
        $now      = new \DateTime();
        $interval = $now->diff($dateTime);

        $translator = $this->container->get('translator');
        if ($interval->y) {
            return $translator->transChoice('{1}1 year ago|]1,+Inf]%n years ago', $interval->y, array('%n' => $interval->y));
        } elseif ($interval->m) {
            return $translator->transChoice('{1}1 month ago|]1,+Inf]%n months ago', $interval->m, array('%n' => $interval->m));
        } elseif ($interval->d) {
            return $translator->transChoice('{1}1 day ago|]1,+Inf]%n days ago', $interval->d, array('%n' => $interval->d));
        } elseif ($interval->h) {
            return $translator->transChoice('{1}1 hour ago|]1,+Inf]%n hours ago', $interval->h, array('%n' => $interval->h));
        } elseif ($interval->i) {
            return $translator->transChoice('{1}1 minute ago|]1,+Inf]%n minutes ago', $interval->i, array('%n' => $interval->i));
        } elseif ($interval->s) {
            return $translator->transChoice('{1}1 second ago|]1,+Inf]%n seconds ago', $interval->s, array('%n' => $interval->s));
        } else {
            return $translator->trans('now');
        }
    }

    /**
     * Formats date in long.
     *
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public function getDateLong(\DateTime $dateTime)
    {
        return $this
            ->getFormatter(\IntlDateFormatter::LONG)
            ->format($dateTime->getTimestamp())
        ;
    }

    /**
     * Formats date in short.
     *
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public function getDateShort(\DateTime $dateTime)
    {
        return $this
            ->getFormatter(\IntlDateFormatter::SHORT)
            ->format($dateTime->getTimestamp())
        ;
    }

    /**
     * Formats date and time in long.
     *
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public function getDateTimeLong(\DateTime $dateTime)
    {
        return $this
            ->getFormatter(\IntlDateFormatter::LONG, \IntlDateFormatter::MEDIUM)
            ->format($dateTime->getTimestamp())
        ;
    }

    /**
     * Formats date and time in short.
     *
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public function getDateTimeShort(\DateTime $dateTime)
    {
        return $this
            ->getFormatter(\IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT)
            ->format($dateTime->getTimestamp())
        ;
    }

    /**
     * Formats time in short.
     *
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public function getTimeShort(\DateTime $dateTime)
    {
        return $this
            ->getFormatter(\IntlDateFormatter::NONE, \IntlDateFormatter::SHORT)
            ->format($dateTime->getTimestamp())
        ;
    }

    /**
     * Returns the day name in short.
     *
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public function getDayNameShort(\DateTime $dateTime)
    {
        $formatter = $this->getFormatter(\IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
        $formatter->setPattern('EE');

        return $formatter->format($dateTime->getTimestamp());
    }

    /**
     * Returns the full day name.
     *
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public function getDayNameLong(\DateTime $dateTime)
    {
        $formatter = $this->getFormatter(\IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
        $formatter->setPattern('EEEE');

        return $formatter->format($dateTime->getTimestamp());
    }

    /**
     * {@inherited}
     */
    public function getName()
    {
        return 'localized_date';
    }

    /**
     * Get the formatter for a given format
     *
     * @param int $dateFormat The date format (IntlDateFormatter::* constants)
     * @param int $timeFormat The time format (IntlDateFormatter::* constants)
     *
     * @return IntlDateFormatter
     */
    protected function getFormatter($dateFormat, $timeFormat = \IntlDateFormatter::NONE)
    {
        $locale = $this->container->get('request')->getLocale();

        $user = $timezone = $this->container->get('security.context')->getToken()->getUser();
        if ($user instanceof User) {
            $timezone = $user->getTimezone();
        } else {
            $timezone = date_default_timezone_get();
        }

        $key = $dateFormat . '_' . $timeFormat.'_'.$locale.'_'.$timezone;
        if (false === isset($this->formatters[$key])) {
            $this->formatter[$key] = new \IntlDateFormatter($locale, $dateFormat, $timeFormat, $timezone);
        }

        return $this->formatter[$key];
    }
}
