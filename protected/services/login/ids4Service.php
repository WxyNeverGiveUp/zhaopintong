<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Original Author <jfxu@wisedu.com>                        |
// |          Your Name <jfxu@wisedu.com>                                 |
// +----------------------------------------------------------------------+


define('IDS_SSO_VERSION','4');

define('IDS4_SERVICE_VERSION','1.0.0');


if (version_compare(PHP_VERSION, '5.0.0', '<')) {
    trigger_error('only support for  php 5+ ', E_USER_ERROR);
}
if (!class_exists("SoapClient")) {
    trigger_error("soap extends is not open", E_USER_ERROR);
}
class Attribute {
    public $name; // string
    public $values; // string
    
}
class Group {
  public $id; // string
  public $name; // string
}
class Identity {
    public $fingure; // string
    public $orgName; // string
    
}

class SSOToken {
  public $tokenValue; // string
  public $userId; // string
}
class Ids4Service extends SoapClient {
    private $client;
    private static $classmap = array(
        'Attribute' => 'Attribute',
        'Group' => 'Group',
        'Identity' => 'Identity',
        'SSOToken' => 'SSOToken',
    );
    protected function remoteCall($method, $args) {
        $result = $this->client->__soapCall($method, array(
            $args
        ) , array(
            'uri' => 'http://restApi.ids4.ids.wisedu.com/',
            'soapaction' => ''
        )); 
		if(isset($result->return)){
			$result=$result->return;
		} 
		if( is_object($result) && !in_array(get_class($result),self::$classmap )){
			$result=get_object_vars($result);
			if(count($result) ==1){ 
				$result= current($result);
			} 
		}
        return $result;
    }
	
	
	/**
     * 取用户的属性
     *
     * @param urls
     *            service wsdl地址，可以为数组（多个）或者为string
     * @param userName
     *            client.properties里的ids.UserName base64解码后的值
	 * @param passwd
     *            client.properties里的ids.Password base64解码后的值
     */
    public function __construct($urls, $userName, $passwd, $options = array()) {
        foreach (self::$classmap as $key => $value) {
            if (!isset($options['classmap'][$key])) {
                $options['classmap'][$key] = $value;
            }
        }
		$url = ""; 
		if(is_string($urls)){
			$url = $urls;
		}
		else if(is_array($urls)){
			$url = $urls[ array_rand($urls,1)];
		}
		else{
			trigger_error("Ids4Service: urls should be array or string ", E_USER_ERROR);
		
		} 
        $this->client = new SoapClient($url, $options);
        $auth = array(
            'spId' => $userName,
            'spPassword' => $passwd
        );
        $authvalues = new SoapVar($auth, SOAP_ENC_OBJECT);
        $header = new SoapHeader("http://restApi.ids4.ids.wisedu.com/", "AuthHeader", $authvalues);
        $this->client->__setSoapHeaders($header);
    }
	 /**
     * 测试方法
     *
     * @param test
     *            测试字符串
     * @return 测试字符串
     */
    public function test($test) {
        $param = array(
            'test' => $test
        );
        $result = $this->remoteCall("test", $param);
        return $result;
    }
    /**
     * 根据用户登录名取得用户姓名
     *
     * @param uid
     *            用户登录名
     * @return 用户姓名, 如果不存在返回 null
     */
    public function getUserNameByID($uid) {
        $param = array(
            'uid' => $uid
        );
        $result = $this->remoteCall("getUserNameByID", $param);
        return $result;
    }
    /**
     * 判断用户是否存在
     *
     * @param uid
     *            用户ID
     * @return true 如果存在 false 如果不存在
     */
    public function isUserExist($uid) {
        $param = array(
            'uid' => $uid
        );
        $result = $this->remoteCall("isUserExist", $param);
        return $result;
    }
    /**
     * 取用户的属性
     *
     * @param uid
     *            用户ID
     * @param attrName
     *            属性名
     * @return 如果属性不存在，返回 长度为0的数组
     */
    public function getUserAttribute($uid, $attrName) {
        $param = array(
            'uid' => $uid,
            'attrName' => $attrName
        );
        $result = $this->remoteCall("getUserAttribute", $param);
        return $result;
    }
    /**
     * 取用户所属的组(静态组)
     *
     * @param uid
     * @return 包含类型为Group的List, 如果0个组，返回空的List
     */
    public function getUserGroup($uid) {
        $param = array(
            'uid' => $uid
        );
        $result = $this->remoteCall("getUserGroup", $param);
        return $result;
    }
    /**
     * 验证用户密码是否正确
     *
     * @param uid
     *            用户ID
     * @param password
     *            用户密码
     * @return 如果正确返回 true, 否则返回 false
     */
    public function checkPassword($uid, $password) {
        $param = array(
            'uid' => $uid,
            'password' => $password
        );
        $result = $this->remoteCall("checkPassword", $param);
        return $result;
    }
    // ////////////////// 组相关方法 ///////////////////
    
