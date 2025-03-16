<?php

namespace CmsHealthProject\EventListener;

use CmsHealth\Definition\CheckResultStatus;
use CmsHealthProject\Event\GatherHealthEvent;
use CmsHealthProject\SerializableReferenceImplementation\Check;
use CmsHealthProject\SerializableReferenceImplementation\CheckResult;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use LogicException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Throwable;

#[AsEventListener(GatherHealthEvent::NAME, 'getHealth')]
final class LastUserLogin
{
    private const CHECK_NAME    = 'Sulu:LastUserLoginTime';
    private const CHECK_COMPONENT_ID = '5a8e4d7f-6c2b-4a91-8d36-f7e9b50c4d3f';
    private const OBSERVED_UNIT = 'time';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    private function getLastUserLogin(): ?DateTimeInterface
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('u.lastLogin');
        $qb->from('se_users', 'u');
        $qb->orderBy('u.lastLogin', 'DESC');
        $qb->setMaxResults(1);

        $result = $qb->fetchFirstColumn();
        if (count($result) > 1) {
            throw new LogicException('More than one user selected');
        }
        [$lastLoginString] = $result;

        if (is_null($lastLoginString)) {
            return null;
        }

        return new DateTimeImmutable($lastLoginString);
    }

    private function getNow(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    private function getInfoThreshold(): ?DateTimeInterface
    {
        return $this->getNow()->modify('-1 week');
    }

    private function getWarnThreshold(): ?DateTimeInterface
    {
        return $this->getNow()->modify('-1 month');
    }

    private function getFailThreshold(): ?DateTimeInterface
    {
        return $this->getNow()->modify('-1 year');
    }

    public function getHealth(GatherHealthEvent $event): void
    {
        $names = $event->getNames();
        if ($names && !in_array(self::CHECK_NAME, $names, true)) {
            return;
        }

        $now       = new DateTime();
        $lastLogin = $this->getLastUserLogin();

        try {
            $checkStatus    = $this->determineStatus($lastLogin);
            $lastLoginValue = $lastLogin?->format('Y-m-d H:i:s') ?? 'never';

            $checkResult = new CheckResult(
                self::CHECK_COMPONENT_ID, 'system', $checkStatus, $now, $lastLoginValue, self::OBSERVED_UNIT, ''
            );
        } catch (Throwable $e) {
            $checkResult = new CheckResult(
                self::CHECK_COMPONENT_ID,
                'system',
                CheckResultStatus::Fail,
                $now,
                $lastLoginValue ?? null,
                self::OBSERVED_UNIT,
                $e->getMessage()
            );
        }

        $loginCheck = new Check(self::CHECK_NAME);
        $loginCheck->addCheckResults($checkResult);
        $event->getCheckCollection()->addCheck($loginCheck);
    }

    /**
     * Determines the check status based on the last login time.
     */
    private function determineStatus(?DateTimeInterface $lastLogin): CheckResultStatus
    {
        // Fail has the highest priority
        if ($lastLogin === null || $lastLogin < $this->getFailThreshold()) {
            return CheckResultStatus::Fail;
        }

        // Warn has the second-highest priority
        if ($lastLogin < $this->getWarnThreshold()) {
            return CheckResultStatus::Warn;
        }

        // Info has the third-highest priority
        if ($lastLogin < $this->getInfoThreshold()) {
            return CheckResultStatus::Info;
        }

        // Default status is Pass
        return CheckResultStatus::Pass;
    }
}
