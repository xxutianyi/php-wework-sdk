<?php
/*
 * Copyright (c) XuTianyi 2023.
 * Email: xutianyi12@outlook.com.
 * Github: https://github.com/xxutianyi
 */

namespace PHPWeworkSDK\ErrorCode;

enum ClientError: int
{
    case API_PARAMS_ERROR = 4220;
    case CROP_ID_PARAM_ERROR = 4221;
    case SECRET_PARAM_ERROR = 4222;
    case AGENT_ID_PARAM_ERROR = 4223;
    case PROVIDER_SECRET_PARAM_ERROR = 4224;
    case PERMANENT_CODE_PARAM_ERROR = 4225;
    case SUITE_ID_PARAM_ERROR = 4226;
    case SUITE_SECRET_PARAM_ERROR = 4227;
    case SUITE_TICKET_PARAM_ERROR = 4228;


}
