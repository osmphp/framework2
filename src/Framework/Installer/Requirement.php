<?php

namespace Manadev\Framework\Installer;

use Illuminate\Console\OutputStyle;
use Manadev\Core\Exceptions\NotImplemented;
use Manadev\Core\Object_;

/**
 * @property string $yes @required
 * @property string $no @required
 * @property OutputStyle $output @temp
 */
class Requirement extends Object_
{
    public function default($property) {
        switch ($property) {
            case 'yes': return (string)m_("Yes");
            case 'no': return (string)m_("No");
        }
        return parent::default($property);
    }

    /**
     * @return bool
     */
    public function check() {
        throw new NotImplemented();
    }
}