    /**
     * 返回系统中所有的组 返回的List的中对象类型是 Group
     *
     * @deprecated 使用 getOrgAllGroups($org)
     */
    public function getGroups() {
        $param = array();
        $result = $this->remoteCall("getRootGroup", $param);
        return $result;
    }
    /**
     * 返回根组
     *
     * @return 如果不存在，返回 null
     * @deprecated 使用 getOrgGroup($orgnizaiton)
     */
    public function getRootGroup() {
        $param = array();
        $result = $this->remoteCall("getRootGroup", $param);
        return $result;
    }
    /**
     * 取组下的子组
     *
     * @param groupID
     *            父组groupID
     * @return 如果组下没有子组，返回长度为0的List List 中对象的类型是 Group
     */
    public function getSubGroups($groupID) {
        $param = array(
            'groupID' => $groupID
        );
        $result = $this->remoteCall("getRootGroup", $param);
        return $result;
    }
    /**
     * 根据组名取得组
     *
     * @param groupName
     * @return 存放Group类型的对象
     */
    public function getGroupByName($groupName) {
        $param = array(
            'groupName' => $groupName
        );
        $result = $this->remoteCall("getGroupByName", $param);
        return $result;
    }
    /**
     * 根据组ID取组
     *
     * @param uid
     * @return 如果组不存在，返回 null
     */
    public function getGroupByID($groupId) {
        $param = array(
            'groupId' => $groupId
        );
        $result = $this->remoteCall("getGroupByID", $param);
        return $result;
    }
    /**
     * 返回一个组中所有的用户
     *
     * @param GroupID
     * @return 用户ID的List, 对象类型为String
     */
    public function getUserByGroup($GroupID) {
        $param = array(
            'GroupID' => $GroupID
        );
        $result = $this->remoteCall("getUserByGroup", $param);
        return $result;
    }
    /**
     * 返回一个组中所有的用户姓名 如果有重名的用户，保留
     *
     * @param group
     * @return 用户全名的List, 对象类型为String
     */
    public function getUserNameByGroup($groupId) {
        $param = array(
            'groupId' => $groupId
        );
        $result = $this->remoteCall("getUserNameByGroup", $param);
        return $result;
    }
    /**
     * 取数据库中任意一个Entry的属性
     *
     * @param dn
     * @param attrName
     * @return 属性值
     */
    public function getEntryAttribute($dn, $attrName) {
        $param = array(
            'dn' => $dn,
            'attrName' => $attrName
        );
        $result = $this->remoteCall("getEntryAttribute", $param);
        return $result;
    }
    /**
     * 取一个组织下的第一级组
     *
     * @param orgnizaiton
     *            组织的DN
     * @return 返回 Object类型为Group的List,如果组织下没有设置组，返回长度为0的List
     */
    public function getOrgFirstLevelGroup($orgnizaiton) {
        $param = array(
            'orgnizaiton' => $orgnizaiton
        );
        $result = $this->remoteCall("getOrgFirstLevelGroup", $param);
        return $result;
    }
    /**
     * 取一个组织下的所有组
     *
     * @param orgnizaiton
     *            组织的DN
     * @return 返回 Object类型为Group的List,如果组织下没有设置组，返回长度为0的List
     */
    public function getOrgAllGroups($orgnizaiton) {
        $param = array(
            'orgnizaiton' => $orgnizaiton
        );
        $result = $this->remoteCall("getOrgAllGroups", $param);
        return $result;
    }
    // //////////// 会话处理部分 ////////////////////////
    
