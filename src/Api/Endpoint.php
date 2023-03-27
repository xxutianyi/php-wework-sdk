<?php

namespace PHPWeworkSDK\Api;

enum Endpoint: string
{
    case InnerGetAccessToken = '/gettoken';
    case InnerGetUserInfoWithCode = '/auth/getuserinfo';
    case InnerGetUserPrivateInfo = '/auth/getuserdetail';

    case InnerSendMessage = '/message/send';
}
