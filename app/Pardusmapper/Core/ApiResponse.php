<?php
declare(strict_types=1);

namespace Pardusmapper\Core;

class ApiResponse
{

    /**
     * HTTP response code constant
     */
    final public const int OK            = 200,
        CREATED                      = 201,
        NOCONTENT                    = 204,
        MOVEDPERMANENTLY             = 301,
        FOUND                        = 302,
        SEEOTHER                     = 303,
        NOTMODIFIED                  = 304,
        TEMPORARYREDIRECT            = 307,
        BADREQUEST                   = 400,
        UNAUTHORIZED                 = 401,
        FORBIDDEN                    = 403,
        NOTFOUND                     = 404,
        METHODNOTALLOWED             = 405,
        NOTACCEPTABLE                = 406,
        GONE                         = 410,
        LENGTHREQUIRED               = 411,
        PRECONDITIONFAILED           = 412,
        REQUESTENTITYTOOLARGE        = 413,
        REQUESTEDRANGENOTSATISFIABLE = 416,
        UNSUPPORTEDMEDIATYPE         = 415,
        INTERNALSERVERERROR          = 500,
        NOTIMPLEMENTED               = 501,
        SERVICE_UNAVAILABLE          = 503,
        GATEWAY_TIMEDOUT             = 504;
}