    /**
     * 创建一次会话的令牌(Token)，该令牌可以在不同的应用间传递
     *
     * @param userId
     *            用户登录名
     * @param password
     *            用户密码
     * @return 如果失败，返回 NULL
     */
    public function createStoken($userId, $password) {
        $param = array(
            'userId' => $userId,
            'password' => $password
        );
        $result = $this->remoteCall("createStoken", $param);
        return $result;
    }
    /**
     * 校验 Token是否有效
     *
     * @param tokenValue
     * @return 如果成功返回 SSOToken
     */
    public function validateToken($tokenValue) {
        $param = array(
            'tokenValue' => $tokenValue
        );
        $result = $this->remoteCall("validateToken", $param);
        return $result;
    }
    /**
     * 注销令牌
     *
     * @param tokenValue
     */
    public function destroyToken($tokenValue) {
        $param = array(
            'tokenValue' => $tokenValue
        );
        $result = $this->remoteCall("destroyToken", $param);
        return $result;
    }
    /**
     * 获取当前登录用户ID
     *
     * @param cookieValue
     *            IDS在用户浏览器中设置的cookie
     *
     */
    public function getCurrentUser($cookieValue) {
        $param = array(
            'cookieValue' => $cookieValue
        );
        $result = $this->remoteCall("getCurrentUser", $param);
        return $result;
    }
    /**
     * 取用户的第一身份
     *
     * @param uid
     *            用户的登录ID
     * @return 用户的第一身份
     */
    public function getUserFirstIdentity($uid) {
        $param = array(
            'uid' => $uid
        );
        $result = $this->remoteCall("getUserFirstIdentity", $param);
        return $result;
    }
    /**
     * 取用户的所有身份，不包括第一身份在内
     *
     * @param uid
     * @return object类型为Identity的List
     */
    public function getUserIdentites($uid) {
        $param = array(
            'uid' => $uid
        );
        $result = $this->remoteCall("getUserIdentites", $param);
        return $result;
    }
    // ////////////// End of 组织 ////////////////////////
    
    /**
     * 取认证服务器的登录URL,当 Web 应用判断没有用户登录时,可以转向该地址
     *
     * @return
     */
    public function getLoginURL() {
        $param = array();
        $result = $this->remoteCall("getLoginURL", $param);
        return $result;
    }
    /**
     * 取认证服务器的注销URL,当 Web 应用需要注销用户时,重定向到该地址
     *
     * @return
     */
    public function getLogoutURL() {
        $param = array();
        $result = $this->remoteCall("getLoginURL", $param);
        return $result;
    }
    // / 修改用户属性操作
    
    /**
     * 添加用户属性，首先判断用户是否存在
     *
     * @param uid
     * @param attr
     * @return
     */
    public function addUserAttribute($uid, Attribute $attr) {
        $param = array(
            'uid' => $uid,
            'attr' => $attr
        );
        $result = $this->remoteCall("addUserAttribute", $param);
        return $result;
    }
    /**
     * 修改用户属性，首先判断用户是否存在,如果要修改用户的密码,请先checkPassword然后用该函数可以修改用户的密码
     *
     * @param uid
     * @param uid
     * @param uid
     * @param uid
     * @param attrName
     * @param oldValue
     * @param newValue
     * @return
     */
    public function updateUserAttribute($uid, $attrName, $oldValue, $newValue) {
        $param = array(
            'uid' => $uid,
            'attrName' => $attrName,
            'oldValue' => $oldValue,
            'newValue' => $newValue
        );
        $result = $this->remoteCall("updateUserAttribute", $param);
        return $result;
    }
    /**
     * 删除用户属性
     *
     * @param uid
     * @param attr
     * @return
     */
    public function deleteUserAttribute($uid, Attribute $attr) {
        $param = array(
            'uid' => $uid,
            'attr' => $attr
        );
        $result = $this->remoteCall("deleteUserAttribute", $param);
        return $result;
    }
    // / 过滤组
    
