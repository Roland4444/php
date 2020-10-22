<?php

namespace Reference\Service;

use Application\Service\BaseService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Interop\Container\ContainerInterface;
use \Reference\Entity\User;
use Zend\Http\Request;

/**
 * Class UserService
 *
 * @package Reference\Service
 */
class UserService extends BaseService
{
    /**
     * UserService constructor.
     */
    public function __construct()
    {
        $this->setEntity(User::class);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $service = parent::__invoke($container, $requestedName, $options);
        $service->setConfig($container->get('config'));
        return $service;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('u, r, d')->from($this->getEntity(), 'u')
            ->join('u.roles', 'r')->leftJoin('u.department', 'd')
            ->where('u.id = ' . $id);
        return $qb->getQuery()->getSingleResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('u, r, d')->from($this->getEntity(), 'u')
            ->join('u.roles', 'r')->leftJoin('u.department', 'd')
            ->addOrderBy('u.name', 'ASC')
            ->addOrderBy('u.login', 'ASC');
        return $qb->getQuery()->getResult();
    }

    /**
     * Find user by login
     *
     * @param $login
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByLogin($login)
    {
        return $this->em->getRepository($this->entity)->findOneBy(['login' => $login]);
    }

    /**
     * Find default entity
     *
     * @param $token
     * @return mixed
     */
    public function findByToken($token)
    {
        return $this->em->getRepository($this->entity)->findOneBy(['token' => $token]);
    }

    /**
     * Сохраняем пользователя
     *
     * @param \Reference\Entity\User $row
     * @param  null|Request          $request
     *
     * @return mixed|void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save($row, $request = null)
    {
        if (! empty($request) && ! empty($request->getPost('password'))) {
            $passwordData =
                PasswordService::getPasswordData($row->getPassword());
            $row->setPass($passwordData['pass']);
            $row->setPassword($passwordData['password']);
        }

        parent::save($row);
    }

    /**
     * Block user
     *
     * @param $login
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function blockUser($login)
    {
        $user = $this->findByLogin($login);
        if (! empty($user)) {
            $user->setAttempts($user->getAttempts() + 1);
            if ($user->getAttempts() == 3) {
                $user->setIsBlocked(1);
            }
            $this->save($user);
        }
    }

    /**
     * Reset attempts
     *
     * @param $login
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function resetAttempts($login)
    {
        $user = $this->findByLogin($login);
        if (! empty($user)) {
            $user->setAttempts(0);
            $this->save($user);
        }
    }

    /**
     * Сохраняет токен для авторизации юзера в апи
     *
     * @param $login
     *
     * @return array
     * @throws ORMException
     * @throws OptimisticLockException*@throws \Exception
     */
    public function saveToken($login): array
    {
        $token = md5(uniqid($login, true));
        $user = $this->findByLogin($login);
        $user->setToken($token);
        $expiredDate = (new \DateTime())->modify('+1 day');
        $expired = $expiredDate->format('Y-m-d H:i:s');
        $user->setTokenExpired($expired);
        $this->save($user);
        return [
            'token' => $token,
            'expired' => $expired
        ];
    }

    /**
     * @param array|null $ids
     * @return array|null
     */
    public function findByIds($ids)
    {
        if (empty($ids)) {
            return null;
        }

        $ids = implode(',', $ids);
        $qb = $this->em->createQueryBuilder();
        $qb->select('s')
            ->from($this->entity, 's')
            ->where("s.id IN ($ids)");

        return $qb->getQuery()->getResult();
    }
}
