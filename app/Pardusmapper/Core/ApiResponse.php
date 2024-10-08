<?php
declare(strict_types=1);

namespace Pardusmapper\Core;

class ApiResponse
{
    /**
     * HTTP response code constants
     */
    public const OK                        = 200;
    public const CREATED                   = 201;
    public const NOCONTENT                 = 204;
    public const MOVEDPERMANENTLY          = 301;
    public const FOUND                     = 302;
    public const SEEOTHER                  = 303;
    public const NOTMODIFIED               = 304;
    public const TEMPORARYREDIRECT         = 307;
    public const BADREQUEST                = 400;
    public const UNAUTHORIZED              = 401;
    public const FORBIDDEN                 = 403;
    public const NOTFOUND                  = 404;
    public const METHODNOTALLOWED          = 405;
    public const NOTACCEPTABLE             = 406;
    public const GONE                      = 410;
    public const LENGTHREQUIRED            = 411;
    public const PRECONDITIONFAILED        = 412;
    public const REQUESTENTITYTOOLARGE     = 413;
    public const REQUESTEDRANGENOTSATISFIABLE = 416;
    public const UNSUPPORTEDMEDIATYPE      = 415;
    public const INTERNALSERVERERROR       = 500;
    public const NOTIMPLEMENTED            = 501;
    public const SERVICE_UNAVAILABLE       = 503;
    public const GATEWAY_TIMEDOUT          = 504;
}