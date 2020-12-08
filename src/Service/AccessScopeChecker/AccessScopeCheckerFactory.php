<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service\AccessScopeChecker;

use Safe\Exceptions\StringsException;
use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use WernerDweight\ApiAuthBundle\Exception\AccessScopeCheckerFactoryException;
use WernerDweight\ApiAuthBundle\Service\AccessScopeChecker\Checker\AccessScopeCheckerInterface;
use WernerDweight\RA\Exception\RAException;
use WernerDweight\RA\RA;

class AccessScopeCheckerFactory
{
    /** @var RA */
    private $scopeCheckers;

    /**
     * AccessScopeCheckerFactory constructor.
     *
     * @param RewindableGenerator<AccessScopeCheckerInterface> $scopeCheckers
     */
    public function __construct(RewindableGenerator $scopeCheckers)
    {
        $this->scopeCheckers = new RA();
        /** @var \Generator<AccessScopeCheckerInterface> $iterator */
        $iterator = $scopeCheckers->getIterator();
        while ($iterator->valid()) {
            /** @var AccessScopeCheckerInterface $scopeChecker */
            $scopeChecker = $iterator->current();
            $this->scopeCheckers->set(get_class($scopeChecker), $scopeChecker);
            $iterator->next();
        }
    }

    /**
     * @throws RAException
     * @throws StringsException
     */
    public function get(string $checkerClass): AccessScopeCheckerInterface
    {
        if (true !== $this->scopeCheckers->hasKey($checkerClass)) {
            throw new AccessScopeCheckerFactoryException(AccessScopeCheckerFactoryException::EXCEPTION_UNKNOWN_CHECKER, [$checkerClass ]);
        }
        /** @var AccessScopeCheckerInterface $scopeChecker */
        $scopeChecker = $this->scopeCheckers->get($checkerClass);
        return $scopeChecker;
    }
}
