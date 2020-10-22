<?php
namespace Application\Service;

final class AccessService
{
    /**
     * @var \Zend\Permissions\Acl\Acl
     */
    protected $acl;

    /**
     * @var string
     */
    protected $roles;

    /**
     * AccessService constructor.
     * @param \Zend\Permissions\Acl\Acl $acl
     * @param  \Zend\Authentication\AuthenticationService $authenticationService
     */
    public function __construct($acl, $authenticationService)
    {
        $identity = $authenticationService->getIdentity();
        $this->roles = $identity ? $identity->getRoleNames() : ['guest'];
        $this->acl = $acl;
    }

    public function isAllowed($controller, $action)
    {
        foreach ($this->roles as $role) {
            if ($this->acl->isAllowed($role, $controller, $action)) {
                return true;
            }
        }
        return false;
    }
}
