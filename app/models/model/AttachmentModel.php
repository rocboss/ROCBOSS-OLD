<?php
namespace model;

/**
 * AttachmentModel
 * @author ROC <i@rocs.me>
 */
class AttachmentModel extends Model
{
    // The table name.
    const TABLE = 'rocboss_attachment';
    
    // Columns the model expects to exist
    const COLUMNS = ['id', 'user_id', 'file_size', 'img_width', 'img_height', 'type', 'content', 'created_at', 'updated_at', 'is_deleted'];

    // List of columns which have a default value or are nullable
    const OPTIONAL_COLUMNS = ['created_at'];

    // Primary Key
    const PRIMARY_KEY = ['id'];

    // List of columns to receive the current timestamp automatically
    const STAMP_COLUMNS = [
        'updated_at' => 'datetime',
    ];

    // It defines the column affected by the soft delete
    const SOFT_DELETE = 'is_deleted';
}
