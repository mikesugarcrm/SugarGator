<?php

namespace Sugarcrm\Sugarcrm\custom\SugarGator\ACLs;

use BeanFactory;
use DBManagerFactory;
use Exception;
use SugarBean;
use Sugarcrm\Sugarcrm\Dbal\Connection;
use ACLAction;

class SugarGatorACL
{
    public string $module = 'sg_LogsAggregator';
    public array $disallowedActions = ['edit', 'delete', 'import', 'massupdate'];
    public Connection $conn;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->conn = DBManagerFactory::getInstance()->getConnection();
    }


    public function setSugarGatorACLs(): void
    {
        global $ACLActions;
        $GLOBALS['log']->fatal("Setting up SugarGator ACL's");
        ACLAction::addActions($this->module, 'module');
        foreach ($ACLActions['module']['actions'] as $action_name => $action_def) {
            $aclActionBean = $this->getACLActionBean($action_name);

            if (!is_a($aclActionBean, 'SugarBean')) {
                $GLOBALS['log']->fatal("Could not create SugarGator ACL for action '$action_name'.");
                continue;
            }

            $access = in_array($action_name, $this->disallowedActions) ? ACL_ALLOW_NONE : ACL_ALLOW_ADMIN;

            $aclActionBean->aclaccess = $access;
            $aclActionBean->save();
            $this->linkActionsToRoles($aclActionBean);
        }
        $GLOBALS['log']->fatal("Done with setting up SugarGator ACL's");
    }



    public function linkActionsToRoles(SugarBean $aclActionBean): void
    {
        $aclRole = BeanFactory::newBean('ACLRoles');
        $allRoles = $aclRole->getAllRoles();

        foreach ($allRoles as $roleBean) {
            $aclRole->setAction($roleBean->id, $aclActionBean->id, $aclActionBean->aclaccess);
        }
    }


    public function getACLActionBean(string $action_name): SugarBean|null
    {
        $aclID = $this->getACLActionID($action_name);
        if (empty($aclID)) {
            return null;
        }

        $aclActionBean = BeanFactory::retrieveBean('ACLActions', $aclID);

        if (is_null($aclActionBean) || !is_a($aclActionBean, 'SugarBean')) {
            $GLOBALS['log']->fatal("Could not retrieve ACLActions bean for ACL '$aclID' - skipping");
        }

        return $aclActionBean;
    }


    public function getACLActionID(string $action_name): string
    {
        $aclID = '';
        $sql = "select id from acl_actions where name = ? and category = ? and deleted = ?";
        $params = [$action_name, $this->module, 0];
        try {
            $aclID = $this->conn->fetchOne($sql, $params);
        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Could not find ACL for SugarGator action '$action_name'\nquery: $sql\nException: " . $e->getMessage());
        }
        return $aclID;
    }


    public function deleteSugarGatorACLs(): void
    {
        ACLAction::removeActions($this->module, 'module');
        $GLOBALS['log']->fatal("Done with tearing down SugarGator ACL's");
    }
}