    /**
     * 检查人员是否在某个组中。 这里的组可以是静态组、也可以是过滤组，如果组是过滤组，则必须用这个方法来判断用户
     *
     */
    public function isUserInGroup($uid, $groupId) {
        $param = array(
            'uid' => $uid,
            'groupId' => $groupId
        );
        $result = $this->remoteCall("isUserInGroup", $param);
        return $result;
    }
    /**
     * 取一组用户属性,不能用这个函数取用户的DN，如果要取用户的 DN用getUserAttribute(uid,attrName)函数。
     *
     * @param uid
     * @param attrName
     * @return key=AttrName, value=Attribute类型的Map
     */
    public function getUserAttributes($uid, $attrNames) {
        $param = array(
            'uid' => $uid,
            'attrNames' => $attrNames
        );
        $result = $this->remoteCall("getUserAttributes", $param);
        return $result;
    }
    /**
     * 根据组织的DN取得组织的属性
     *
     * @param orgDN
     *            组织DN
     * @param attrName
     *            属性名
     * @return 属性值的String数组
     */
    public function getOrgAttribute($orgDN, $attrName) {
        $param = array(
            'orgDN' => $orgDN,
            'attrName' => $attrName
        );
        $result = $this->remoteCall("getOrgAttribute", $param);
        return $result;
    }
    /**
     * 根据用户的UID，以及提供的组DN，把用户添加到组中，使用前应该先判断用户是否存在
     *
     * @param userID
     *            用户ID
     * @param groupId
     * @return
     */
    public function addUserToGroup($userID, $groupId) {
        $param = array(
            'userID' => $userID,
            'groupId' => $groupId
        );
        $result = $this->remoteCall("addUserToGroup", $param);
        return $result;
    }
    /**
     * 根据用户的UID数组，以及提供的组DN，把数组中的所有用户添加到组中，使用前保证所有的用户存在
     * 并且也要保证所有的用户和需要添加的组在同一个根组织下
     *
     * @param userIDs
     *            用户的ID数组
     * @param groupId
     * @return
     */
    public function addUsersToGroup($userIDs, $groupId) {
        $param = array(
            'userIDs' => $userIDs,
            'groupId' => $groupId
        );
        $result = $this->remoteCall("addUsersToGroup", $param);
        return $result;
    }
    /**
     * 根据用户的UID，以及提供的组DN，把用户从组中删除，使用前应该先判断用户是否存在
     *
     * @param userID
     *            用户ID
     * @param groupId
     * @return
     */
    public function deleteUserToGroup($userID, $groupId) {
        $param = array(
            'userID' => $userID,
            'groupId' => $groupId
        );
        $result = $this->remoteCall("deleteUserToGroup", $param);
        return $result;
    }
    /**
     * 根据用户的UID数组，以及提供的组DN，把数组中的所有用户从组中删除，使用前保证所有的用户存在
     * 并且也要保证所有的用户和需要添加的组在同一个根组织下
     *
     * @param userIDs
     *            用户ID数组
     * @param groupId
     * @return
     */
    public function deleteUsersToGroup($userIDs, $groupId) {
        $param = array(
            'userIDs' => $userIDs,
            'groupId' => $groupId
        );
        $result = $this->remoteCall("deleteUsersToGroup", $param);
        return $result;
    }
    /**
     * 在指定的组容器中，添加一个静态组
     *
     * @param groupName
     * @param groupContainer
     *            组容器的DN 如ou=Groups,o=sun.com
     * @return
     */
    public function addGroupToContainer($groupName, $groupContainerDn) {
        $param = array(
            'groupName' => $groupName,
            'groupContainerDn' => $groupContainerDn
        );
        $result = $this->remoteCall("addGroupToContainer", $param);
        return $result;
    }
    /**
     * 在指定的组容器中，删除一个组
     *
     * @param groupId
     * @param groupContainer
     * @return
     */
    public function deleteGroupFromContainer($groupId) {
        $param = array(
            'groupId' => $groupId
        );
        $result = $this->remoteCall("deleteGroupFromContainer", $param);
        return $result;
    }
    /**
     * 在添加用户到指定的容器中
     *
     * @param userID
     *            用户的UID
     * @param attrMap
     *            用户属性值对 Map中存放键值--数组对应值对,键值为属性名,数组为属性值数组
     * @param containerDN
     *            要添加到的容器DN
     * @return
     */
    public function addUserToPeopleContainer($userID, $attrs, $containerDN) {
        $param = array(
            'userID' => $userID,
            'attrs' => $attrs,
            'containerDN' => $containerDN
        );
        $result = $this->remoteCall("addUserToPeopleContainer", $param);
        return $result;
    }
    /**
     * 从指定的容器中删除用户
     *
     * @param userID
     *            用户的UID
     * @param containerDN
     *            容器DN
     * @return
     */
    public function deleteUserFromPeopleContainer($userID, $containerDN) {
        $param = array(
            'userID' => $userID,
            'containerDN' => $containerDN
        );
        $result = $this->remoteCall("deleteUserFromPeopleContainer", $param);
        return $result;
    }
    /**
     * 为用户注册服务
     *
     * @param userID
     *            用户UID
     * @param serviceName
     *            要注册的服务名
     * @return
     */
    public function registerServiceForUser($userID, $serviceName) {
        $param = array(
            'userID' => $userID,
            'serviceName' => $serviceName
        );
        $result = $this->remoteCall("registerServiceForUser", $param);
        return $result;
    }
}
 


 


?>



