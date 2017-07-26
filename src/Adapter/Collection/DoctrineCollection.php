<?php declare(strict_types=1);

namespace Novuso\Common\Adapter\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Novuso\Common\Domain\Collection\DomainCollectionInterface;

/**
 * DoctrineCollection is a Doctrine array collection adapter
 *
 * @copyright Copyright (c) 2017, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
class DoctrineCollection extends ArrayCollection implements DomainCollectionInterface
{
}
