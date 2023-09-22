<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalModel extends Model
{
    // Set Variable Data
    private $curTimestamp;

    // Constructor
    public function __construct()
    {
        $this->curTimestamp = date("Y-m-d H:i:s");
    }

    // Set status
    public function setActive($id = null, $status = false)
    {
        if (!is_null($id)) {
            try {
                $this->where(['id' => $id])->update([
                    'status' => $status,
                ]);

                // return boolean only
                return true;
            } catch (\Exception $e) {
                \Log::error(json_encode($e));
            }
        }

        return;
    }

    // Add record global 
    public function add($data = [])
    {
        try {
            $data = is_object($data) ? (array) $data : (is_array($data) ? $data : []);
            // looping insert by app side for 1 row transaction
            foreach ($data as $field => $value) {
                if (in_array($field, $this->fillable)) {
                    $this->{$field} = $value;
                }
            }
            // dd($this);
            if ($sql = $this->save()) {
                // this will return id
                return $this->id;
            }
        } catch (\Exception $e) {
            \Log::error(json_encode($e));
        }

        return;
    }

    // Edit record global 
    public function edit($data = [], $id = null)
    {
        try {
            if (!is_null($id)) {
                // find current record first then update data
                $sql = $this->find($id);

                // validation for record are exist or not
                if ($sql) {
                    $data = is_object($data) ? (array) $data : (is_array($data) ? $data : []);
                    // looping insert by app side for 1 row transaction
                    foreach ($data as $field => $value) {
                        if (!in_array($field, $this->fillable) || $field == 'id') {
                            unset($data[$field]);
                        }
                    }
                    if ($sql->update($data)) {
                        // this will return id
                        return $sql;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error(json_encode($e));
        }

        return;
    }

    // Soft delete by update data 
    public function del($id = null)
    {
        if (!is_null($id)) {
            try {
                $this->where(['id' => $id])->update([
                    'deleted_by' => 1,
                    'deleted_at' => $this->curTimestamp
                ]);

                // return boolean only
                return true;
            } catch (\Exception $e) {
                \Log::error(json_encode($e));
            }
        }

        return;
    }
}
