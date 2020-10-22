<?php
namespace Application\Service;

use Application\Entity\AuthLog as LogEntity;

class AuthLog extends BaseService
{

    protected $entity = '\Application\Entity\AuthLog';

    public function saveLog($login)
    {
        $query = $this->em->createQuery("INSERT INTO ".$this->entity." s SET s.login = '".$login."', s.date = NOW(), s.ip = '".$_SERVER['REMOTE_ADDR']."';");
        $query->execute();
    }

    public function getCountForIp($ip)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('count(p.id)')
            ->from($this->entity, 'p')
            ->where('p.date =\''.date('Y-m-d').'\'')
            ->andWhere('p.ip = \''.$ip.'\'');
        return $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR);
    }

    public function addToBlackList($ip)
    {
        $bl = new \Application\Entity\BlackList();
        $bl->setIp($ip);
        $this->em->persist($bl);
        return $this->em->flush();
    }

    public function inBlackList($ip)
    {
        $query = $this->em->createQuery("SELECT r.id FROM \Application\Entity\BlackList r WHERE r.ip = '".$ip."'");
        $res = $query->getOneOrNullResult();
        return $res != null;
    }

    public function getLastLogin($login)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('a')
            ->from($this->entity, 'a')
            ->where('a.login = :login')
            ->andWhere('a.allow = 1')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(2)
            ->setParameter('login', $login);
        $res = $qb->getQuery()->getResult();
        if (isset($res[1])) {
            return $res[1];
        }
        return null;
    }

    public function log($login, $allow = 0)
    {
        if ($this->getCountForIp($_SERVER['REMOTE_ADDR']) > 5) {
            $this->addToBlackList($_SERVER['REMOTE_ADDR']);
        }
        $log = new LogEntity();
        $log->setDate(date('Y-m-d H:i:s'));
        $log->setIp($_SERVER['REMOTE_ADDR']);
        $log->setLogin($login);
        $log->setAllow($allow);
        $this->save($log);
    }
}
