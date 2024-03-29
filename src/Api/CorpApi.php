<?php
/*
 * Copyright (c) XuTianyi 2023.
 * Email: xutianyi12@outlook.com.
 * Github: https://github.com/xxutianyi
 */

namespace PHPWeworkSDK\Api;

use GuzzleHttp\Exception\GuzzleException;
use PHPWeworkSDK\Abstract\Api;
use PHPWeworkSDK\ErrorCode\ClientError;
use PHPWeworkSDK\Exception\CallException;
use PHPWeworkSDK\Exception\RemoteException;
use PHPWeworkSDK\Model\Department;
use PHPWeworkSDK\Model\User;
use Psr\Cache\InvalidArgumentException;

class CorpApi extends Api
{

    private string $corpID = "";
    private string $secret = "";
    private string|int $agentID = "";

    /**
     * @param int|string $agentID 企业微信自建应用 Agent ID 或内置应用 CONTACT,DAIL,CHECKIN,MEETING_ROOM,KF
     * @param string $corpID 企业微信企业 ID
     * @param string $secret 应用 secret 或内置功能 secret
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     * @throws InvalidArgumentException
     */
    public function __construct(int|string $agentID, string $corpID, string $secret)
    {
        parent::__construct();

        if (empty($corpID)) {
            throw new CallException(ClientError::CROP_ID_PARAM_ERROR);
        }

        if (empty($secret)) {
            throw new CallException(ClientError::SECRET_PARAM_ERROR);
        }


        if (empty($agentID) || (is_string($agentID) && !in_array($agentID, $this->builtinApp))) {
            throw new CallException(ClientError::AGENT_ID_PARAM_ERROR);
        }

        $this->corpID = $corpID;
        $this->secret = $secret;
        $this->agentID = $agentID;

        $this->cacheItemKey = "$this->cacheKeyPrefix.$corpID.$agentID";

        $this->accessToken = $this->getAccessToken();


    }

    /**
     * 获取AccessToken, 自动维护缓存
     * @return string
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAccessToken(): string
    {
        $accessToken = $this->cache->getItem($this->cacheItemKey);
        if (!$accessToken->isHit()) {
            return $this->refreshAccessToken();
        }
        return $accessToken->get();
    }

    /**
     * 刷新 AccessToken
     * @return string
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function refreshAccessToken(): string
    {
        $query = [
            'corpid' => $this->corpID,
            'corpsecret' => $this->secret
        ];

        $response = $this->request(Endpoint::InnerGetAccessToken, 'GET', $query, [], false);

        $accessToken = $response['access_token'];
        $expiresIn = $response['expires_in'];

        $accessTokenCache = $this->cache->getItem($this->cacheItemKey);
        $accessTokenCache->set($accessToken);
        $accessTokenCache->expiresAfter($expiresIn);
        $this->cache->save($accessTokenCache);

        return $accessToken;

    }

    /*
     * ------------------------------------------------
     * 身份认证接口
     * ------------------------------------------------
     */

    /**
     * 生成 oauth 授权链接
     * @param string $redirectUrl 重定向链接
     * @param string $state 回传 state 信息
     * @param bool $private 是否获取敏感信息，默认获取
     * @return string 拼装好的链接
     * @throws CallException
     */
    public function getOAuthUrl(string $redirectUrl, string $state = "", bool $private = true): string
    {

        if (is_string($this->agentID)) {
            throw new CallException(ClientError::AGENT_ID_PARAM_ERROR);
        }

        $type = $private ? 'snsapi_privateinfo' : 'snsapi_base';

        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->corpID&redirect_uri=$redirectUrl&response_type=code&scope=$type&state=$state&agentid=$this->agentID#wechat_redirect";

    }

    /**
     * oAuth code 换用户信息
     * @param $code
     * @return array 返回type参数，0 外部成员，1 内部成员带敏感信息， 2 内部成员不含敏感信息
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    public function getUserInfoByCode($code): array
    {
        $query = [
            'code' => $code
        ];

        $response = $this->request(Endpoint::InnerGetUserInfoByCode, 'GET', $query);

        if (key_exists('openid', $response)) {
            return [
                'type' => 0,
                'open_id' => $response['openid'],
                'external_userid' => $response['external_userid'],
            ];
        }

        if (key_exists('user_ticket', $response)) {
            return [
                'type' => 1,
                'user_id' => $response['userid'],
                'user_ticket' => $response['user_ticket'],
            ];
        } else {
            return [
                'type' => 2,
                'user_id' => $response['userid'],
            ];
        }
    }

    /**
     * user_ticket 换用户敏感信息
     * @param $ticket
     * @return User
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    public function getUserDetailWithTicket($ticket): User
    {
        $params = [
            'user_ticket' => $ticket
        ];

        $response = $this->request(Endpoint::InnerGetUserPrivateInfo, 'POST', [], $params);

        return new User($response);

    }

    /*
     * ------------------------------------------------
     * 通讯录管理接口
     * ------------------------------------------------
     */

