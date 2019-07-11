<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service\AccessScopeChecker;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use WernerDweight\ApiAuthBundle\Exception\AccessScopeCheckerFactoryException;
use WernerDweight\ApiAuthBundle\Service\AccessScopeChecker\Checker\AccessScopeCheckerInterface;
use WernerDweight\RA\RA;

class AccessScopeCheckerFactory
{
    /** @var RA */
    private $scopeCheckers;

    /**
     * AccessScopeCheckerFactory constructor.
     * @param RewindableGenerator $scopeCheckers
     */
    public function __construct(RewindableGenerator $scopeCheckers)
    {
        $this->scopeCheckers = new RA();
        /** @var \Generator $iterator */
        $iterator = $scopeCheckers->getIterator();
        while ($iterator->valid()) {
            /** @var AccessScopeCheckerInterface $scopeChecker */
            $scopeChecker = $iterator->current();
            $this->scopeCheckers->set(get_class($scopeChecker), $scopeChecker);
            $iterator->next();
        }
    }

    /**
     * @param string $checkerClass
     *
     * @return AccessScopeCheckerInterface
     *
     * @throws \WernerDweight\RA\Exception\RAException
     */
    public function get(string $checkerClass): AccessScopeCheckerInterface
    {
        if (true !== $this->scopeCheckers->hasKey($checkerClass)) {
            throw new AccessScopeCheckerFactoryException(
                AccessScopeCheckerFactoryException::EXCEPTION_UNKNOWN_CHECKER,
                [$checkerClass]
            );
        }
        /** @var AccessScopeCheckerInterface $scopeChecker */
        $scopeChecker = $this->scopeCheckers->get($checkerClass);
        return $scopeChecker;
    }
}
