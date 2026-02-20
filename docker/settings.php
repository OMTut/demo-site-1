<?php

/**
 * @file
 * Production Drupal settings for the community-bloom k3s deployment.
 *
 * All sensitive values are read from environment variables injected by
 * Kubernetes Secrets (see apps/staging/community-bloom/community-bloom-secret.yaml
 * in the lab repo).
 */

// ---------------------------------------------------------------------------
// Database
// ---------------------------------------------------------------------------
$databases['default']['default'] = [
  'driver'    => 'mysql',
  'database'  => getenv('DRUPAL_DB_NAME'),
  'username'  => getenv('DRUPAL_DB_USER'),
  'password'  => getenv('DRUPAL_DB_PASSWORD'),
  'host'      => getenv('DRUPAL_DB_HOST') ?: 'mariadb',
  'port'      => '3306',
  'prefix'    => '',
  'collation' => 'utf8mb4_general_ci',
  'namespace' => 'Drupal\\mysql\\Driver\\Database\\mysql',
  'autoload'  => 'core/modules/mysql/src/Driver/Database/mysql/',
];

// ---------------------------------------------------------------------------
// Security
// ---------------------------------------------------------------------------
$settings['hash_salt'] = getenv('DRUPAL_HASH_SALT');

$settings['trusted_host_patterns'] = [
  '^community\-bloom\.hondo\-dev\.com$',
  '^community\-bloom\.wc$',
];

// ---------------------------------------------------------------------------
// File paths
// ---------------------------------------------------------------------------
$settings['config_sync_directory'] = '../config/sync';
$settings['file_public_path'] = 'sites/default/files';

// ---------------------------------------------------------------------------
// Performance
// ---------------------------------------------------------------------------
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';

// Aggregation is handled at the CDN/Cloudflare layer; keep CSS/JS aggregation
// enabled to reduce requests.
$config['system.performance']['css']['preprocess'] = TRUE;
$config['system.performance']['js']['preprocess'] = TRUE;

// ---------------------------------------------------------------------------
// Reverse proxy / Cloudflare
// ---------------------------------------------------------------------------
$settings['reverse_proxy'] = TRUE;
$settings['reverse_proxy_addresses'] = ['127.0.0.1'];
