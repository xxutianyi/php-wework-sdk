<?php

namespace PHPWeworkSDK\Api;

enum Endpoint: string
{
    case InnerGetAccessToken = '/gettoken';
    case InnerGetUserInfoWithCode = '/auth/getuserinfo';
    case InnerGetUserPrivateInfo = '/auth/getuserdetail';


    case InnerCreateUser = '/user/create';
    case InnerUpdateUser = '/user/update';
    case InnerDeleteUser = '/user/delete';

    case InnerSendMessage = '/message/send';
}