    /**
     * 创建用户
     * @param User $user
     * @return bool
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    public function createUser(User $user): bool
    {
        $this->request(Endpoint::InnerCreateUser, 'POST', [], $user->toArray());

        return true;
    }

    /**
     * 更新用户
     * @param User $user
     * @return bool
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    public function updateUser(User $user): bool
    {
        $this->request(Endpoint::InnerUpdateUser, 'POST', [], $user->toArray());

        return true;
    }

    /**
     * 删除用户
     * @param User $user
     * @return bool
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    public function deleteUser(User $user): bool
    {
        $this->request(Endpoint::InnerDeleteUser, 'GET', ['user_id' => $user->user_id]);

        return true;
    }

    /**
     * 批量删除用户
     * @param array $list
     * @return bool
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException/
     *
     */
    public function batchDeleteUser(array $list): bool
    {
        $this->request(Endpoint::InnerBatchDeleteUser, 'POST', ['useridlist' => $list]);

        return true;
    }

    /**
     * 登陆时二次验证
     * @param User $user
     * @return bool
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    public function twoStepAuthSuccess(User $user): bool
    {
        $this->request(Endpoint::InnerTwoStepAuthSuccess, 'GET', ['user_id' => $user->user_id]);

        return true;
    }

    /**
     * 手机号换 user id
     * @param $mobile
     * @return string|null
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    public function getUserIdByMobile($mobile): string|null
    {
        $response = $this->request(Endpoint::InnerGetUserIdByMobile, 'POST', [], ['mobile' => $mobile]);

        return $response['userid'];
    }

    /**
     * 邮箱换 user id
     * @param $email
     * @return string|null
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    public function getUserIdByEmail($email): string|null
    {
        $response = $this->request(Endpoint::InnerGetUserIdByEmail, 'POST', [], ['email' => $email]);

        return $response['userid'];
    }

    /**
     * 创建部门
     * @param Department $department
     * @return true
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    public function createDepartment(Department $department): bool
    {
        $this->request(Endpoint::InnerCreateDepartment, 'POST', [], $department->toArray());

        return true;
    }

    /**
     * 更新部门
     * @param Department $department
     * @return bool
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    public function updateDepartment(Department $department): bool
    {
        $this->request(Endpoint::InnerUpdateDepartment, 'POST', [], $department->toArray());

        return true;
    }

    /**
     * 删除部门
     * @param Department $department
     * @return bool
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    public function deleteDepartment(Department $department): bool
    {
        $this->request(Endpoint::InnerDeleteDepartment, 'GET', ['id' => $department->id]);

        return true;
    }

    /**
     * 获取部门ID列表
     * @param Department|null $department
     * @return array
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    public function indexDepartment(Department $department = null): array
    {
        $response = $this->request(Endpoint::InnerIndexDepartment, 'GET', $department ? ['id' => $department->id] : []);

        return $response;
    }


    /*
     * ------------------------------------------------
     * 消息发送接口
     * ------------------------------------------------
     */

    /**
     * 发送文本消息
     * @param string $content
     * @param array $users
     * @param array $tags
     * @param array $parties
     * @param int $safe
     * @return array
     * @throws CallException
     * @throws GuzzleException|RemoteException
     */
    public function sendTextMessage(string $content, array $users = [], array $tags = [], array $parties = [], int $safe = 0): array
    {
        if (is_string($this->agentID)) {
            throw new CallException(ClientError::AGENT_ID_PARAM_ERROR);
        }

        if (empty($users) && empty($tags) && empty($parties)) {
            throw new CallException(ClientError::API_PARAMS_ERROR);
        }

        $users = implode("|", $users);
        $tags = implode("|", $tags);
        $parties = implode("|", $parties);

        $query = [
            'access_token' => $this->accessToken,
        ];

        $params = [
            'touser' => $users,
            'toparty' => $parties,
            'totag' => $tags,
            'msgtype' => 'text',
            'agentid' => $this->agentID,
            'text' => [
                'content' => $content
            ],
            'safe' => $safe,
        ];

        $response = $this->request(Endpoint::InnerSendMessage, 'POST', $query, $params);

        //TODO:整理返回值

        return $response;

    }

}
