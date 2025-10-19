<?php
/**
 * Copyright © NativeLang. All rights reserved.
 */
declare(strict_types=1);

namespace NativeLang\Translation\Plugin;

use Magento\Framework\App\Response\Http as HttpResponse;

/**
 * CORS headers plugin for translation admin
 */
class CorsPlugin
{
    /**
     * Add CORS headers to response
     *
     * @param HttpResponse $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterSendResponse(HttpResponse $subject, $result)
    {
        // Add CORS headers
        $subject->setHeader('Access-Control-Allow-Origin', '*', true);
        $subject->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS', true);
        $subject->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With', true);
        $subject->setHeader('Access-Control-Max-Age', '3600', true);

        return $result;
    }
}

