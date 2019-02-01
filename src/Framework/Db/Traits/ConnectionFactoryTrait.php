<?php

namespace Manadev\Framework\Db\Traits;

use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\SqlServerConnection;
use Manadev\Core\App;

trait ConnectionFactoryTrait
{
    protected function around_createConnection(callable $proceed, $driver, $connection, $database,
        $prefix = '', array $config = [])
    {
        global $m_app; /* @var App $m_app */

        if ($resolver = Connection::getResolver($driver)) {
            return $resolver($connection, $database, $prefix, $config);
        }

        switch ($driver) {
            case 'mysql':
                return $m_app->createRaw(MySqlConnection::class,
                    $connection, $database, $prefix, $config);
            case 'pgsql':
                return $m_app->createRaw(PostgresConnection::class,
                    $connection, $database, $prefix, $config);
            case 'sqlite':
                return $m_app->createRaw(SQLiteConnection::class,
                    $connection, $database, $prefix, $config);
            case 'sqlsrv':
                return $m_app->createRaw(SqlServerConnection::class,
                    $connection, $database, $prefix, $config);
        }

        throw new \InvalidArgumentException("Unsupported driver [$driver]");
    }
}