<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Acl\Permission;

use InvalidArgumentException;

/**
 * This class allows you to build cumulative permissions easily, or convert
 * masks to a human-readable format.
 *
 * @see Symfony\Component\Security\Acl\Permission\MaskBuilder
 */
class MaskBuilder
{
    const MASK_VIEW         = 1;          // 1 << 0
    const MASK_EDIT         = 4;          // 1 << 2
    const MASK_DELETE       = 8;          // 1 << 3
    const MASK_PUBLISH      = 16;         // 1 << 4
    const MASK_UNPUBLISH    = 32;         // 1 << 5
    const MASK_IDDQD        = 1073741823; // 1 << 0 | 1 << 1 | ... | 1 << 30

    const CODE_VIEW         = 'V';
    const CODE_EDIT         = 'E';
    const CODE_DELETE       = 'D';
    const CODE_PUBLISH      = 'P';
    const CODE_UNPUBLISH    = 'U';

    const ALL_OFF           = '................................';
    const OFF               = '.';
    const ON                = '*';

    private $mask;

    /**
     * Constructor
     *
     * @param int $mask optional; defaults to 0
     *
     * @throws InvalidArgumentException
     */
    public function __construct($mask = 0)
    {
        if (!is_int($mask)) {
            throw new InvalidArgumentException('$mask must be an integer.');
        }

        $this->mask = $mask;
    }

    /**
     * Adds a mask to the permission
     *
     * @param mixed $mask
     *
     * @throws InvalidArgumentException
     *
     * @return MaskBuilder
     */
    public function add($mask)
    {
        if (is_string($mask) && defined($name = 'static::MASK_'.strtoupper($mask))) {
            $mask = constant($name);
        } elseif (!is_int($mask)) {
            throw new InvalidArgumentException('$mask must be an integer.');
        }

        $this->mask |= $mask;

        return $this;
    }

    /**
     * Returns the mask of this permission
     *
     * @return int
     */
    public function get()
    {
        return $this->mask;
    }

    /**
     * Returns a human-readable representation of the permission
     *
     * @return string
     */
    public function getPattern()
    {
        $pattern = self::ALL_OFF;
        $length = strlen($pattern);
        $bitmask = str_pad(decbin($this->mask), $length, '0', STR_PAD_LEFT);

        for ($i=$length-1; $i>=0; $i--) {
            if ('1' === $bitmask[$i]) {
                try {
                    $pattern[$i] = self::getCode(1 << ($length - $i - 1));
                } catch (\Exception $notPredefined) {
                    $pattern[$i] = self::ON;
                }
            }
        }

        return $pattern;
    }

    /**
     * Removes a mask from the permission
     *
     * @param mixed $mask
     *
     * @throws InvalidArgumentException
     *
     * @return MaskBuilder
     */
    public function remove($mask)
    {
        if (is_string($mask) && defined($name = 'static::MASK_'.strtoupper($mask))) {
            $mask = constant($name);
        } elseif (!is_int($mask)) {
            throw new InvalidArgumentException('$mask must be an integer.');
        }

        $this->mask &= ~$mask;

        return $this;
    }

    /**
     * Resets the MaskBuilder
     *
     * @return MaskBuilder
     */
    public function reset()
    {
        $this->mask = 0;

        return $this;
    }

    /**
     * Returns the code for the passed mask
     *
     * @param null|int $mask
     *
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return string
     */
    public static function getCode($mask)
    {
        if (!is_int($mask)) {
            throw new InvalidArgumentException('$mask must be an integer.');
        }

        $reflection = new \ReflectionClass(get_called_class());
        foreach ($reflection->getConstants() as $name => $cMask) {
            if (0 !== strpos($name, 'MASK_')) {
                continue;
            }

            if ($mask === $cMask) {
                if (!defined($cName = 'static::CODE_'.substr($name, 5))) {
                    throw new \RuntimeException('There was no code defined for this mask.');
                }

                return constant($cName);
            }
        }

        throw new InvalidArgumentException(sprintf('The mask "%d" is not supported.', $mask));
    }

    /**
     * Checks if a specific permission or mask value is set in the current mask
     *
     * @param string|int $mask
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function has($mask)
    {
        if (is_string($mask) && defined($name = 'static::MASK_'.strtoupper($mask))) {
            $mask = constant($name);
        } elseif (!is_int($mask)) {
            throw new InvalidArgumentException('$mask must be an integer.');
        }

        return ($this->mask & $mask) != 0;
    }
}
