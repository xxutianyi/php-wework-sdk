<?php

namespace PHPWeworkSDK\Api;

enum Endpoint: string
{
    case InnerGetAccessToken = '/gettoken';
    case InnerGetUserInfoByCode = '/auth/getuserinfo';
    case InnerGetUserPrivateInfo = '/auth/getuserdetail';


    case InnerCreateUser = '/user/create';
    case InnerUpdateUser = '/user/update';
    case InnerDeleteUser = '/user/delete';
    case InnerBatchDeleteUser = '/user/batchdelete';
    case InnerTwoStepAuthSuccess = '/user/authsucc';
    case InnerGetUserIdByMobile = '/user/getuserid';
    case InnerGetUserIdByEmail = '/user/get_userid_by_email';
    case InnerSendMessage = '/message/send';
}
