<?php

namespace PHPWeworkSDK\Api;

enum Endpoint: string
{
    /** 身份认证 */
    case InnerGetAccessToken = '/gettoken';
    case InnerGetUserInfoByCode = '/auth/getuserinfo';
    case InnerGetUserPrivateInfo = '/auth/getuserdetail';

    /** 通讯录管理 */
    case InnerCreateUser = '/user/create';
    case InnerUpdateUser = '/user/update';
    case InnerDeleteUser = '/user/delete';
    case InnerBatchDeleteUser = '/user/batchdelete';
    case InnerTwoStepAuthSuccess = '/user/authsucc';
    case InnerGetUserIdByMobile = '/user/getuserid';
    case InnerGetUserIdByEmail = '/user/get_userid_by_email';
    case InnerCreateDepartment = '/department/create';
    case InnerUpdateDepartment = '/department/update';
    case InnerDeleteDepartment = '/department/delete';
    case InnerIndexDepartment = '/department/simplelist';

    /** 发送消息 */
    case InnerSendMessage = '/message/send';
}
