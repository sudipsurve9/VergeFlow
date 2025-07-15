<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use App\Services\DatabaseService;

trait HasClientDatabase
{
    /**
     * Get the database connection for the model.
     */
    public function getConnectionName()
    {
        // If we're in a client context, use client database
        if (session('current_client_id')) {
            $client = \App\Models\Client::find(session('current_client_id'));
            if ($client && $client->database_name) {
                return $this->getClientConnection($client);
            }
        }
        
        // Default to main database
        return parent::getConnectionName();
    }
    
    /**
     * Get client database connection
     */
    private function getClientConnection($client)
    {
        $connectionName = 'client_' . $client->id;
        
        if (!config("database.connections.{$connectionName}")) {
            $databaseService = new DatabaseService();
            $databaseService->createClientConnection($client, $connectionName);
        }
        
        return $connectionName;
    }
    
    /**
     * Scope to use client database
     */
    public function scopeForClient($query, $client)
    {
        if ($client && $client->database_name) {
            $connectionName = $this->getClientConnection($client);
            $query->on($connectionName);
        }
        
        return $query;
    }
    
    /**
     * Create model on client database
     */
    public static function createForClient($attributes, $client)
    {
        $model = new static($attributes);
        
        if ($client && $client->database_name) {
            $connectionName = $model->getClientConnection($client);
            $model->setConnection($connectionName);
        }
        
        $model->save();
        return $model;
    }
} 