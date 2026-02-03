<?php

namespace App\Services;

/**
 * Core PHP IP Validation Service
 * Handles IP address validation and retrieval using pure PHP
 */
class IpValidationService
{
    /**
     * Validate and sanitize IP address (Core PHP)
     *
     * @param string $ipAddress
     * @return array
     */
    public function validateIp($ipAddress)
    {
        // IPv4 validation
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return [
                'valid' => true,
                'type' => 'IPv4',
                'ip' => $ipAddress
            ];
        }

        // IPv6 validation
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return [
                'valid' => true,
                'type' => 'IPv6',
                'ip' => $ipAddress
            ];
        }

        return [
            'valid' => false,
            'error' => 'Invalid IP address format'
        ];
    }

    /**
     * Get client IP address (handles proxies) - Core PHP
     *
     * @return string
     */
    public function getClientIp()
    {
        $ipAddress = '';

        // Check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        }
        // Check for IPs passing through proxies
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Handle multiple IPs (proxy chain)
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ipAddress = trim($ips[0]);
        }
        // Check for Cloudflare
        elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ipAddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        // Default remote address
        else {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }

        return $ipAddress;
    }

    /**
     * Sanitize IP address
     *
     * @param string $ipAddress
     * @return string|false
     */
    public function sanitizeIp($ipAddress)
    {
        return filter_var($ipAddress, FILTER_VALIDATE_IP);
    }

    /**
     * Check if IP is in private range
     *
     * @param string $ipAddress
     * @return bool
     */
    public function isPrivateIp($ipAddress)
    {
        return !filter_var(
            $ipAddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * Get IP version (4 or 6)
     *
     * @param string $ipAddress
     * @return int|null
     */
    public function getIpVersion($ipAddress)
    {
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return 4;
        } elseif (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return 6;
        }

        return null;
    }
}
