<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Scan µs Documentation",
 *      description="Scan µs Swagger OpenApi Description",
 *      @OA\Contact(
 *          email="developers@beitsafe.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *     url="http://192.168.5.45:8001/api/v1/scan",
 *     description="LOCAL",
 * )
 *
 * @OA\Server(
 *     url="http://scan-n-go-975358305.ap-southeast-2.elb.amazonaws.com/api/v1/scan",
 *     description="STAGING",
 * )
 *
 * @OA\SecurityScheme(
 *    securityScheme="bearerAuth",
 *    in="header",
 *    name="Bearer Token",
 *    type="http",
 *    scheme="bearer",
 *    bearerFormat="JWT",
 * )
 *
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
