<?php
/*
 * Copyright (c) XuTianyi 2023.
 * Email: xutianyi12@outlook.com.
 * Github: https://github.com/xxutianyi
 */

namespace PHPWeworkSDK\ErrorCode;

enum ClientError: int
{
    /**
     * 参数错误-调用错误
     */
    case API_PARAMS_ERROR = 42200;
    case CROP_ID_PARAM_ERROR = 42201;
    case SECRET_PARAM_ERROR = 42202;
    case AGENT_ID_PARAM_ERROR = 42203;
    case PROVIDER_SECRET_PARAM_ERROR = 42204;
    case PERMANENT_CODE_PARAM_ERROR = 42205;
    case SUITE_ID_PARAM_ERROR = 42206;
    case SUITE_SECRET_PARAM_ERROR = 42207;
    case SUITE_TICKET_PARAM_ERROR = 42208;


    /**
     * 参数错误-模型错误
     */
    case MODEL_TO_MANY_ATTRIBUTES = 42211;
    case MODEL_TO_FEW_ATTRIBUTES = 42212;

}
