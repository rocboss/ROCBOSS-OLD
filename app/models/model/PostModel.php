<?php
namespace model;

/**
 * PostModel
 * @author ROC <i@rocs.me>
 */
class PostModel extends Model
{
    // The table name.
    const TABLE = 'rocboss_post';
    
    // Columns the model expects to exist
    const COLUMNS = ['id', 'alias_id', 'group_id', 'user_id', 'type', 'comment_count', 'collection_count', 'upvote_count', 'created_at', 'updated_at', 'is_deleted'];

    // List of columns which have a default value or are nullable
    const OPTIONAL_COLUMNS = ['comment_count', 'collection_count', 'upvote_count', 'created_at'];

    // Primary Key
    const PRIMARY_KEY = ['id'];

    // List of columns to receive the current timestamp automatically
    const STAMP_COLUMNS = [
        'updated_at' => 'datetime',
    ];

    // It defines the column affected by the soft delete
    const SOFT_DELETE = 'is_deleted';
}
