<?php
/*
 * Copyright (c) XuTianyi 2023.
 * Email: xutianyi12@outlook.com.
 * Github: https://github.com/xxutianyi
 */

namespace PHPWeworkSDK\Api;

use GuzzleHttp\Exception\GuzzleException;
use PHPWeworkSDK\ErrorCode\ClientError;
use PHPWeworkSDK\Exception\CallException;

class CorpApi extends Api
{

    private string $corpID = "";
    private string $secret = "";

    private string|int $agentID = "";

    private string $accessToken = "";


    /**
     * @param int|string $agentID 企业微信自建应用 Agent ID 或内置应用 CONTACT,DAIL,CHECKIN,MEETING_ROOM,KF
     * @param string $corpID 企业微信企业 ID
     * @param string $secret 应用 secret 或内置功能 secret
     * @throws CallException
     */
    public function __construct(int|string $agentID, string $corpID, string $secret)
    {
        parent::__construct();

        if (empty($corpID)) {
            throw new CallException(ClientError::CROP_ID_PARAM_ERROR->name, ClientError::CROP_ID_PARAM_ERROR->value);
        }

        if (empty($secret)) {
            throw new CallException(ClientError::SECRET_PARAM_ERROR->name, ClientError::SECRET_PARAM_ERROR->value);
        }


        if (empty($agentID) || (is_string($agentID) && !in_array($agentID, $this->builtinApp))) {
            throw new CallException(ClientError::AGENT_ID_PARAM_ERROR->name, ClientError::AGENT_ID_PARAM_ERROR->value);
        }

        $this->corpID = $corpID;
        $this->secret = $secret;
        $this->agentID = $agentID;

        $this->cacheItemKey = "$this->cacheKeyPrefix.$corpID.$agentID";

        $this->accessToken = $this->getAccessToken();


    }

    protected function getAccessToken(): string
    {
        $accessToken = $this->cache->getItem($this->cacheItemKey);
        if (!$accessToken->isHit()) {
            return $this->refreshAccessToken();
        }
        return $accessToken->get();

    }

    protected function refreshAccessToken(): string
    {
        $query = [
            'corpid' => $this->corpID,
            'corpsecret' => $this->secret
        ];

        $response = $this->request('/gettoken', 'GET', $query);

        $accessToken = $response['access_token'];
        $expiresIn = $response['expires_in'];

        $accessTokenCache = $this->cache->getItem($this->cacheItemKey);
        $accessTokenCache->set($accessToken);
        $accessTokenCache->expiresAfter($expiresIn);
        $this->cache->save($accessTokenCache);

        return $accessToken;

    }

    /**
     * 生成 oauth 授权链接
     * @param string $redirectUrl
     * @param string $type
     * @param string $state
     * @return string
     * @throws CallException
     */
    public function getOAuthUrl(string $redirectUrl, string $type, string $state = ""): string
    {

        if (is_string($this->agentID)) {
            throw new CallException('Invalid AgentID');
        }

        $allowedType = [
            'snsapi_base',
            'snsapi_privateinfo'
        ];

        if (!in_array($type, $allowedType)) {
            throw new CallException('Invalid Type');
        }

        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->corpID&redirect_uri=$redirectUrl&response_type=code&scope=$type&state=$state&agentid=$this->agentID#wechat_redirect";

    }

    /**
     * oauth code 换用户信息
     * @param $code
     * @return array 返回type参数，0 外部成员，1 内部成员带敏感信息， 2 内部成员不含敏感信息
     * @throws GuzzleException
     */
    public function getUserInfoWithCode($code): array
    {
        $query = [
            'access_token' => $this->accessToken,
            'code' => $code
        ];

        $response = $this->request('/auth/getuserinfo', 'GET', $query);

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
     * @return array
     * @throws GuzzleException
     */
    public function getUserDetailWithTicket($ticket): array
    {
        $query = [
            'access_token' => $this->accessToken,
        ];

        $params = [
            'user_ticket' => $ticket
        ];

        $response = $this->request('/auth/getuserdetail', 'POST', $query, $params);

        //TODO:整理返回值

        return $response;

    }

    /**
     * 发送文本消息
     * @param string $content
     * @param array $users
     * @param array $tags
     * @param array $parties
     * @param int $safe
     * @return array
     * @throws CallException
     * @throws GuzzleException
     */
    public function sendTextMessage(string $content, array $users = [], array $tags = [], array $parties = [], int $safe = 0): array
    {
        if (is_string($this->agentID)) {
            throw new CallException('Invalid AgentID');
        }

        if (empty($users) && empty($tags) && empty($parties)) {
            throw new CallException('Users,Tags,Parties 不能同时为空！');
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

        $response = $this->request('/message/send', 'POST', $query, $params);

        //TODO:整理返回值

        return $response;

    }

}
