<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Dashboard",
    description: "Authenticated dashboard endpoints"
)]
#[OA\Get(
    path: "/api/v1/dashboard",
    summary: "Admin dashboard welcome endpoint",
    security: [["bearerAuth" => []]],
    tags: ["Dashboard"],
    responses: [
        new OA\Response(response: 200, description: "Dashboard response"),
        new OA\Response(response: 401, description: "Unauthenticated"),
        new OA\Response(response: 403, description: "Forbidden"),
    ]
)]
class ApiDocumentation
{
}
